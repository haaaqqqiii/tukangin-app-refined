import unittest
import os
import time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By

BASE_URL    = "http://localhost:8000"
ADMIN_EMAIL = "admin@tukangin.com"
ADMIN_PASS  = "admin123"
USER_EMAIL  = "user@tukangin.com"
USER_PASS   = "password"

SCREENSHOT_DIR = "screenshots"


class BaseTest(unittest.TestCase):
    def setUp(self):
        options = Options()
        # options.add_argument("--headless")  # Uncomment jika tidak ingin browser terbuka
        options.add_argument("--window-size=1366,768")
        self.driver = webdriver.Chrome(options=options)
        self.driver.implicitly_wait(5)
        self.wait = WebDriverWait(self.driver, 10)

        os.makedirs(SCREENSHOT_DIR, exist_ok=True)

    def tearDown(self):
        # Ambil screenshot otomatis kalau test ini gagal/error
        failed = False
        if hasattr(self, "_outcome"):
            result = self._outcome
            if hasattr(result, "errors"):
                failed = any(error for _, error in result.errors)

        if failed:
            filename = f"{SCREENSHOT_DIR}/FAILED_{self._testMethodName}_{int(time.time())}.png"
            try:
                self.driver.save_screenshot(filename)
                print(f"\n[SCREENSHOT] Disimpan ke: {filename}")
                print(f"[CURRENT URL] {self.driver.current_url}")
                print(f"[PAGE TITLE] {self.driver.title}")
            except Exception as e:
                print(f"[SCREENSHOT GAGAL] {e}")

        self.driver.quit()

    def login(self, email, password):
        """Helper untuk login. Dipanggil di setUp() test class yang butuh user sudah login."""
        self.driver.get(f"{BASE_URL}/login")
        self.wait.until(EC.presence_of_element_located((By.NAME, "email")))
        time.sleep(0.5)  # beri jeda agar Alpine.js selesai inisialisasi form

        email_fields = self.driver.find_elements(By.NAME, "email")
        password_fields = self.driver.find_elements(By.NAME, "password")

        # Form login ada di tab pertama (mode='login' default di Alpine.js)
        email_fields[0].clear()
        email_fields[0].send_keys(email)
        password_fields[0].clear()
        password_fields[0].send_keys(password)

        login_buttons = self.driver.find_elements(
            By.XPATH, "//form[@action[contains(.,'login')]]//button[@type='submit']"
        )
        login_buttons[0].click()

        # Tunggu redirect selesai (URL berubah dari /login)
        self.wait.until(lambda d: "/login" not in d.current_url)
        time.sleep(0.5)
