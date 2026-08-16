const { test, expect } = require('@playwright/test');
const fs = require('fs');

test('capture screenshots of the application', async ({ page }, testInfo) => {
  test.setTimeout(120000); // 120 seconds to prevent timeout during multi-device testing
  
  const device = testInfo.project.name.replace(/\s+/g, '_');
  const screenshotDir = `screenshots/${device}`;

  // Ensure screenshots directory exists
  if (!fs.existsSync(screenshotDir)) {
    fs.mkdirSync(screenshotDir, { recursive: true });
  }

  const baseUrl = 'http://127.0.0.1:8080';

  // 1. Homepage / Login Page
  await page.goto(`${baseUrl}/auth/login`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: `${screenshotDir}/1-login-page.png`, fullPage: true });

  // 2. Registration Page
  await page.goto(`${baseUrl}/auth/register`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: `${screenshotDir}/2-register-page.png`, fullPage: true });

  // 3. Login as Demo Admin
  await page.goto(`${baseUrl}/auth/login`);
  await page.fill('input[name="email"]', 'demoadmin@example.com');
  await page.fill('input[name="password"]', 'demoadmin123');
  await page.click('input[type="submit"]');
  await page.waitForLoadState('networkidle');

  // 4. Admin Dashboard
  await page.goto(`${baseUrl}/admin`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: `${screenshotDir}/3-admin-dashboard.png`, fullPage: true });

  // 5. Logout
  await page.goto(`${baseUrl}/auth/logout`);

  // 6. Login as Standard Demo User
  await page.goto(`${baseUrl}/auth/login`);
  await page.fill('input[name="email"]', 'demouser@example.com');
  await page.fill('input[name="password"]', 'demouser123');
  await page.click('input[type="submit"]');
  await page.waitForLoadState('networkidle');

  // 7. User Dashboard
  await page.goto(`${baseUrl}/dashboard`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: `${screenshotDir}/4-user-dashboard.png`, fullPage: true });

  // 8. Transactions Page
  await page.goto(`${baseUrl}/transaction`);
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: `${screenshotDir}/5-transactions-page.png`, fullPage: true });
});
