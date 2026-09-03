const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');
const OUT_DIR = path.join(__dirname, '..', 'audit-output');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 3, isMobile: true });
  await page.goto('http://iphonebaycustom.test/', { waitUntil: 'networkidle' });
  await page.waitForTimeout(1200);
  // Close menu if open by clicking a neutral area
  await page.mouse.click(50, 100);
  await page.waitForTimeout(500);
  await page.screenshot({ path: path.join(OUT_DIR, 'home-mobile-menu-closed.png'), fullPage: false });
  // Now open menu intentionally
  const hamburger = await page.$('.nav-hamburger');
  if (hamburger) {
    await hamburger.click();
    await page.waitForTimeout(500);
    await page.screenshot({ path: path.join(OUT_DIR, 'home-mobile-menu-open.png'), fullPage: false });
  }
  await browser.close();
})();
