const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests',
  fullyParallel: true,
  reporter: 'list',
  use: {
    // Base URL where the PHP Built-in Server runs
    baseURL: 'http://localhost:8080',
    trace: 'on-first-retry',
  },
  // Configure testing matrices for 3 different viewports
  projects: [
    {
      name: 'Desktop Chrome',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'Tablet iPad',
      use: { ...devices['iPad Pro 11'] },
    },
    {
      name: 'Mobile Safari',
      use: { ...devices['iPhone 12'] },
    },
  ],
});
