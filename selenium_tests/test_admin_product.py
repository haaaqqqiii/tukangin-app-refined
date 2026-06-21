from base_test import BaseTest, BASE_URL, ADMIN_EMAIL, ADMIN_PASS, USER_EMAIL, USER_PASS
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
import time


class TestAdminProduct(BaseTest):

    def setUp(self):
        super().setUp()
        self.login(ADMIN_EMAIL, ADMIN_PASS)
        self.driver.get(f"{BASE_URL}/admin/products")
        # Gunakan contains(., '...') bukan text(), karena tombol punya <svg> di dalamnya
        # sebelum teks "Tambah Produk", sehingga text() langsung gagal mendeteksi.
        self.wait.until(
            EC.presence_of_element_located((By.XPATH, "//button[contains(., 'Tambah Produk')]"))
        )

    # S6: Tambah produk baru melalui modal
    def test_01_tambah_produk_baru(self):
        # Klik via JavaScript langsung memanggil event handler Alpine.js,
        # bypass masalah "element not interactable" akibat x-cloak/transisi
        # CSS yang membuat native Selenium click tidak ter-register.
        add_button = self.driver.find_element(By.XPATH, "//button[contains(., 'Tambah Produk')]")
        self.driver.execute_script("arguments[0].click();", add_button)

        # Tunggu modal benar-benar visible
        self.wait.until(EC.visibility_of_element_located((By.NAME, "name")))
        time.sleep(0.5)

        unique_name = f"Besi WF Test {int(time.time())}"

        name_field = self.driver.find_element(By.NAME, "name")
        name_field.clear()
        name_field.send_keys(unique_name)

        category_field = self.driver.find_element(By.NAME, "category")
        category_field.clear()
        category_field.send_keys("Besi & Baja")

        price_field = self.driver.find_element(By.NAME, "price")
        price_field.clear()
        price_field.send_keys("250000")

        unit_field = self.driver.find_element(By.NAME, "unit")
        unit_field.clear()
        unit_field.send_keys("batang")

        stock_field = self.driver.find_element(By.NAME, "stock")
        stock_field.clear()
        stock_field.send_keys("50")

        image_field = self.driver.find_element(By.NAME, "image")
        image_field.clear()
        image_field.send_keys("https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800")

        description_field = self.driver.find_element(By.NAME, "description")
        description_field.clear()
        description_field.send_keys("Besi WF untuk struktur bangunan berat.")

        # Verifikasi Alpine.js state SEBELUM submit - cek value yang
        # benar-benar tersimpan di field, bukan asumsi send_keys() berhasil
        print(f"\n[DEBUG] Value field 'name' sebelum submit: {name_field.get_attribute('value')}")
        print(f"[DEBUG] Value field 'category' sebelum submit: {category_field.get_attribute('value')}")
        print(f"[DEBUG] Value field 'price' sebelum submit: {price_field.get_attribute('value')}")

        # Submit form via JavaScript juga - cari form yang sedang visible,
        # lalu trigger native form submit langsung (skip klik tombol sama sekali)
        submit_buttons = self.driver.find_elements(
            By.XPATH, "//button[@type='submit' and contains(., 'Tambah Produk')]"
        )
        visible_submit = next(b for b in submit_buttons if b.is_displayed())
        print(f"[DEBUG] Submit button found, is_displayed: {visible_submit.is_displayed()}, is_enabled: {visible_submit.is_enabled()}")

        self.driver.execute_script("arguments[0].click();", visible_submit)

        # Tunggu sampai flash message sukses ATAU error muncul
        try:
            self.wait.until(
                lambda d: "Produk berhasil ditambahkan" in d.page_source
                or "error" in d.page_source.lower()
            )
        except Exception:
            pass

        # Diagnostic output - akan tercetak di terminal pytest kalau assertion di bawah gagal
        print(f"\n[DEBUG] Current URL: {self.driver.current_url}")
        print(f"[DEBUG] Page title: {self.driver.title}")
        body_text = self.driver.find_element(By.TAG_NAME, "body").text
        print(f"[DEBUG] Cuplikan body text (500 char pertama):\n{body_text[:500]}")

        self.assertIn("Produk berhasil ditambahkan", self.driver.page_source)
        self.assertIn(unique_name, self.driver.page_source)

    # S6: Edit produk yang sudah ada
    def test_02_edit_produk(self):
        # Klik tombol edit (icon pensil, SVG biru) pada baris produk pertama di tabel
        edit_button = self.driver.find_element(
            By.XPATH, "(//table//button[contains(@class, 'text-blue-500')])[1]"
        )
        edit_button.click()
        time.sleep(0.5)

        name_input = self.driver.find_element(By.NAME, "name")
        name_input.clear()
        updated_name = f"Produk Diperbarui {int(time.time())}"
        name_input.send_keys(updated_name)

        submit_btn = self.driver.find_element(
            By.XPATH, "//button[contains(., 'Perbarui Produk')]"
        )
        submit_btn.click()
        time.sleep(1)

        self.assertIn("Produk berhasil diperbarui", self.driver.page_source)
        self.assertIn(updated_name, self.driver.page_source)

    # S6: User biasa tidak dapat akses halaman admin (kontrol akses)
    def test_03_user_biasa_tidak_bisa_akses_admin(self):
        # Logout dari admin
        self.driver.find_element(By.XPATH, "//button[contains(., 'Keluar')]").click()
        time.sleep(1)

        # Login sebagai user biasa
        self.login(USER_EMAIL, USER_PASS)
        self.driver.get(f"{BASE_URL}/admin/products")
        time.sleep(1)

        page_source = self.driver.page_source
        self.assertTrue(
            "403" in page_source or "Akses ditolak" in page_source,
            "Seharusnya user biasa tidak dapat mengakses halaman admin"
        )


if __name__ == "__main__":
    import pytest
    pytest.main([__file__, "-v"])
