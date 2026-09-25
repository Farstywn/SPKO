import os

# Helper untuk membaca file .env secara otomatis tanpa library pihak ketiga
def _load_env_file(filepath):
    if os.path.isfile(filepath):
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                for line in f:
                    line = line.strip()
                    if line and not line.startswith('#') and '=' in line:
                        k, v = line.split('=', 1)
                        k = k.strip()
                        v = v.strip().strip('"').strip("'")
                        if k not in os.environ:
                            os.environ[k] = v
        except Exception:
            pass

# Coba baca file .env di folder python_flask atau folder parent (root Laravel)
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
_load_env_file(os.path.join(BASE_DIR, '.env'))
_load_env_file(os.path.join(BASE_DIR, '..', '.env'))

class Config:
    """Konfigurasi database MySQL ERP untuk modul Flask."""
    DB_HOST = os.getenv('DB_HOST', '127.0.0.1')
    DB_PORT = int(os.getenv('DB_PORT', 3306))
    DB_USER = os.getenv('DB_USERNAME', 'root')
    DB_PASSWORD = os.getenv('DB_PASSWORD', '')
    DB_NAME = os.getenv('DB_DATABASE', 'database_erp')
    SECRET_KEY = os.getenv('SECRET_KEY', 'spko-2026')
