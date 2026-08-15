const { test, expect } = require('@playwright/test');

// We will test logging in as both an Admin and a Standard User
const users = [
  { role: 'Admin', email: 'admin@example.com', password: 'admin123', portalBtn: '👑 Admin / Recruiter Portal' },
  { role: 'User', email: 'user@example.com', password: 'user123', portalBtn: '👤 User Login Portal' }
];

test.describe('Responsive Screenshot Engine', () => {
  for (const user of users) {
    test(`Capture Full-Flow Screenshots for ${user.role}`, async ({ page }, testInfo) => {
      // Get the sanitized name of the device (Desktop, Tablet, Mobile)
      const deviceName = testInfo.project.name.replace(/\s+/g, '-');
      
      console.log(`Testing ${user.role} on ${deviceName}...`);

      // 1. Visit the Auth Portal
      await page.goto('/auth');
      await page.screenshot({ path: `screenshots/${deviceName}/${user.role}-1-Portal.png` });

      // 2. Click respective portal button
      await page.getByText(user.portalBtn).click();
      await page.screenshot({ path: `screenshots/${deviceName}/${user.role}-2-LoginForm.png` });

      // 3. Login
      await page.fill('input[name="email"]', user.email);
      await page.fill('input[name="password"]', user.password);
      await page.click('input[type="submit"]');

      // 4. Wait for Dashboard to load and take a FULL PAGE screenshot
      await page.waitForURL('**/dashboard', { timeout: 5000 }).catch(() => {});
      // Wait for chart.js animation to finish
      await page.waitForTimeout(1000); 
      await page.screenshot({ path: `screenshots/${deviceName}/${user.role}-3-Dashboard.png`, fullPage: true });

      // 5. If it's an Admin, also screenshot the Admin Panel
      if (user.role === 'Admin') {
        await page.goto('/admin');
        await page.waitForTimeout(1000); 
        await page.screenshot({ path: `screenshots/${deviceName}/${user.role}-4-AdminPanel.png`, fullPage: true });
      }

      // 6. Logout cleanly
      await page.goto('/auth/logout');
    });
  }
});
