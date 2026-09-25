import unittest
from app import app

class FlaskAppTestCase(unittest.TestCase):
    def setUp(self):
        app.config['TESTING'] = True
        self.client = app.test_client()

    def test_index_page(self):
        """Memverifikasi bahwa halaman utama selisih berat dapat diakses (status 200)."""
        response = self.client.get('/')
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Analisis Selisih Berat per Produk (FG)', response.data)
        self.assertIn(b'Tabel Selisih Berat per Produk (FG)', response.data)

    def test_detail_page_valid_fg(self):
        """Memverifikasi bahwa halaman rincian produk FG 128409 dapat diakses."""
        response = self.client.get('/detail/128409')
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Cincin Anak Laki Putus', response.data)

    def test_detail_page_invalid_fg(self):
        """Memverifikasi bahwa FG yang tidak terdaftar menghasilkan 404."""
        response = self.client.get('/detail/99999999')
        self.assertEqual(response.status_code, 404)

    def test_api_summary(self):
        """Memverifikasi endpoint API summary mengembalikan respons JSON valid."""
        response = self.client.get('/api/summary')
        self.assertEqual(response.status_code, 200)
        json_data = response.get_json()
        self.assertEqual(json_data['status'], 'success')
        self.assertIn('data', json_data)

if __name__ == '__main__':
    unittest.main()
