#!/usr/bin/env python3
import argparse
import subprocess
import requests
from requests.exceptions import HTTPError
from simplejson.errors import JSONDecodeError
import os
import pwd
import re
import random
import string
from nbgrader.api import Gradebook

JUPYTER_ADMIN = 'profnsi'
ANNEE = 2021

deps = [
'apt update',
'apt upgrade -o Dpkg::Options::="--force-confold" --force-yes -y',
'apt install -y npm',
'npm install -g configurable-http-proxy',
'apt install -y python3-pip',
'pip3 install -U jupyter',
'pip3 install -U jupyterhub',
'pip3 install nbgrader',
'pip3 install metakernel',  # Pour avoir pythontutor avec %%tutor
# configuration de adduser pour autoriser un . dans le nom
'sed -E -i "s/.*NAME_REGEX=.*/NAME_REGEX=\"\^\[a-z\]\[-a-z0-9_.\]\*\[a-z0-9_\]$\"/" /etc/adduser.conf',
# et regrouper les utilisateurs par lettre
# 'sed -E -i "s/.*LETTERHOMES=.*/LETTERHOMES=yes/" /etc/adduser.conf',
]

srv_root="/srv/nbgrader"
nbgrader_root="/srv/nbgrader/nbgrader"
jupyterhub_root="/srv/nbgrader/jupyterhub"
exchange_root="/srv/nbgrader/exchange"
jh_config_file = os.path.join(jupyterhub_root,'jupyterhub_config.py')



# global nbgrader config
nbgrader_global_config = """from nbgrader.auth import JupyterHubAuthPlugin
c = get_config()
c.Exchange.path_includes_course = True
c.Authenticator.plugin_class = JupyterHubAuthPlugin
c.IncludeHeaderFooter.header = "/usr/share/jupyter/header.ipynb"
c.IncludeHeaderFooter.footer = "/usr/share/jupyter/footer.ipynb"
c.ClearSolutions.code_stub = {'python': '# TAPEZ VOTRE CODE ICI', 'matlab': "% YOUR CODE HERE\\nerror('No Answer Given!')", 'octave': "% YOUR CODE HERE\\nerror('No Answer Given!')", 'java': '// YOUR CODE HERE'}
c.ClearSolutions.text_stub = 'VOTRE REPONSE'
"""

# Basic jupyterhub config
jupyterhub_config = f"""c = get_config()
c.LocalAuthenticator.create_system_users = True
## Grant admin users permission to access single-user servers.
c.JupyterHub.admin_access = True
c.JupyterHub.bind_url = 'https://:8888/jupyter'
c.JupyterHub.cookie_secret_file = '/srv/nbgrader/jupyterhub/cookie_secret'
c.JupyterHub.ssl_cert = '/srv/nbgrader/jupyterhub/jupyterhub.crt'
c.JupyterHub.ssl_key = '/srv/nbgrader/jupyterhub/jupyterhub.key'
c.Authenticator.admin_users = set()
c.JupyterHub.load_groups = dict()
c.JupyterHub.services = []
c.Authenticator.admin_users.add('{JUPYTER_ADMIN}')
### End of basic config
########################
"""

# Jupyterhub service
jh_service="""[Unit]
Description=Jupyterhub
After=syslog.target network.target

[Service]
User=root
Environment="PATH=/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin"
ExecStart=/usr/local/bin/jupyterhub -f /srv/nbgrader/jupyterhub/jupyterhub_config.py
WorkingDirectory=/srv/nbgrader/jupyterhub/
StandardOutput=file:/var/log/jupyterhub.log
StandardError=file:/var/log/jupyterhub-error.log

[Install]
WantedBy=multi-user.target
"""

course_config_base="""c = get_config()
c.CourseDirectory.root = '{home}/{course}'
c.CourseDirectory.course_id = '{course}'
"""

# configuration des notebook
ipython_config="""c = get_config()
startup = [
   'from metakernel import register_ipython_magics',
   'register_ipython_magics()',
]
c.InteractiveShellApp.exec_lines = startup
"""


class CourseAlreadyExists(Exception):
    def __init__(self, *args):
        if args:
            self.message = args[0]
        else:
            self.message = None

    def __str__(self):
        if self.message:
            return 'Course {0} already exists'.format(self.message)
        else:
            return 'Course already exists'


class CourseDoesNotExist(Exception):
    def __init__(self, *args):
        if args:
            self.message = args[0]
        else:
            self.message = None

    def __str__(self):
        if self.message:
            return "Course {0} does not exist".format(self.message)
        else:
            return "Course does not exist"


class MalformedCsvFile(Exception):
    def __init__(self, *args):
        if args:
            self.message = args[0]
        else:
            self.message = None

    def __str__(self):
        if self.message:
            return 'Wrong csv headers : {}'.format(self.message)
        else:
            return 'Wrong csv headers'


class MissingParameter(Exception):
    def __init__(self, *args):
        if args:
            self.message = args[0]
        else:
            self.message = None

    def __str__(self):
        if self.message:
            return 'Missing parameter : {}'.format(self.message)
        else:
            return 'Missing parameter'


def randomString(stringLength=10):
    """Generate a random string of fixed length """
    letters = string.ascii_lowercase
    return ''.join(random.choice(letters) for i in range(stringLength))


def get_token_for_user(user):
    with subprocess.Popen(['jupyterhub','token',user], stdout=subprocess.PIPE, encoding='utf-8', cwd=jupyterhub_root) as proc:
        token=proc.stdout.read().rstrip()
    return token


def get_home_dir(user):
    """Renvoie le répertoire home de l'utilisateur."""
    usr = pwd.getpwnam(user)
    return usr.pw_dir


def call_api(method, path, datas=None):
    dispatcher = {
        'get': requests.get,
        'post': requests.post,
        'delete': requests.delete,
        'patch': requests.patch
    }
    try:
        func=dispatcher[method]
    except KeyError:
        raise ValueError('invalid method')

    api_url = 'http://127.0.0.1:8081/jupyter/hub/api'
    token = get_token_for_user(JUPYTER_ADMIN)
    r = func(os.path.join(api_url,path),
        headers={
                 'Authorization': 'token {}'.format(token),
                },
        json=datas
    )
    r.raise_for_status()
    try:
        return r.json()
    except JSONDecodeError:
        pass


def get_service_repr(course, grader, port, token):
    service = {
          'name': course,
          'url': 'http://127.0.0.1:{}'.format(port),
          'command': [
              'jupyterhub-singleuser',
              '--group=formgrade-{}'.format(course),
              '--debug',
          ],
          'user': grader,
          'cwd': get_home_dir(grader),
          'api_token': '{}'.format(token),
    }
    return repr(service)


def get_course_config(home, course):
    return course_config_base.format(home=home, course=course)


def get_next_port():
    used_ports = set()
    with open(jh_config_file, 'r') as cfg:
        for line in cfg:
            if not 'c.JupyterHub.services.append' in line:
                continue
            d = line[line.index('(')+1:line.index(')')]
            assert d[0] == '{' and d[-1] == '}', f"Problème dans le fichier {jh_config_file}, ligne :\n    {line}\n    {d}"
            d = eval(d)
            port = int(d['url'].split(':')[2])
            used_ports.add(port)
    port = 9999
    while port in used_ports:
        port -= 1
    return port


def toggle_nbgrader_component(user, component, enable=True):
    if component not in ['create_assignment','formgrader','assignment_list','course_list']:
        raise KeyError
    home = get_home_dir(user)
    if enable:
        action = 'enable'
    else:
        action = 'disable'
    command = [ 'sudo','-u',user,
                'jupyter','nbextension', action,
                '--user',"{}/main".format(component)]
    if component != 'create_assignment':
        command.append('--section=tree')
    subprocess.run(command, env={'HOME':home,'USER':user})
    if component != 'create_assignment':
        subprocess.run(['sudo','-u',user,
                        'jupyter','serverextension',action,
                        '--user',"nbgrader.server_extensions.{}".format(component)],
                        env={'HOME':home,'USER':user})

def add_system_user(user, password, grader=False, course=None):
    """add user to system if necessary"""
    try:
        pwd.getpwnam(user)
    except KeyError:
        if not password:
            raise MissingParameter('--password')
        assert grader or course is not None, "Il faut un groupe (grader ou course)"
        group = "grader" if grader else f"eleves_{course}_{ANNEE}"
        os.system(f"""adduser --disabled-password --gecos "" --ingroup {group} {user}""")
        with subprocess.Popen(['passwd',user], stdin=subprocess.PIPE, encoding='utf-8') as proc:
            proc.stdin.write('{}\n'.format(password))
            proc.stdin.write('{}\n'.format(password))
    else:
        print(f"system user {user} allready exists")

def del_system_user(user):
    print(f"Deleting {user} from system")
    os.system(f"deluser --remove-home {user}")

def add_jupyter_user(user):
    print(f"add {user} as jupyter user")
    try:
        call_api('post', f'users/{user}')
    except HTTPError as e:
        if e.response.status_code == 409:
            print(f"jupyter user {user} allready exists")
        else:
            raise

def add_nbgrader_user(user, first_name, last_name, course):
    print(f"add {user} ({last_name}, {first_name}) to course {course}")
    home = get_home_dir(f"grader-{course}")
    course_dir = f"{home}/{course}"
    gradebook = Gradebook(f"sqlite:///{course_dir}/gradebook.db", course_id=course)
    gradebook.update_or_create_student(user, first_name=first_name, last_name=last_name)
    gradebook.close()
#    os.system(f"""nbgrader db student add {user} """
#              f"""--last-name="{last_name}" """
#              f"""--first-name="{first_name}" """
#              f"""--course-dir={course_dir} """
##              f"""--CourseDirectory.course_id={course} """
#              )

def del_nbgrader_user(user, course):
    print(f"del {user} from course {course}")
    home = get_home_dir(f"grader-{course}")
    course_dir = f"{home}/{course}"
    gradebook = Gradebook(f"sqlite:///{course_dir}/gradebook.db", course_id=course)
    gradebook.remove_student(user)
    gradebook.close()
    for subdir in ("autograded", "feedback", "submitted"):
        os.system(f"rm -rf {course_dir}/{subdir}/{user}")
#    os.system(f"""nbgrader db student remove {user} """
#              f"""--course-dir={course_dir} """
#              f"""--force """
#              )

def del_jupyter_user(user):
    print(f"delete jupyter user {user}")
    try:
        call_api('delete', f'users/{user}')
    except HTTPError as e:
        if e.response.status_code == 404:
            print(f"jupyter user {user} does not exist")
        else:
            raise

def add_jupyter_admin(name):
    print(f"set user {name} as admin")
    call_api('patch', f'users/{name}', datas={'admin':True})

def add_jupyter_group(group):
    print(f"add jupyter group {group}")
    try:
        call_api('post', f'groups/{group}')
    except HTTPError as e:
        if e.response.status_code == 404:
            print(f"jupyter group {group} allready exists")
        else:
            raise

def del_jupyter_group(group):
    print(f"delete jupyter group {group}")
    try:
        call_api('delete', f'groups/{group}')
    except HTTPError as e:
        if e.response.status_code == 404:
            print(f"jupyter group {group} does not exist")
        else:
            raise

def add_user_group(user, group):
    print(f"add user {user} to group {group}")
    call_api('post', f"groups/{group}/users", datas={'users':[user]})

### For course management
#########################

def check_course_exists(course):
    # check if group exists
    try:
        return call_api('get', 'services/{}'.format(course))
    except HTTPError as e:
        if e.response.status_code == 404:
            raise CourseDoesNotExist(course)
        # Whatever, we raise
        raise

def add_course(args):
    course = args.course_name
    grader_account = "grader-{}".format(course)
    # check if course exists in config
    with open(jh_config_file, 'r') as f :
        if grader_account in f.read():
            raise CourseAlreadyExists('{}'.format(course))
    admin_token = get_token_for_user(JUPYTER_ADMIN)
    port = get_next_port()
    print(f"setting up service with token {admin_token} on port {port} for course {course}")
    print("---------------------------------------------------------")
    add_system_user(grader_account, randomString(), grader=True)
    add_jupyter_user(grader_account)
    # need admin rights to add system users
    add_jupyter_admin(grader_account)
    add_jupyter_group(f"formgrade-{course}")
    add_user_group(grader_account, f"formgrade-{course}")
    # empty students group
    add_jupyter_group(f"nbgrader-{course}")

    toggle_nbgrader_component(grader_account, 'formgrader')
    toggle_nbgrader_component(grader_account, 'create_assignment')

    with open(jh_config_file,'a') as f:
        # Append service
        service_string = get_service_repr(course,grader_account,port,admin_token)
        f.write("c.JupyterHub.services.append({})\n".format(service_string))
    # create course directory and .jupyter/nbgrader_config.py
    user = pwd.getpwnam(grader_account)
    home = user.pw_dir
    uid = user.pw_uid
    gid = user.pw_gid
    course_dir = os.path.join(home, course)
    os.makedirs(course_dir, exist_ok=True)
    os.chown(course_dir, uid, gid)
    home_jupyter_dir = os.path.join(home, '.jupyter')
    os.makedirs(home_jupyter_dir, exist_ok=True)
    os.chown(home_jupyter_dir, uid, gid)
    with open(os.path.join(home_jupyter_dir,'nbgrader_config.py'),'w') as f:
        f.write(get_course_config(home, course))
    os.system('systemctl restart jupyterhub')


def del_course(args):
    course = args.course_name
    check_course_exists(course)
    print(f"Deleting course {course}")
    print("------------------------------------")
    grader_account = "grader-{}".format(course)
    del_jupyter_user(grader_account)
    del_jupyter_group(f"formgrade-{course}")
    students = call_api('get', f'groups/nbgrader-{course}')['users']
    for student in students:
        del_jupyter_user(student)
    del_jupyter_group(f"nbgrader-{course}")

    os.system(f"""sed -i -e "/c.JupyterHub.services.append({{'name': '{course}'/d" {jh_config_file}""")
    os.system('systemctl restart jupyterhub')
    for student in students:
        del_system_user(student)
    del_system_user(grader_account)


def add_teacher(args):
    teacher = args.teacher_name
    password = args.password
    course = args.course_name
    check_course_exists(course)
    print(f"- Adding teacher {teacher} to course : {course}")
    print('------------------------------------')
    add_system_user(teacher, password, grader=True)
    add_jupyter_user(teacher)
    add_user_group(teacher, f"formgrade-{course}")
    add_jupyter_admin(teacher)
    toggle_nbgrader_component(teacher, 'assignment_list')
    toggle_nbgrader_component(teacher, 'course_list')

def add_student(args):
    student = args.student_name
    first_name = args.first_name
    last_name = args.last_name
    password = args.password
    course = args.course_name
    check_course_exists(course)
    print(f"- Adding student {student} to course : {course}")
    print("------------------------------")
    add_system_user(student, password, course=course)
    add_jupyter_user(student)
    add_user_group(student, f"nbgrader-{course}")
    toggle_nbgrader_component(student, 'assignment_list')
    add_nbgrader_user(student, first_name, last_name, course)

def del_user(args):
    user = args.user_name
    course = args.course_name
    print(f"- Deleting user {user}")
    print("------------------------------")
    del_jupyter_user(user)
    if course:
        del_nbgrader_user(user, course)
    del_system_user(user)

def import_students(args):
    student_parser = args.student_parser
    if not args.file:
        args.file = 'www/site_nsi/data/admin/students.csv'
    print("- Importing students from file {}".format(args.file))
    print("---------------------------------------------")
    with open(args.file) as f:
        data_line = f.readline()
        while data_line: # TODO empty or malformed lines
            datas = [d.rstrip() for d in re.split(',|;', data_line)]
            course = datas[3]
            try:
                check_course_exists(course)
            except CourseDoesNotExist:
                add_course(args.course_parser.parse_args([course]))
            ns = student_parser.parse_args([
                datas[2], # id
                course,
                "--last_name={}".format(datas[0]),
                "--first_name={}".format(datas[1]),
#                "--email={}".format(datas[3]),
#                "--lms-user-id={}".format(datas[4]),
                "--password={}".format(datas[4]),
                ])
            add_student(ns)
            data_line = f.readline()

def install_all(args):
    print('- Installing jupyterhub and nbgrader with service : {}'.format(args.systemd))
    print('----------------------------------------------------')
    for dep in deps:
        os.system(dep)
    os.makedirs(srv_root, exist_ok=True)
    os.chmod(srv_root, os.stat(srv_root).st_mode | 0o444)
    os.makedirs(nbgrader_root, exist_ok=True)
    os.chmod(nbgrader_root, os.stat(nbgrader_root).st_mode | 0o444)
    os.makedirs(jupyterhub_root, exist_ok=True)
    #os.chmod(jupyterhub_root, os.stat(jupyterhub_root).st_mode | 0o444)
    os.chmod(jupyterhub_root, 0o600)
    with open(jh_config_file, "w") as f:
        f.write(jupyterhub_config)
    curdir = os.getcwd()
    os.chdir(jupyterhub_root)
    os.system('openssl rand -base64 2048 > cookie_secret')
    os.chmod(os.path.join(jupyterhub_root, 'cookie_secret'), 0o600)
    os.system('openssl req -x509 -nodes -days 30 -newkey rsa:4096 -keyout jupyterhub.key -out jupyterhub.crt')
    os.chmod(os.path.join(jupyterhub_root, 'jupyterhub.key'), 0o600)
    os.chmod(os.path.join(jupyterhub_root, 'jupyterhub.crt'), 0o600)

    #os.chdir(nbgrader_root)
    #os.system('git clone https://github.com/Lapin-Blanc/nbgrader .')
    #os.system('git checkout create-users-on-demand')
    #os.system('pip3 install -U -r requirements.txt -e .')
    os.chdir(curdir)

    os.system('jupyter nbextension install --symlink --sys-prefix --py nbgrader --overwrite')

    os.system('jupyter nbextension disable --sys-prefix --py nbgrader')
    os.system('jupyter serverextension disable --sys-prefix --py nbgrader')

    os.system('jupyter nbextension enable --sys-prefix validate_assignment/main --section=notebook')
    os.system('jupyter serverextension enable --sys-prefix nbgrader.server_extensions.validate_assignment')

    if os.path.isdir(exchange_root):
        os.rmdir(exchange_root)

    os.makedirs(exchange_root)
    os.chmod(exchange_root,0o777)

    os.system('addgroup grader')

    home = get_home_dir(JUPYTER_ADMIN)
    try:
        os.symlink(src=f"{home}/install/header.ipynb",
                   dst="/usr/share/jupyter/header.ipynb")
        os.symlink(src=f"{home}/install/footer.ipynb",
                   dst="/usr/share/jupyter/footer.ipynb")
    except FileExistsError:
        pass

    os.makedirs('/etc/jupyter/', exist_ok=True)
    with open('/etc/jupyter/nbgrader_config.py', "w") as f:
        f.write(nbgrader_global_config)

    os.makedirs("/etc/ipython/", exist_ok=True)
    with open("/etc/ipython/ipython_config.py", "w") as f:
        f.write(ipython_config)

    with open("/etc/systemd/system/jupyterhub.service","w") as f:
        f.write(jh_service)
    os.system('systemctl start jupyterhub')
    os.system('systemctl enable jupyterhub')


def main():
    parser = argparse.ArgumentParser()

    subparsers = parser.add_subparsers(dest='command')
    subparsers.required = True
    # create the parser for the "install" command
    parser_install = subparsers.add_parser('install', help='install jupyterhub and nbgrader from scratch')
    parser_install.add_argument('-s','--systemd', action='store_true', help='also install startup script')
    parser_install.set_defaults(func=install_all)

    # create the parser for the "add" command
    parser_add = subparsers.add_parser('add', help='add a course, a teacher or a student')

    subparsers_add = parser_add.add_subparsers(dest='element')
    subparsers_add.required = True
    # ADD COURSE
    parser_add_course = subparsers_add.add_parser('course', help='add course help')
    parser_add_course.add_argument('course_name', help='the name of the course to add')
    parser_add_course.set_defaults(func=add_course)

    # ADD TEACHER TO COURSE
    parser_add_teacher = subparsers_add.add_parser('teacher', help='add teacher to existing course')
    parser_add_teacher.add_argument('teacher_name', help='the username of the teacher to add')
    parser_add_teacher.add_argument('course_name', help='the name of the course')
    parser_add_teacher.add_argument('--password', help='required if teacher is created')
    parser_add_teacher.set_defaults(func=add_teacher)

    # ADD STUDENT TO COURSE
    parser_add_student = subparsers_add.add_parser('student', help='add student to existing course')
    parser_add_student.add_argument('student_name', help='the name of the student to add')
    parser_add_student.add_argument('course_name', help='the name of the course')
    parser_add_student.add_argument('--first_name', help='the first name of the student to add')
    parser_add_student.add_argument('--last_name', help='the last name of the student to add')
    parser_add_student.add_argument('--email', help='the student\'s email')
    parser_add_student.add_argument('--lms_user_id', help='the lms_id of the student')
    parser_add_student.add_argument('--password', help='required if teacher is created')
    parser_add_student.set_defaults(func=add_student)

    # create the parser for the "del" command
    parser_del = subparsers.add_parser('del', help='delete a course or a user')
    subparsers_del = parser_del.add_subparsers(dest='element')
    subparsers_del.required = True

    # DELETE USER
    parser_del_user = subparsers_del.add_parser('user', help='delete user')
    parser_del_user.add_argument('user_name', help='the name of the user to delete')
    parser_del_user.add_argument('--course_name', help='the name of the course')
    parser_del_user.set_defaults(func=del_user)

    # DELETE COURSE
    parser_del_course = subparsers_del.add_parser('course', help='delete course')
    parser_del_course.add_argument('course_name', help='the name of the course to delete')
    parser_del_course.set_defaults(func=del_course)


    # create the parser for the "import" command
    parser_import = subparsers.add_parser('import', help='import students to course from a csv file')
    parser_import.add_argument('--file', help='file to import (last name, first name, student name, course name, password). default www/site_nsi/data/admin/students.csv')
#    parser_import.add_argument('course', help='course to add student to')
    parser_import.set_defaults(func=import_students, student_parser=parser_add_student)

    s = parser.parse_args()
    try:
        s.func(s)
    except (CourseAlreadyExists, CourseDoesNotExist) as e:
        print(e)

if __name__ == "__main__":
    print(f"Année en cours ({ANNEE}) à changer si besoin dans le fichier.")
    main()
