#! /bin/bash

sudo pip3 install --upgrade pip
sudo apt-get install npm 
sudo npm install -g configurable-http-proxy
sudo -H pip3 install notebook jupyterhub
sudo mkdir -p /srv/jupyterhub /etc/jupyterhub
openssl rand -base64 2048 > cookie_secret
openssl req -x509 -nodes -days 30 -newkey rsa:4096 -keyout jupyterhub.key -out jupyterhub.crt
jupyterhub --generate-config
sed -i -e "s/#c.JupyterHub.cookie_secret_file = 'jupyterhub_cookie_secret'/c.JupyterHub.cookie_secret_file = '\/srv\/jupyterhub\/cookie_secret'/" jupyterhub_config.py
sed -i -e "s/#c.JupyterHub.db_url = 'sqlite:\/\/\/jupyterhub.sqlite'/c.JupyterHub.db_url = 'sqlite:\/\/\/\/srv\/jupyterhub\/jupyterhub.sqlite'/" jupyterhub_config.py
sed -i -e "s/#c.JupyterHub.ssl_cert = ''/c.JupyterHub.sl_cert = '\/srv\/jupyterhub\/jupyterhub.crt'/" jupyterhub_config.py
sed -i -e "s/#c.JupyterHub.ssl_key = ''/c.JupyterHub.sl_key = '\/srv\/jupyterhub\/jupyterhub.key'/" jupyterhub_config.py
sed -i -e "s/#c.JupyterHub.bind_url = 'http:\/\/:8000'/c.JupyterHub.bind_url = 'http:\/\/:8888/jupyter'/" jupyterhub_config.py
sed -i -e "s/#c.Authenticator.admin_users = set()/c.Authenticator.admin_users = {'manu'}/" jupyterhub_config.py
sed -i -e "s/#c.JupyterHub.admin_access = False/c.JupyterHub.admin_access = True/" jupyterhub_config.py
sudo chown root:root jupyterhub_config.py jupyterhub.key jupyterhub.crt cookie_secret
sudo mv jupyterhub.key jupyterhub.crt cookie_secret /srv/jupyterhub/
sudo mv jupyterhub_config.py /etc/jupyterhub/ 
sudo chmod -R 600 /srv/jupyterhub /etc/jupyterhub
sudo cat <<EOF > /lib/systemd/system/jupyterhub.service
[Unit]
Description=JupyterHub Service 
After=multi-user.target

[Service] 
User=root 
ExecStart=/usr/local/bin/jupyterhub --config=/etc/jupyterhub/jupyterhub_config.py 
Restart=on-failure

[Install] 
WantedBy=multi-user.target
EOF
sudo systemctl daemon-reload
sudo systemctl start jupyterhub
sudo systemctl enable jupyterhub
sudo systemctl status jupyterhub.service

sudo pip3 install nbgrader

# Install global extensions, and disable them globally.
sudo jupyter nbextension install --sys-prefix --py nbgrader --overwrite
sudo jupyter nbextension disable --sys-prefix --py nbgrader
sudo jupyter serverextension disable --sys-prefix --py nbgrader

# Everybody gets the validate extension, however.
sudo jupyter nbextension enable --sys-prefix validate_assignment/main --section=notebook
sudo jupyter serverextension enable --sys-prefix nbgrader.server_extensions.validate_assignment

# Prof
jupyter nbextension enable --user create_assignment/main
jupyter nbextension enable --user formgrader/main --section=tree
jupyter serverextension enable --user nbgrader.server_extensions.formgrader

# Prof + élève (à executer avec le compte élève : `sudo -u toto jupyter ...`)
jupyter nbextension enable --user assignment_list/main --section=tree
jupyter serverextension enable --user nbgrader.server_extensions.assignment_list

sudo mkdir -p /etc/jupyter /srv/nbgrader/exchange
sudo chmod ugo+rwx /srv/nbgrader/exchange
sudo cat <<EOF > /etc/jupyter/nbgrader_config.py
c = get_config()
c.NbGrader.logfile = '/usr/local/share/jupyter/nbgrader.log'
EOF
sudo cat <<EOF > /home/manu/.jupyter/nbgrader_config.py
c = get_config()
c.CourseDirectory.course_id = "ipynb"
c.IncludeHeaderFooter.header = "source/header.ipynb"
c.ClearSolutions.code_stub = {'python': '# TAPEZ VOTRE CODE ICI', 'matlab': "% YOUR CODE HERE\nerror('No Answer Given!')", 'octave': "% YOUR CODE HERE\nerror('No Answer Given!')", 'java': '// YOUR CODE HERE'}
c.ClearSolutions.text_stub = 'VOTRE REPONSE'
EOF

