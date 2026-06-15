---
name: woocommerce-custom-theme
description: >
  Build bespoke, brand-controlled WordPress + WooCommerce themes from scratch and override
  WooCommerce's native templates and styles cleanly. Use when designing/building a custom
  Woo storefront theme, restyling single-product/cart/checkout/my-account, replacing Woo's
  default CSS, adding variation swatches, building editable content without ACF, or migrating
  a catalog between installs. Encodes the approach used to build the IphoneBayKE theme.
---

# Building custom WooCommerce themes (own the design, keep Woo's brains)

The whole job comes down to one principle:

> **Own the markup and the CSS. Keep WooCommerce's functionality.**
> You are never "at WooCommerce's mercy" — every front-end page comes from a template you can
> override and a stylesheet you can replace. But you should *never* rebuild cart/checkout as
> standalone pages, because their session/AJAX/shipping/payment/nonce logic is tied to Woo's
> template tags and hooks. Restyle them; don't reinvent them.

## The override decision tree (lightest tool first)

For any WooCommerce screen that looks wrong, pick the **lightest** layer that does the job:

1. **CSS** — restyle the existing markup. Default choice. ~80% of the work.
2. **Hooks** (`add_action` / `remove_action`) — reorder or inject pieces without copying a
   template. Use for "move the badge above the title", "add a trust strip after add-to-cart".
3. **Template override** — copy the Woo template into `your-theme/woocommerce/...` and edit the
   HTML. Only when structure genuinely must change (e.g. shop archive with a left sidebar). Each
   override is a maintenance liability after Woo updates, so keep them few and simple.

Never fork cart/checkout wholesale. Override individual sub-templates if you must, but keep the
WooCommerce function calls intact.

---

## 1. Theme scaffold

```
theme/
├── style.css                 # theme header ONLY (enqueue real CSS separately)
├── functions.php             # setup, enqueue, menus, image sizes, require inc/*
├── header.php  footer.php
├── front-page.php            # auto-used for the site front page
├── page.php single.php index.php archive.php search.php 404.php comments.php
├── template-<name>.php       # "Template Name:" page templates (e.g. full-width for builders)
├── inc/
│   ├── post-types.php        # CPTs + native meta boxes (no ACF)
│   ├── customizer.php        # editable global content + a get-option helper
│   ├── template-functions.php# card renderer, product queries, partial helpers
│   ├── class-*-nav-walker.php# custom menu walkers
│   └── woocommerce.php        # ALL Woo hooks/filters (loaded only if Woo active)
├── template-parts/...        # get_template_part() fragments
├── woocommerce/              # template OVERRIDES (mirror Woo's folder structure)
│   ├── archive-product.php
│   └── content-product.php
└── assets/{css,js,images}/
```

### functions.php essentials

```php
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    register_nav_menus( array( 'primary' => 'Primary Menu' ) );
    add_image_size( 'theme_card', 600, 630, true );
} );

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'theme-fonts', 'https://fonts.googleapis.com/css2?...&display=swap', array(), null );
    wp_enqueue_style( 'theme-base', get_stylesheet_uri(), array(), THEME_VER );
    wp_enqueue_style( 'theme-main', get_template_directory_uri() . '/assets/css/theme.css', array( 'theme-base' ), THEME_VER );
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_style( 'theme-woo', get_template_directory_uri() . '/assets/css/woocommerce.css', array( 'theme-main' ), THEME_VER );
    }
    // jQuery dep is required if you progressively enhance Woo's variation form.
    wp_enqueue_script( 'theme-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), THEME_VER, true );
} );

// Load Woo integration only when active; warn if not.
if ( class_exists( 'WooCommerce' ) ) {
    require get_template_directory() . '/inc/woocommerce.php';
}
```

Use a single `THEME_VER` constant as the cache-buster; bump it on release.

---

## 2. Overriding native WooCommerce styles

WooCommerce ships **three** stylesheets: `woocommerce-layout`, `woocommerce-smallscreen`,
`woocommerce-general` (the visual skin). For a fully bespoke theme, disable all three and own
everything:

```php
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
```

**What this breaks — and that you must now restyle yourself:**

- **Star ratings.** Woo's `.star-rating` relies on a `@font-face` star glyph in the general
  stylesheet. Rebuild with unicode:

  ```css
  .woocommerce .star-rating { position: relative; width: 5.4em; height: 1.2em; overflow: hidden; font-family: inherit; }
  .woocommerce .star-rating::before { content: "★★★★★"; color: #ccc; position: absolute; left: 0; }
  .woocommerce .star-rating span { position: absolute; left: 0; overflow: hidden; }
  .woocommerce .star-rating span::before { content: "★★★★★"; color: var(--gold); }
  ```

- **Product gallery (flexslider) layout**, **quantity inputs**, **buttons**, **notices**,
  **forms/select2**, **cart table**, **checkout columns**, **my-account nav**, **tabs**,
  **layered-nav widgets**. Style each yourself. (select2 CSS is a *separate* handle and stays.)

- **Responsive cart**: Woo's smallscreen stylesheet stacked the cart table into cards on mobile.
  Recreate it:

  ```css
  @media (max-width: 768px) {
    .woocommerce-cart table.cart thead { display: none; }
    .woocommerce-cart table.cart tr, .woocommerce-cart table.cart td { display: block; width: 100%; }
    .woocommerce-cart table.cart td::before { content: attr(data-title); font-weight: 700; }
  }
  ```

Keep all commerce CSS in one `woocommerce.css`, enqueued *after* the base theme CSS so it wins.

### Single-product layout via hooks (not a template override)

The default `content-single-product.php` already gives a gallery-left / summary-right structure.
Make it a 2-column grid in CSS, then inject extras with the summary hook:

```php
add_action( 'woocommerce_single_product_summary', 'theme_single_badge', 4 );   // before title (5)
add_action( 'woocommerce_single_product_summary', 'theme_single_trust', 35 );  // after add-to-cart (30)
```

Summary hook priorities to know: title 5, rating 10, price 10, excerpt 20, add-to-cart 30,
meta 40. Reorder with `remove_action` + `add_action` rather than overriding the template.

### Content wrappers

If you want Woo pages inside your own container, swap the default wrappers:

```php
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', fn() => print '<main class="woo-main"><div class="container">', 10 );
add_action( 'woocommerce_after_main_content',  fn() => print '</div></main>', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
```

(The shop archive, if you override `archive-product.php` fully, won't call these — build its own
wrapper there.)

---

## 3. Custom variation swatches (the JS bridge)

Goal: replace Woo's variation `<select>` dropdowns with pills / colour dots **without** touching
add-to-cart logic. Pattern: progressively enhance the existing `.variations_form`.

```js
// jQuery required — Woo fires jQuery events on the form.
jQuery(function ($) {
  $('.variations_form').each(function () {
    var $form = $(this), $selects = $form.find('.variations select');
    if (!$selects.length) return;
    $form.addClass('swatches-active');            // CSS hides the original table.variations

    var groups = [];
    $selects.each(function () {
      var $select = $(this);
      var $ui = $('<div class="swatches"></div>');
      $form.find('.single_variation_wrap').before($ui);
      $select.find('option').each(function () {
        if (!this.value) return;                  // skip "Choose an option"
        $('<button type="button" class="swatch">' + this.textContent + '</button>')
          .attr('data-value', this.value)
          .on('click', function () { $select.val(this.getAttribute('data-value')).trigger('change'); })
          .appendTo($ui);
      });
      groups.push({ $select: $select, $ui: $ui });
    });

    function sync() {                              // reflect Woo's enable/disable + selection
      groups.forEach(function (g) {
        var cur = g.$select.val(), avail = {};
        g.$select.find('option').each(function () { if (this.value && !this.disabled) avail[this.value] = true; });
        g.$ui.find('.swatch').each(function () {
          var v = this.getAttribute('data-value');
          $(this).toggleClass('active', v === cur).toggleClass('disabled', !avail[v]);
        });
      });
    }
    sync();
    $form.on('woocommerce_update_variation_values check_variations found_variation', sync);
    $form.on('reset_data', function () { setTimeout(sync, 10); });
  });
});
```

- **Colour dots:** detect attribute name/label matching `/colou?r/`, render a dot with a
  `background` from a server-provided name→hex map (`wp_localize_script`). Woo swaps the gallery
  image on `found_variation` automatically.
- The hidden `<select>`s must stay in the DOM (Woo reads/writes them); hide via CSS.
- Build buttons from the **initial full** option list; `sync()` disables currently-invalid ones.

CSS gate to hide the native table only when JS ran: `.swatches-active table.variations { display: none; }`.

---

## 4. Shop filters with native layered nav (no plugin)

WooCommerce's `WC_Query` reads `filter_<attribute>` (comma-separated slugs) and
`min_price`/`max_price` GET params on shop/archives — no widget or plugin needed.

- Render a `<form method="get" action="<shop_url>">` sidebar with checkboxes named
  `filter_condition[]`, plus price inputs.
- On submit (JS), **join each checkbox group into one comma value** and disable the `[]` inputs
  so the URL becomes `?filter_condition=ex-uk,new` (the shape Woo expects):

  ```js
  form.addEventListener('submit', function () {
    var g = {};
    form.querySelectorAll('input[name$="[]"]').forEach(function (cb) {
      if (cb.checked) (g[cb.name.slice(0,-2)] = g[cb.name.slice(0,-2)] || []).push(cb.value);
      cb.disabled = true;
    });
    Object.keys(g).forEach(function (n) {
      var h = document.createElement('input'); h.type='hidden'; h.name=n; h.value=g[n].join(','); form.appendChild(h);
    });
  });
  ```

- When reading chosen values server-side, **normalise both shapes** (a comma string from Woo, or
  an array if JS is off) before `explode()` — passing an array to `explode()` is a fatal:

  ```php
  $raw = wp_unslash( $_GET['filter_'.$slug] ?? '' );
  $raw = is_array( $raw ) ? $raw : explode( ',', (string) $raw );
  $chosen = array_filter( array_map( 'sanitize_title', (array) $raw ) );
  ```

---

## 5. Editable content WITHOUT ACF (free route)

Clients want to edit content; you don't need ACF Pro.

- **Repeating items** (hero slides, testimonials) → a **Custom Post Type** with native
  `add_meta_box` fields. One entry per item; ordering via `page-attributes` (menu_order). This is
  the free, native substitute for an ACF repeater.
- **Singular global content** (trade-in copy, marquee, socials, section titles) → the
  **Customizer**, wrapped in a panel. Add a tiny getter: `get_theme_mod('theme_'.$key, $default)`.
- **Auto-populating sections** beat manual fields: "Best Sellers" = top sellers, "New Arrivals" =
  newest, "Deals" = on-sale. Query them so the client never maintains a list:

  ```php
  wc_get_products( array( 'limit' => 8, 'orderby' => 'meta_value_num', 'meta_key' => 'total_sales', 'order' => 'DESC' ) ); // best
  wc_get_products( array( 'limit' => 8, 'orderby' => 'date', 'order' => 'DESC' ) );                                         // new
  wc_get_products( array( 'include' => wc_get_product_ids_on_sale(), 'limit' => 8 ) );                                      // deals
  ```

- **Menus** → custom `Walker_Nav_Menu` subclasses so Appearance → Menus drives both desktop
  dropdowns and the mobile drawer from one menu. Always provide a `fallback_cb` so a fresh install
  isn't empty. Categories can be a mega-dropdown pulled from `product_cat`.

---

## 6. Design system discipline

- Put every colour/radius/shadow/spacing/type-scale value in `:root` as a CSS variable. Reference
  by token everywhere — no stray hex in component rules.
- Use a single container system: `--maxw` + a responsive `--gutter`, applied to nav, hero,
  sections, and footer so left edges align at every breakpoint.
- Mobile floor: no horizontal scroll, no two-line buttons, image grid tracks use
  `minmax(0, 1fr)`, `overflow-x: clip` on root, display headers `word-break: keep-all`.

---

## 7. Gotchas checklist

- `explode()` on a `filter_*[]` array → fatal. Normalise first (§4).
- Disabling Woo CSS kills the **star font** → rebuild with unicode (§2).
- Variation swatches need the **native selects left in the DOM** (hide, don't remove).
- `wp db import` can fail on a `SOURCE` syntax quirk → use the `mysql` client directly.
- A grid that shows exactly N items and has exactly N products won't scroll — seed enough demo
  data when testing carousels.
- Mobile filter **backdrop** must live *outside* the CSS grid, or on desktop it eats a grid cell
  and breaks the layout.
- Bump the cache-buster version after editing CSS/JS.

---

## 8. Verify every change (don't trust, check)

```bash
SITE=~/Sites/<site>
# 1. Lint all PHP
find $SITE/wp-content/themes/<theme> -name '*.php' -exec php -l {} \; | grep -v "No syntax errors"
# 2. HTTP smoke test (expect 200; checkout 302 on empty cart; 0 fatals)
for u in / /shop/ /product/<slug>/ /cart/ /checkout/ /my-account/; do
  curl -sS -o /tmp/p.html -w "%{http_code} $u\n" "http://<site>.test$u"
  grep -ciE "Fatal error|critical error" /tmp/p.html
done
# 3. Confirm Woo default CSS is gone, ours is present
curl -sS "http://<site>.test/product/<slug>/" | grep -c "woocommerce-layout"   # expect 0
```

JS-built UI (swatches, steppers) can't be seen via curl — open the product page in a browser and
exercise add-to-cart + checkout.

---

## 9. Migrating a catalog between installs (DB clone)

When a client already built products/attributes/variations elsewhere, cloning the DB is the most
faithful copy (variations, galleries, attribute terms, category images, WC pages all intact):

```bash
OLD=~/Sites/old; NEW=~/Sites/new
wp --path=$NEW db export /tmp/new-backup.sql                 # safety net
wp --path=$OLD db export /tmp/old.sql
wp --path=$NEW db reset --yes
mysql -u root -p<pw> <new_db> < /tmp/old.sql                 # mysql client, not `wp db import`
wp --path=$NEW search-replace 'old.test' 'new.test' --all-tables --report-changed-only
rsync -a $OLD/wp-content/uploads/ $NEW/wp-content/uploads/
wp --path=$NEW theme activate <theme>
wp --path=$NEW plugin deactivate --all && wp --path=$NEW plugin activate woocommerce
wp --path=$NEW wc update && wp --path=$NEW rewrite flush
```

Then clean duplicate WC pages (trash strays holding the clean `shop`/`cart`/`checkout`/
`my-account` slugs, reslug the configured ones, flush). Note: admin users become the **old**
site's accounts after a full clone.

---

*Methodology distilled from the IphoneBayKE theme build. To activate this as a live Claude skill,
copy it to `~/.claude/skills/woocommerce-custom-theme/SKILL.md`.*
