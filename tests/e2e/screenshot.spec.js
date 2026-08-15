const { test, expect } = require('@playwright/test');
const fs = require('fs');

test('capture screenshots of the application', async ({ page }) => {
  test.setTimeout(120000); // 120 seconds to prevent timeout during multi-device testing
  // Ensure screenshots directory exists
  if (!fs.existsSync('screenshots')) {
    fs.mkdirSync('screenshots');
  }

  const baseUrl = 'http://127.0.0.1:8080';

  // 1. Homepage / Login Page
  await page.goto(`${baseUrl}/auth/login`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: 'screenshots/1-login-page.png', fullPage: true });

  // 2. Registration Page
  await page.goto(`${baseUrl}/auth/register`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: 'screenshots/2-register-page.png', fullPage: true });

  // 3. Login as Demo Admin
  await page.goto(`${baseUrl}/auth/login`);
  await page.fill('input[name="email"]', 'demoadmin@example.com');
  await page.fill('input[name="password"]', 'demoadmin123');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');

  // 4. Admin Dashboard
  await page.goto(`${baseUrl}/admin`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: 'screenshots/3-admin-dashboard.png', fullPage: true });

  // 5. Logout
  await page.goto(`${baseUrl}/auth/logout`);

  // 6. Login as Standard Demo User
  await page.goto(`${baseUrl}/auth/login`);
  await page.fill('input[name="email"]', 'demouser@example.com');
  await page.fill('input[name="password"]', 'demouser123');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');

  // 7. User Dashboard
  await page.goto(`${baseUrl}/dashboard`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: 'screenshots/4-user-dashboard.png', fullPage: true });

  // 8. Transactions Page
  await page.goto(`${baseUrl}/transaction`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: 'screenshots/5-transactions-page.png', fullPage: true });
});
