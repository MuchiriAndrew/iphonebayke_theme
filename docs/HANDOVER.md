# IphoneBayKE — Theme Handover

A custom WordPress + WooCommerce theme for **IphoneBayKE**, a Kenyan premium smartphone
marketplace (Ex‑UK / Brand New / Refurbished iPhones, Samsung, Google Pixel, accessories).
Product‑first, mobile‑first, and fully editable without any paid plugins.

- **Theme slug:** `iphonebay`
- **Location:** `wp-content/themes/iphonebay/`
- **Live (local):** `http://iphonebaycustom.test` (Laravel Valet)
- **Author:** Belva Digital

---

## 1. Stack & requirements

| Item | Detail |
| --- | --- |
| WordPress | 6.0+ |
| PHP | 7.4+ (developed on 8.3) |
| WooCommerce | 7.0+ (running 10.8.1) — **required** |
| Fonts | Outfit (Google Fonts, loaded by the theme) |
| Page builder | Elementor‑*compatible* (optional). Full‑Width template provided. |
| Paid plugins | **None.** No ACF, no swatch plugin, no slider plugin. |

If WooCommerce is not active, the theme still loads and shows an admin notice; shop/product
sections degrade gracefully (carousels simply render nothing).

---

## 2. File map

```
iphonebay/
├── style.css                    Theme header only (styles are enqueued separately)
├── functions.php                Setup, enqueues, menus, image sizes, includes
├── header.php                   Fixed nav + off‑canvas (menu‑driven)
├── footer.php                   4‑column footer (menu locations + customizer)
├── front-page.php               Homepage section assembly
├── page.php / single.php        Standard page / blog post
├── index.php / archive.php / search.php   Blog + fallbacks
├── 404.php / comments.php
├── template-full-width.php      "Full Width (Page Builder)" page template for Elementor/blocks
│
├── inc/
│   ├── class-iphonebay-nav-walker.php   Desktop dropdown walker + mobile accordion walker + fallbacks
│   ├── post-types.php           CPTs: hero_slide, testimonial (+ native meta boxes, no ACF)
│   ├── customizer.php           "IphoneBayKE Options" panel + iphonebay_opt() + iphonebay_highlight()
│   ├── template-functions.php   Product card, homepage product queries, carousel renderer, socials
│   └── woocommerce.php          Woo hooks, style dequeue, single‑product hooks, shop filter sidebar
│
├── template-parts/home/
│   ├── hero.php                 Hero slider (from hero_slide CPT, fallback slide)
│   ├── marquee-gold.php         Editable marquee (Customizer)
│   ├── marquee-dark.php         Product line‑up marquee (from product categories)
│   ├── categories.php           Editorial category grid (product_cat + images)
│   ├── trust.php                4 trust badges (static)
│   ├── tradein.php              Trade‑In section (Customizer)
│   ├── reviews.php              Testimonials (CPT, fallback samples)
│   └── delivery.php             Delivery & Payment (Customizer)
│
├── woocommerce/
│   ├── archive-product.php      Custom shop: light header + LEFT filter sidebar + grid + pagination
│   └── content-product.php      Loop item → shared product card
│
├── assets/
│   ├── css/theme.css            Design system + all non‑commerce components
│   ├── css/woocommerce.css      Owns ALL commerce styling (Woo defaults are disabled)
│   ├── js/main.js               Slider, off‑canvas, carousels, filters, qty steppers, variation swatches
│   └── images/                  logo.png, apple-logo.png (theme defaults)
│
├── HANDOVER.md                  ← this file
└── docs/                        Developer reference (skill doc, etc.)
```

---

## 3. Design system

Defined as CSS custom properties at the top of `assets/css/theme.css` (`:root`).

- **Fonts:** Outfit (display + body). Weights 400–900.
- **Colour:** OKLCH palette anchored on brand **navy** `oklch(26% 0.085 232)` (#113D5B) and
  **gold** `oklch(72% 0.140 75)` (#D4A83A). Light mode only.
- **Container:** `--maxw: 1400px` with a responsive `--gutter` (1.5rem → 1.25 → 1rem). Every
  full‑width section uses this so content edges align across nav, hero, sections, and footer.
- **Tokens:** radii, shadows, easing, and text scales are all variables — change them in one place.

> All colours and fonts are referenced via tokens. There are no hard‑coded hex/OKLCH values
> scattered through component CSS.

---

## 4. What the client can edit (and where)

| Area | Edited in | Notes |
| --- | --- | --- |
| **Products / prices / variations** | Products (WooCommerce) | Variable products. The shop & product rows read these. |
| **Best Sellers / New Arrivals / Deals** | *Nothing* — automatic | Best = top sellers, New = most recent, Deals = on‑sale. They self‑populate. |
| **Categories grid** | Products → Categories | Uses each category's image + product count. |
| **Hero slides** | "Hero Slides" menu | One slide per entry. Featured image = background. Wrap a word in `*asterisks*` in the title for gold. Order via Page Attributes. |
| **Testimonials** | "Testimonials" menu | Review text = editor; reviewer name/location/rating = side box. Falls back to samples if none. |
| **Trade‑In / Delivery / Marquee / Socials / Contact / Section titles** | Appearance → Customize → **IphoneBayKE Options** | |
| **Menus (with dropdowns)** | Appearance → Menus → **Primary** | Desktop dropdowns + mobile accordion read the same menu. Footer has 3 optional menu locations. |
| **Logo** | Appearance → Customize → Site Identity | Falls back to `assets/images/logo.png`, then a text logo. |

Nothing in templates is hard‑coded that the client is expected to change.

---

## 5. Product attributes the theme relies on

The theme reads these **global** product attributes (Products → Attributes):

| Attribute (taxonomy) | Used for |
| --- | --- |
| `pa_condition` (Ex‑UK / New / Refurbished) | Product‑card **badge**, single‑product badge, shop **filter** |
| `pa_storage` (64GB, 128GB, …) | Product‑card meta line, shop **filter**, single‑product **swatch** |
| `pa_colour` | Single‑product colour **swatch** (dots) |

Colour dots map term names → hex via `iphonebay_colour_map()` in `inc/woocommerce.php`
(filterable with `iphonebay_colour_map`). Unmapped colours fall back to grey — add new ones there.

---

## 6. WooCommerce: how it's customised

The guiding principle: **own the markup and the CSS; keep WooCommerce's functionality.**
Three layers are used, lightest first:

1. **CSS ownership.** WooCommerce's three default stylesheets are disabled
   (`add_filter('woocommerce_enqueue_styles','__return_empty_array')`) so nothing fights our
   design. Everything commerce — single product, cart, checkout, my‑account, notices, star
   ratings (rebuilt with unicode ★) — is styled in `assets/css/woocommerce.css`.
2. **Hooks.** The single‑product layout is achieved with hooks, *not* a template override:
   - condition badge → `woocommerce_single_product_summary` priority 4
   - trust grid → priority 35
   - content wrappers, breadcrumb defaults, per‑page/columns, related count → `inc/woocommerce.php`
3. **Template overrides** (only where structure must change): `woocommerce/archive-product.php`
   (left‑sidebar shop) and `woocommerce/content-product.php` (our product card in loops).

**Variation pickers are custom swatches** (pills + colour dots) built by `assets/js/main.js`,
which progressively enhances WooCommerce's `.variations_form` `<select>`s. Clicking a swatch sets
the hidden select and fires Woo's `change` event, so price/image/add‑to‑cart stay 100% native.
Invalid combinations auto‑disable. **Quantity steppers** (+/−) are injected on all `.quantity`
inputs.

> Cart and checkout are **not** rebuilt as standalone pages — that would discard WooCommerce's
> session/AJAX/shipping/payment logic. We restyle them; Woo keeps the brains.

---

## 7. Homepage anatomy (`front-page.php`)

Hero slider → gold marquee → category grid → **Best Sellers** carousel → trust strip →
**New Arrivals** carousel → dark marquee → **Deals & Offers** carousel → Trade‑In → Reviews →
Delivery & Payment → footer.

`front-page.php` is used for the site front page automatically (template hierarchy), regardless
of the Reading‑settings front‑page choice.

---

## 8. JavaScript (`assets/js/main.js`)

Vanilla JS (jQuery only for the swatch enhancer, which must hook Woo's jQuery events):

- Hero crossfade slider (autoplay, dots, swipe, pause on hover)
- Nav transparent→solid on scroll (home only)
- Off‑canvas mobile menu + submenu accordion
- Product carousels with prev/next arrows that **disable** at the ends
- Shop filter drawer (mobile) + converting checkbox filters into WooCommerce layered‑nav
  comma params on submit
- Quantity steppers
- Variation swatch enhancer (reads colour map from `IphoneBayData.colours`)

---

## 9. Catalog migration (record)

The catalog was cloned from the previous site (`iphonebayke` DB / `iphonebayke.test`) into this
site (`iphonebaycustom`):

1. Backed up the new DB.
2. `mysql < old.sql` (note: `wp db import` failed on a `SOURCE` quirk — use the mysql client).
3. `wp search-replace iphonebayke.test iphonebaycustom.test --all-tables`.
4. `rsync` of `wp-content/uploads`.
5. Activated `iphonebay` theme; trimmed active plugins to WooCommerce only.
6. `wp wc update`; flushed rewrites.
7. Cleaned duplicate Shop/Cart/Checkout/My‑account pages so slugs are clean.

**Result:** 5 variable iPhone products (11/12/13/14/15), 38 variations, attributes intact.
Store currency is **KSh**. Admin login now uses the **old site's** user accounts.

---

## 10. Fresh‑install checklist (new environment)

1. Install + activate **WooCommerce**.
2. Activate the **IphoneBayKE** theme.
3. Create global attributes **Condition**, **Storage**, **Colour** (+ terms).
4. Add products as **Variable** products using those attributes; upload images.
5. Set **category images** (Products → Categories).
6. Add a few **Hero Slides** and build the **Primary** menu.
7. Fill **Appearance → Customize → IphoneBayKE Options**.
8. (Optional) Assign footer menus to the 3 footer locations.

---

## 11. Maintenance notes & gotchas

- **Updating WooCommerce:** template overrides live in `woocommerce/`. After a major Woo update,
  check the "Status → Templates" screen for any override flagged as outdated and diff if needed
  (only `archive-product.php` and `content-product.php` are overridden — both are simple).
- **Star ratings** depend on our unicode CSS (Woo's star font is disabled). If ratings look off,
  check `.star-rating` rules in `woocommerce.css`.
- **A new colour shows a grey dot** → add the term name → hex in `iphonebay_colour_map()`.
- **Bumped a CSS/JS file but no change in browser** → `IPHONEBAY_VERSION` in `functions.php`
  is the cache‑buster; bump it on release.
- **Shop filters** use WooCommerce's native layered nav (`filter_condition`, `filter_storage`,
  `min_price`/`max_price`) — no filter plugin required.

---

## 12. Known TODO / future enhancements

- **Demo/seed content** importer (not shipped — catalog was migrated instead).
- **"Buy Now → checkout"** button on single product (mockup had it; not yet wired).
- **Deep Elementor sync** — global colours/fonts are not yet pushed into Elementor's Site
  Settings; only basic compatibility + the Full‑Width template are in place.
- **Custom Elementor widgets** for hero/carousel/trade‑in (if the client wants to rearrange the
  homepage themselves).
- Front page option still points at an old draft page; harmless (front-page.php overrides) but
  could be tidied to a clean static "Home".

---

## 13. Verifying changes (developer)

```bash
SITE=~/Sites/iphonebaycustom

# Lint every PHP file
find $SITE/wp-content/themes/iphonebay -name '*.php' -exec php -l {} \; | grep -v "No syntax errors"

# Smoke‑test key pages (expect 200; checkout 302 when cart empty; 0 fatals)
for u in / /shop/ /product/<slug>/ /cart/ /my-account/; do
  curl -sS -o /tmp/p.html -w "%{http_code} $u\n" "http://iphonebaycustom.test$u"
  grep -ciE "Fatal error|critical error" /tmp/p.html
done
```

See `docs/SKILL.md` for the full methodology behind this theme.
