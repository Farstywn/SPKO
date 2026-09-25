import os

class Config:
    """Konfigurasi database MySQL ERP untuk modul Flask."""
    DB_HOST = os.getenv('DB_HOST', '127.0.0.1')
    DB_PORT = int(os.getenv('DB_PORT', 3306))
    DB_USER = os.getenv('DB_USERNAME', 'root')
    DB_PASSWORD = os.getenv('DB_PASSWORD', '')
    DB_NAME = os.getenv('DB_DATABASE', 'db_spko')
    SECRET_KEY = os.getenv('SECRET_KEY', 'spko-2026')
