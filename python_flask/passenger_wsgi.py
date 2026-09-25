import sys
import os

# Menambahkan direktori python_flask ke sys.path
CURRENT_DIR = os.path.dirname(os.path.abspath(__file__))
if CURRENT_DIR not in sys.path:
    sys.path.insert(0, CURRENT_DIR)

# Import Flask instance 'app' dan expose sebagai 'application' (standar WSGI Passenger cPanel)
from app import app as application
