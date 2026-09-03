/**
 * Design audit script using Playwright.
 * Captures screenshots of key pages at common breakpoints.
 */

const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = process.env.AUDIT_URL || 'http://iphonebaycustom.test/';
const OUT_DIR = process.env.AUDIT_OUT || path.join(__dirname, '..', 'audit-output');

const PAGES = [
  { name: 'home', path: '' },
  { name: 'shop', path: 'shop/' },
  { name: 'product', path: 'product/iphone-15-pro-max/' },
  { name: 'cart', path: 'cart/' },
  { name: 'checkout', path: 'checkout/' },
  { name: 'contact', path: 'contact/' },
];

const VIEWPORTS = [
  { name: 'mobile-se', width: 375, height: 667, deviceScaleFactor: 2, isMobile: true },
  { name: 'mobile-14', width: 390, height: 844, deviceScaleFactor: 3, isMobile: true },
  { name: 'tablet', width: 768, height: 1024, deviceScaleFactor: 2 },
  { name: 'laptop', width: 1280, height: 800, deviceScaleFactor: 1 },
  { name: 'desktop', width: 1440, height: 900, deviceScaleFactor: 1 },
  { name: 'wide', width: 1920, height: 1080, deviceScaleFactor: 1 },
];

function cleanFileName(name) {
  return name.replace(/[^a-z0-9-]/gi, '_').toLowerCase();
}

async function runAudit() {
  if (!fs.existsSync(OUT_DIR)) fs.mkdirSync(OUT_DIR, { recursive: true });

  const browser = await chromium.launch();
  const context = await browser.newContext();
  const report = { baseUrl: BASE_URL, pages: [], startedAt: new Date().toISOString() };

  for (const pageDef of PAGES) {
    const pageReport = { name: pageDef.name, url: new URL(pageDef.path, BASE_URL).toString(), viewports: [], consoleErrors: [], accessibility: {} };
    console.log(`Auditing: ${pageReport.url}`);

    for (const vp of VIEWPORTS) {
      const page = await browser.newPage({ viewport: { width: vp.width, height: vp.height }, deviceScaleFactor: vp.deviceScaleFactor || 1, isMobile: vp.isMobile || false });
      const consoleErrors = [];
      page.on('console', msg => {
        if (msg.type() === 'error') consoleErrors.push(msg.text());
      });

      try {
        await page.goto(pageReport.url, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(1500); // let animations/fonts settle

        const filePrefix = `${cleanFileName(pageDef.name)}-${vp.name}`;
        const viewportPath = path.join(OUT_DIR, `${filePrefix}-viewport.png`);
        const fullPagePath = path.join(OUT_DIR, `${filePrefix}-full.png`);

        await page.screenshot({ path: viewportPath, fullPage: false });
        await page.screenshot({ path: fullPagePath, fullPage: true });

        // Collect some metrics
        const metrics = await page.evaluate(() => {
          const bodyStyles = window.getComputedStyle(document.body);
          const firstH1 = document.querySelector('h1');
          const firstP = document.querySelector('p');
          const images = Array.from(document.querySelectorAll('img'));
          const links = Array.from(document.querySelectorAll('a'));
          const buttons = Array.from(document.querySelectorAll('button, .button, [role="button"]'));
          const hero = document.querySelector('.hero, [class*="hero"]');
          return {
            title: document.title,
            bodyFont: bodyStyles.fontFamily,
            bodyBg: bodyStyles.backgroundColor,
            bodyColor: bodyStyles.color,
            h1Text: firstH1 ? firstH1.innerText.trim().slice(0, 120) : null,
            h1Font: firstH1 ? window.getComputedStyle(firstH1).fontFamily : null,
            h1Size: firstH1 ? window.getComputedStyle(firstH1).fontSize : null,
            pFont: firstP ? window.getComputedStyle(firstP).fontFamily : null,
            pSize: firstP ? window.getComputedStyle(firstP).fontSize : null,
            imageCount: images.length,
            missingAlt: images.filter(img => !img.alt && !img.getAttribute('aria-label')).length,
            lazyLoaded: images.filter(img => img.loading === 'lazy').length,
            linkCount: links.length,
            buttonCount: buttons.length,
            heroVisible: !!hero,
            scrollWidth: document.documentElement.scrollWidth,
            viewportWidth: window.innerWidth,
          };
        });

        pageReport.viewports.push({
          viewport: vp,
          viewportScreenshot: viewportPath,
          fullPageScreenshot: fullPagePath,
          metrics,
          consoleErrors: [...consoleErrors],
        });
      } catch (err) {
        pageReport.viewports.push({ viewport: vp, error: err.message });
        console.error(`  Failed ${vp.name}: ${err.message}`);
      } finally {
        await page.close();
      }
    }

    report.pages.push(pageReport);
  }

  await browser.close();
  report.finishedAt = new Date().toISOString();

  fs.writeFileSync(path.join(OUT_DIR, 'report.json'), JSON.stringify(report, null, 2));
  console.log(`Audit complete. Output: ${OUT_DIR}`);
}

runAudit().catch(err => {
  console.error(err);
  process.exit(1);
});
