import pymysql
from pymysql.cursors import DictCursor
from flask import Flask, render_template, request, jsonify, abort
from config import Config

app = Flask(__name__)
app.config.from_object(Config)

def get_db_connection():
    """Membuka koneksi ke MySQL database_erp."""
    return pymysql.connect(
        host=app.config['DB_HOST'],
        port=app.config['DB_PORT'],
        user=app.config['DB_USER'],
        password=app.config['DB_PASSWORD'],
        database=app.config['DB_NAME'],
        cursorclass=DictCursor,
        autocommit=True
    )

@app.route('/')
def index():
    """
    Halaman Utama Modul Selisih Berat per Produk (FG) - Soal 4.
    Menampilkan perbandingan bobot SPKO (workallocation) dan NTHKO (workcompletion).
    """
    category_filter = request.args.get('category', '').strip()
    search_keyword = request.args.get('search', '').strip()

    connection = get_db_connection()
    try:
        with connection.cursor() as cursor:
            # Mengambil daftar sub-kategori untuk filter
            cursor.execute("SELECT DISTINCT sub_category FROM product ORDER BY sub_category")
            categories = [row['sub_category'] for row in cursor.fetchall()]

            # Query utama: Agregasi per produk (FG)
            query = """
                SELECT 
                    p.Id_product AS fg,
                    p.description,
                    p.sub_category,
                    p.carat,
                    p.serial_no,
                    COALESCE(SUM(wai.Qty), 0) AS qty_spko,
                    COALESCE(SUM(wai.Weight), 0.00) AS berat_spko,
                    COALESCE(SUM(wci.Qty), 0) AS qty_nthko,
                    COALESCE(SUM(wci.Weight), 0.00) AS berat_nthko,
                    (COALESCE(SUM(wai.Weight), 0.00) - COALESCE(SUM(wci.Weight), 0.00)) AS selisih_berat,
                    (COALESCE(SUM(wai.Qty), 0) - COALESCE(SUM(wci.Qty), 0)) AS selisih_qty
                FROM product p
                LEFT JOIN workallocationitem wai ON p.Id_product = wai.FG
                LEFT JOIN workcompletionitem wci 
                    ON p.Id_product = wci.FG 
                    AND wai.IDM = wci.LinkID 
                    AND wai.Ordinal = wci.LinkOrd
                WHERE 1=1
            """
            params = []

            if category_filter:
                query += " AND p.sub_category = %s"
                params.append(category_filter)

            if search_keyword:
                query += " AND (p.description LIKE %s OR p.Id_product LIKE %s)"
                params.append(f"%{search_keyword}%")
                params.append(f"%{search_keyword}%")

            query += """
                GROUP BY p.Id_product, p.description, p.sub_category, p.carat, p.serial_no
                ORDER BY selisih_berat DESC, p.Id_product ASC
            """

            cursor.execute(query, params)
            products = cursor.fetchall()

            # Hitung total metrik ringkasan
            total_spko_weight = sum(float(p['berat_spko']) for p in products)
            total_nthko_weight = sum(float(p['berat_nthko']) for p in products)
            total_weight_diff = total_spko_weight - total_nthko_weight
            shrinkage_percentage = (total_weight_diff / total_spko_weight * 100) if total_spko_weight > 0 else 0.0

            total_spko_qty = sum(int(p['qty_spko']) for p in products)
            total_nthko_qty = sum(int(p['qty_nthko']) for p in products)

            summary = {
                'total_products': len(products),
                'total_spko_weight': total_spko_weight,
                'total_nthko_weight': total_nthko_weight,
                'total_weight_diff': total_weight_diff,
                'shrinkage_percentage': shrinkage_percentage,
                'total_spko_qty': total_spko_qty,
                'total_nthko_qty': total_nthko_qty,
            }

    finally:
        connection.close()

    return render_template(
        'index.html',
        products=products,
        categories=categories,
        summary=summary,
        selected_category=category_filter,
        search_keyword=search_keyword
    )

@app.route('/detail/<int:fg_id>')
def detail(fg_id):
    """
    Halaman Rincian Transaksi per Produk (FG).
    Menampilkan setiap transaksi SPKO dan pasangannya di NTHKO.
    """
    connection = get_db_connection()
    try:
        with connection.cursor() as cursor:
            # Ambil data produk
            cursor.execute("SELECT * FROM product WHERE Id_product = %s", (fg_id,))
            product = cursor.fetchone()
            if not product:
                abort(404, description="Produk FG tidak ditemukan")

            # Ambil perincian transaksi
            query = """
                SELECT 
                    wai.IDM AS spko_id,
                    wa.SW AS no_spko,
                    wa.TransDate AS tgl_spko,
                    wa.Process AS proses,
                    e.nama AS nama_operator,
                    wai.Ordinal AS ordinal,
                    wai.Qty AS qty_spko,
                    wai.Weight AS berat_spko,
                    wci.Qty AS qty_nthko,
                    wci.Weight AS berat_nthko,
                    wc.TransDate AS tgl_nthko,
                    (COALESCE(wai.Weight, 0) - COALESCE(wci.Weight, 0)) AS selisih_berat,
                    (COALESCE(wai.Qty, 0) - COALESCE(wci.Qty, 0)) AS selisih_qty
                FROM workallocationitem wai
                JOIN workallocation wa ON wai.IDM = wa.ID
                JOIN employee e ON wa.Employee = e.Id_employee
                LEFT JOIN workcompletionitem wci 
                    ON wai.IDM = wci.LinkID 
                    AND wai.Ordinal = wci.LinkOrd
                LEFT JOIN workcompletion wc ON wci.IDM = wc.ID
                WHERE wai.FG = %s
                ORDER BY wa.TransDate DESC, wai.IDM DESC, wai.Ordinal ASC
            """
            cursor.execute(query, (fg_id,))
            transactions = cursor.fetchall()

    finally:
        connection.close()

    return render_template('detail.html', product=product, transactions=transactions)

@app.route('/api/summary')
def api_summary():
    """Endpoint REST JSON untuk kebutuhan integrasi/API eksternal."""
    connection = get_db_connection()
    try:
        with connection.cursor() as cursor:
            cursor.execute("""
                SELECT 
                    COUNT(DISTINCT wai.FG) AS active_fg_count,
                    SUM(wai.Weight) AS total_spko_weight,
                    SUM(wci.Weight) AS total_nthko_weight,
                    (SUM(wai.Weight) - SUM(wci.Weight)) AS total_loss
                FROM workallocationitem wai
                LEFT JOIN workcompletionitem wci 
                    ON wai.IDM = wci.LinkID AND wai.Ordinal = wci.LinkOrd
            """)
            data = cursor.fetchone()
            return jsonify({
                'status': 'success',
                'data': data
            })
    finally:
        connection.close()

@app.errorhandler(Exception)
def handle_exception(e):
    """Menampilkan detail error jika terjadi kendala runtime (misal: koneksi DB gagal)."""
    return f"""
    <div style="font-family: sans-serif; padding: 25px; max-width: 750px; margin: 40px auto; border: 1px solid #f87171; border-radius: 10px; background: #fef2f2; color: #1f2937; line-height: 1.5;">
        <h2 style="color: #dc2626; margin-top: 0;">Terjadi Kendala pada Modul Flask (Python)</h2>
        <p><strong>Pesan Error:</strong> <span style="color: #b91c1c; font-family: monospace;">{str(e)}</span></p>
        <hr style="border: none; border-top: 1px solid #fca5a5; margin: 20px 0;">
        <p style="font-size: 14px; color: #4b5563;">
            <strong>Pengaturan Database Aktif Saat Ini:</strong><br>
            • Host: <code>{app.config.get('DB_HOST')}</code><br>
            • Port: <code>{app.config.get('DB_PORT')}</code><br>
            • Database: <code>{app.config.get('DB_NAME')}</code><br>
            • User: <code>{app.config.get('DB_USER')}</code><br>
        </p>
        <p style="font-size: 13px; color: #6b7280;">
            Pastikan data di atas sudah sama dengan database cPanel Anda pada file <code>.env</code> atau <code>config.py</code>.
        </p>
    </div>
    """, 500

if __name__ == '__main__':
    # Jalankan server development Flask pada port 5000
    app.run(host='127.0.0.1', port=5000, debug=True)
