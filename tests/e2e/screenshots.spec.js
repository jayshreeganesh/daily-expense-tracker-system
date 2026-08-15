const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

(async () => {
    const baseDir = 'screenshots';
    if (!fs.existsSync(baseDir)) {
        fs.mkdirSync(baseDir);
    }

    const browser = await chromium.launch();
    const page = await browser.newPage();

    console.log('Logging in...');
    await page.goto('http://localhost:8080/auth/login');
    await page.fill('input[name="email"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('input[type="submit"]');

    await page.waitForTimeout(2000); 

    const viewports = [
        { name: 'Desktop_1080p', width: 1920, height: 1080 },
        { name: 'Laptop', width: 1366, height: 768 },
        { name: 'Tablet_Landscape', width: 1024, height: 768 },
        { name: 'Tablet_Portrait', width: 768, height: 1024 },
        { name: 'Mobile', width: 375, height: 812 }
    ];

    const pagesToCapture = [
        { name: 'Dashboard', url: 'http://localhost:8080/dashboard' },
        { name: 'Admin_Panel', url: 'http://localhost:8080/admin' },
        { name: 'Transactions', url: 'http://localhost:8080/transaction' },
        { name: 'Profile', url: 'http://localhost:8080/profile' },
        { name: 'Reminders', url: 'http://localhost:8080/reminder' }
    ];

    for (const vp of viewports) {
        const deviceDir = path.join(baseDir, vp.name);
        if (!fs.existsSync(deviceDir)) {
            fs.mkdirSync(deviceDir);
        }

        await page.setViewportSize({ width: vp.width, height: vp.height });
        console.log(`Setting viewport to ${vp.name}...`);

        for (const pg of pagesToCapture) {
            console.log(`Navigating to ${pg.name}...`);
            await page.goto(pg.url, { waitUntil: 'networkidle' });
            await page.waitForTimeout(1000); 

            const screenshotPath = path.join(deviceDir, `${pg.name}.png`);
            await page.screenshot({ path: screenshotPath, fullPage: true });
            console.log(`Saved screenshot: ${screenshotPath}`);
        }
    }

    console.log('Generating PDF of Desktop Dashboard...');
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.goto('http://localhost:8080/dashboard', { waitUntil: 'networkidle' });
    await page.waitForTimeout(1000); 
    
    // Force CSS media type to 'screen' to prevent Bootstrap from applying print stylesheets
    // which normally strip colors, borders, and mess up charts.
    await page.emulateMedia({ media: 'screen' });
    
    // Inject a style to prevent page breaks inside cards
    await page.addStyleTag({ content: '.card { page-break-inside: avoid; }' });

    await page.pdf({ 
        path: path.join(baseDir, 'ExpenseTracker_Portfolio_Doc.pdf'), 
        format: 'A3', // Using A3 for wider layout to fit desktop better
        printBackground: true,
        margin: { top: '20px', right: '20px', bottom: '20px', left: '20px' }
    });
    console.log(`Saved PDF: ${path.join(baseDir, 'ExpenseTracker_Portfolio_Doc.pdf')}`);

    await browser.close();
    console.log('All screenshots and PDFs generated successfully!');
})();
