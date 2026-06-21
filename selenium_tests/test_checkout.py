from base_test import BaseTest, BASE_URL, USER_EMAIL, USER_PASS
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
import time


class TestCheckout(BaseTest):

    def setUp(self):
        super().setUp()
        self.login(USER_EMAIL, USER_PASS)

    # S2 -> S2: Tambah produk ke keranjang
    def test_01_tambah_produk_ke_keranjang(self):
        self.driver.get(f"{BASE_URL}/")
        self.wait.until(
            EC.presence_of_element_located((By.XPATH, "//button[contains(text(), '+ Keranjang')]"))
        )

        add_button = self.driver.find_element(By.XPATH, "//button[contains(text(), '+ Keranjang')]")
        add_button.click()
        time.sleep(1)

        self.assertIn("ditambahkan ke keranjang", self.driver.page_source)

    # S3: Update kuantitas item di keranjang
    def test_02_update_kuantitas_keranjang(self):
        # Tambah dulu satu item
        self.driver.get(f"{BASE_URL}/")
        self.wait.until(
            EC.presence_of_element_located((By.XPATH, "//button[contains(text(), '+ Keranjang')]"))
        )
        self.driver.find_element(By.XPATH, "//button[contains(text(), '+ Keranjang')]").click()
        time.sleep(1)

        # Buka halaman keranjang
        self.driver.get(f"{BASE_URL}/cart")
        self.wait.until(EC.presence_of_element_located((By.NAME, "quantity")))

        qty_input = self.driver.find_element(By.NAME, "quantity")
        qty_input.clear()
        qty_input.send_keys("2")

        ok_button = self.driver.find_element(By.XPATH, "//button[contains(text(), 'OK')]")
        ok_button.click()
        time.sleep(1)

        self.assertIn("Keranjang diperbarui", self.driver.page_source)

    # S3 -> S4: Checkout berhasil
    def test_03_checkout_berhasil_buat_pesanan(self):
        self.driver.get(f"{BASE_URL}/")
        self.wait.until(
            EC.presence_of_element_located((By.XPATH, "//button[contains(text(), '+ Keranjang')]"))
        )
        self.driver.find_element(By.XPATH, "//button[contains(text(), '+ Keranjang')]").click()
        time.sleep(1)

        self.driver.get(f"{BASE_URL}/cart")
        self.assertIn("Keranjang Belanja", self.driver.page_source)

        checkout_btn = self.driver.find_element(
            By.XPATH, "//button[contains(text(), 'Checkout Sekarang')]"
        )
        checkout_btn.click()
        time.sleep(2)

        self.assertIn("/orders", self.driver.current_url)
        self.assertIn("Checkout berhasil", self.driver.page_source)

    # S3: Hapus item dari keranjang
    def test_04_hapus_item_keranjang(self):
        self.driver.get(f"{BASE_URL}/")
        self.wait.until(
            EC.presence_of_element_located((By.XPATH, "//button[contains(text(), '+ Keranjang')]"))
        )
        self.driver.find_element(By.XPATH, "//button[contains(text(), '+ Keranjang')]").click()
        time.sleep(1)

        self.driver.get(f"{BASE_URL}/cart")
        self.wait.until(EC.presence_of_element_located((By.XPATH, "//button[contains(text(), 'Hapus')]")))

        hapus_btn = self.driver.find_element(By.XPATH, "//button[contains(text(), 'Hapus')]")
        hapus_btn.click()
        time.sleep(1)

        self.assertIn("Item dihapus dari keranjang", self.driver.page_source)


if __name__ == "__main__":
    import pytest
    pytest.main([__file__, "-v"])
