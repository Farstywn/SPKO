import sys
import os

# Menambahkan direktori python_flask ke sys.path
CURRENT_DIR = os.path.dirname(os.path.abspath(__file__))
if CURRENT_DIR not in sys.path:
    sys.path.insert(0, CURRENT_DIR)

try:
    # Import Flask instance 'app' dan jadikan 'application' untuk Passenger WSGI
    from app import app as application
except Exception as e:
    import traceback
    error_details = traceback.format_exc()

    # Fallback WSGI handler jika ada module missing atau syntax error
    def application(environ, start_response):
        start_response('500 Internal Server Error', [('Content-Type', 'text/html; charset=utf-8')])
        html = f"""
        <div style="font-family: sans-serif; padding: 25px; max-width: 750px; margin: 40px auto; border: 1px solid #f87171; border-radius: 10px; background: #fef2f2; color: #1f2937; line-height: 1.5;">
            <h2 style="color: #dc2626; margin-top: 0;">Error Inisialisasi Modul Flask</h2>
            <p>Aplikasi gagal dimuat oleh server cPanel Passenger dengan detail error:</p>
            <pre style="background: #ffffff; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb; overflow-x: auto; color: #b91c1c; font-size: 13px; font-family: monospace;">{error_details}</pre>
            <hr style="border: none; border-top: 1px solid #fca5a5; margin: 20px 0;">
            <p style="font-size: 14px; color: #4b5563;">
                <strong>Solusi Cepat:</strong><br>
                1. Jika error tertulis <code>No module named 'flask'</code> atau <code>'pymysql'</code>, masuk ke menu <strong>Setup Python App</strong> di cPanel lalu klik tombol <strong>Run Pip Install</strong>.<br>
                2. Jika error koneksi database, pastikan file <code>.env</code> di root folder atau konfigurasi database di <code>config.py</code> sudah sesuai dengan database MySQL cPanel Anda.
            </p>
        </div>
        """
        return [html.encode('utf-8')]
