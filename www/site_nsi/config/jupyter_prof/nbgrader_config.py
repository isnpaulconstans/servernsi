c = get_config()
c.CourseDirectory.root = '/home/manu/ipynb'

#c.CourseDirectory.course_id = "ipynb"
#c.IncludeHeaderFooter.header = "source/header.ipynb"
c.ClearSolutions.code_stub = {'python': '# TAPEZ VOTRE CODE ICI', 'matlab': "% YOUR CODE HERE\nerror('No Answer Given!')", 'octave': "% YOUR CODE HERE\nerror('No Answer Given!')", 'java': '// YOUR CODE HERE'}
c.ClearSolutions.text_stub = 'VOTRE REPONSE'


