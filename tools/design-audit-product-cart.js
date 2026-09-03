/**
 * Follow-up audit: single product page and populated cart/checkout.
 */

const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = process.env.AUDIT_URL || 'http://iphonebaycustom.test/';
const OUT_DIR = process.env.AUDIT_OUT || path.join(__dirname, '..', 'audit-output');

const VIEWPORTS = [
  { name: 'mobile-14', width: 390, height: 844, deviceScaleFactor: 3, isMobile: true },
  { name: 'laptop', width: 1280, height: 800, deviceScaleFactor: 1 },
];

async function runAudit() {
  if (!fs.existsSync(OUT_DIR)) fs.mkdirSync(OUT_DIR, { recursive: true });
  const browser = await chromium.launch();
  const context = await browser.newContext();

  // 1. Product page
  for (const vp of VIEWPORTS) {
    const page = await browser.newPage({ viewport: { width: vp.width, height: vp.height }, deviceScaleFactor: vp.deviceScaleFactor || 1, isMobile: vp.isMobile || false });
    await page.goto(`${BASE_URL}product/apple-iphone-15-pro/`, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(1500);
    const prefix = `product-real-${vp.name}`;
    await page.screenshot({ path: path.join(OUT_DIR, `${prefix}-viewport.png`), fullPage: false });
    await page.screenshot({ path: path.join(OUT_DIR, `${prefix}-full.png`), fullPage: true });
    const metrics = await page.evaluate(() => ({
      title: document.title,
      h1Text: document.querySelector('h1')?.innerText?.trim().slice(0, 120),
      price: document.querySelector('.price, [class*="price"]')?.innerText?.trim().slice(0, 120),
      addToCartVisible: !!document.querySelector('.single_add_to_cart_button, button[name="add-to-cart"], .add_to_cart_button'),
      galleryImages: document.querySelectorAll('.woocommerce-product-gallery__image img, .product-gallery img').length,
    }));
    console.log(`product ${vp.name}:`, metrics);
    await page.close();
  }

  // 2. Cart with item: add to cart first
  for (const vp of VIEWPORTS) {
    const page = await browser.newPage({ viewport: { width: vp.width, height: vp.height }, deviceScaleFactor: vp.deviceScaleFactor || 1, isMobile: vp.isMobile || false });
    // Try adding iPhone 15 Pro to cart via URL
    await page.goto(`${BASE_URL}?add-to-cart=apple-iphone-15-pro`, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(1000);
    // If redirected to product, add via form
    const addButton = await page.$('button[name="add-to-cart"]');
    if (addButton) {
      await addButton.click();
      await page.waitForTimeout(2000);
    }
    await page.goto(`${BASE_URL}cart/`, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(1500);
    const prefix = `cart-populated-${vp.name}`;
    await page.screenshot({ path: path.join(OUT_DIR, `${prefix}-viewport.png`), fullPage: false });
    await page.screenshot({ path: path.join(OUT_DIR, `${prefix}-full.png`), fullPage: true });
    const cartMetrics = await page.evaluate(() => ({
      title: document.title,
      cartItems: document.querySelectorAll('.cart_item').length,
      emptyMessage: document.querySelector('.cart-empty')?.innerText?.trim().slice(0, 120),
    }));
    console.log(`cart ${vp.name}:`, cartMetrics);

    // Checkout
    await page.goto(`${BASE_URL}checkout/`, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(1500);
    const checkoutPrefix = `checkout-populated-${vp.name}`;
    await page.screenshot({ path: path.join(OUT_DIR, `${checkoutPrefix}-viewport.png`), fullPage: false });
    await page.screenshot({ path: path.join(OUT_DIR, `${checkoutPrefix}-full.png`), fullPage: true });
    const checkoutMetrics = await page.evaluate(() => ({
      title: document.title,
      h1Text: document.querySelector('h1')?.innerText?.trim().slice(0, 120),
      checkoutForm: !!document.querySelector('form.checkout'),
    }));
    console.log(`checkout ${vp.name}:`, checkoutMetrics);
    await page.close();
  }

  await browser.close();
  console.log('Follow-up audit complete.');
}

runAudit().catch(err => {
  console.error(err);
  process.exit(1);
});
