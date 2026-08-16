const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/e2e',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  use: {
    baseURL: 'http://localhost:8080',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  },
  projects: [
    {
      name: 'Desktop_1080p',
      use: { browserName: 'chromium', viewport: { width: 1920, height: 1080 } },
    },
    {
      name: 'Laptop',
      use: { browserName: 'chromium', viewport: { width: 1366, height: 768 } },
    },
    {
      name: 'Tablet_Landscape',
      use: { browserName: 'chromium', viewport: { width: 1024, height: 768 } },
    },
    {
      name: 'Tablet_Portrait',
      use: { browserName: 'chromium', viewport: { width: 768, height: 1024 } },
    },
    {
      name: 'Mobile',
      use: { browserName: 'chromium', viewport: { width: 375, height: 812 }, isMobile: true },
    },
  ],
});
