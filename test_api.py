from nbgrader.apps import NbGraderAPI
from traitlets.config import Config

# create a custom config object to specify options for nbgrader
config = Config()
config.CourseDirectory.course_id = "tnsi"
config.CourseDirectory.db_url = "sqlite:////home/grader-1nsi/1nsi/gradebook.db"


api = NbGraderAPI(config=config)

