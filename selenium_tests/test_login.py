from base_test import BaseTest, BASE_URL, USER_EMAIL, USER_PASS, ADMIN_EMAIL, ADMIN_PASS
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
import time


class TestLogin(BaseTest):

    # S1 -> S2: Login user valid
    def test_01_login_user_valid_redirect_ke_home(self):
        self.login(USER_EMAIL, USER_PASS)
        self.assertNotIn("/login", self.driver.current_url)
        header_text = self.driver.find_element(By.TAG_NAME, "header").text
        self.assertIn("Demo User", header_text)

    # S1 -> S5: Login admin valid
    def test_02_login_admin_valid_redirect_ke_dashboard(self):
        self.login(ADMIN_EMAIL, ADMIN_PASS)
        self.assertIn("/admin", self.driver.current_url)
        self.assertIn("Dashboard Overview", self.driver.page_source)

    # S1 -> S1: Login dengan password salah
    def test_03_login_password_salah_tampilkan_error(self):
        self.driver.get(f"{BASE_URL}/login")
        self.wait.until(EC.presence_of_element_located((By.NAME, "email")))

        self.driver.find_elements(By.NAME, "email")[0].send_keys(USER_EMAIL)
        self.driver.find_elements(By.NAME, "password")[0].send_keys("passwordSalah")
        self.driver.find_elements(
            By.XPATH, "//form[@action[contains(.,'login')]]//button[@type='submit']"
        )[0].click()

        time.sleep(1)
        self.assertIn("Email atau password salah", self.driver.page_source)
        self.assertIn("/login", self.driver.current_url)

    # S1 -> S1: Login dengan email tidak terdaftar
    def test_04_login_email_tidak_terdaftar(self):
        self.driver.get(f"{BASE_URL}/login")
        self.wait.until(EC.presence_of_element_located((By.NAME, "email")))

        self.driver.find_elements(By.NAME, "email")[0].send_keys("tidakada@email.com")
        self.driver.find_elements(By.NAME, "password")[0].send_keys("password")
        self.driver.find_elements(
            By.XPATH, "//form[@action[contains(.,'login')]]//button[@type='submit']"
        )[0].click()

        time.sleep(1)
        self.assertIn("Email atau password salah", self.driver.page_source)

    # S0 -> S1: Register akun baru valid
    def test_05_register_akun_baru_berhasil(self):
        self.driver.get(f"{BASE_URL}/login")
        self.wait.until(EC.presence_of_element_located((By.NAME, "email")))

        # Klik tab "Daftar" dulu (Alpine.js toggle mode)
        daftar_tab = self.driver.find_element(By.XPATH, "//button[contains(text(), 'Daftar')]")
        daftar_tab.click()
        time.sleep(0.5)

        unique_email = f"seleniumtest{int(time.time())}@tukangin.com"

        self.driver.find_element(By.NAME, "name").send_keys("Selenium Test User")
        self.driver.find_elements(By.NAME, "email")[1].send_keys(unique_email)
        self.driver.find_elements(By.NAME, "password")[1].send_keys("password123")
        self.driver.find_element(By.NAME, "password_confirmation").send_keys("password123")

        register_buttons = self.driver.find_elements(
            By.XPATH, "//form[@action[contains(.,'register')]]//button[@type='submit']"
        )
        register_buttons[0].click()

        self.wait.until(lambda d: "/login" not in d.current_url)
        self.assertIn("Akun berhasil dibuat", self.driver.page_source)


if __name__ == "__main__":
    import pytest
    pytest.main([__file__, "-v"])
