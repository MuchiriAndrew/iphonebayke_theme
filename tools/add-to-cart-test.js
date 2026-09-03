const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://iphonebaycustom.test/';
const OUT_DIR = path.join(__dirname, '..', 'audit-output');

async function run() {
  const browser = await chromium.launch();
  const context = await browser.newContext({ viewport: { width: 1280, height: 800 } });
  const page = await context.newPage();

  await page.goto(`${BASE_URL}product/apple-iphone-15/`, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(2000);

  // Select first non-empty option in each variation select
  const selects = await page.$$('select[name^="attribute_"]');
  console.log('Variation selects:', selects.length);
  for (const select of selects) {
    const options = await select.$$('option');
    for (const option of options) {
      const val = await option.getAttribute('value');
      if (val) {
        await select.selectOption(val);
        break;
      }
    }
  }

  await page.waitForTimeout(800);
  const addBtn = await page.$('.single_add_to_cart_button, button[name="add-to-cart"]');
  if (addBtn) {
    await addBtn.click();
    console.log('Clicked add to cart');
    await page.waitForTimeout(2500);
  } else {
    console.log('No add to cart button found');
  }

  await page.goto(`${BASE_URL}cart/`, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(1500);
  await page.screenshot({ path: path.join(OUT_DIR, 'cart-with-item-full.png'), fullPage: true });
  await page.screenshot({ path: path.join(OUT_DIR, 'cart-with-item-viewport.png'), fullPage: false });

  await page.goto(`${BASE_URL}checkout/`, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(1500);
  await page.screenshot({ path: path.join(OUT_DIR, 'checkout-with-item-full.png'), fullPage: true });
  await page.screenshot({ path: path.join(OUT_DIR, 'checkout-with-item-viewport.png'), fullPage: false });

  const cartMetrics = await page.evaluate(() => ({
    cartTitle: document.title,
    cartItems: document.querySelectorAll('.cart_item').length,
    emptyMessage: document.querySelector('.cart-empty')?.innerText?.trim().slice(0, 120),
  }));
  console.log('Cart metrics:', cartMetrics);

  await browser.close();
}

run().catch(err => { console.error(err); process.exit(1); });
