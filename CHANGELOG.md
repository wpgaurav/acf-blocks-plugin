# Changelog

All notable changes to the ACF Blocks plugin are documented here.

## [2.2.6] - 2026-02-26

### Changed
- **Compare Block:** Minimalistic card redesign — removed outer card wrapper, columns are now clean individual cards with 16px radius, hairline borders, and subtle hover. Pill-shaped CTA button with dark neutral default instead of red. Lighter feature-list separators.
- **Product List Block:** Minimalistic card redesign — near-zero resting shadow, hover changes border instead of dramatic shadow lift. Neutral gray pricing/coupon backgrounds instead of blue/green tints. Pill-shaped rank badge and CTA buttons. Dark neutral default button instead of blue.
- **Hero Block:** Minimalistic card redesign — now renders as a proper card with 16px radius, border, and background. Image gets a gradient scrim overlay for text readability. Pill-shaped CTA buttons. Better typography with tighter letter-spacing and improved line-heights. Responsive padding adjustments.
- All three blocks share a consistent design language: 16px outer radius, pill buttons (100px radius), dark neutral default colors, subtle border-color hover transitions, and full dark mode support.

## [2.2.5] - 2026-02-26

### Fixed
- **Critical:** Self-hosted videos never played — ACF compat layer returned raw numeric attachment IDs from `$block['data']`, but the template expected arrays with `url` and `mime_type` keys. Now resolves IDs via `wp_get_attachment_url()` / `get_post_mime_type()`, which also enables CDN URL rewriting.
- **Critical:** Poster image for self-hosted videos had the same numeric ID issue. Now resolved via `acf_blocks_resolve_image()`.
- **Controls toggle ignored:** Strict comparison `$controls !== false` was always truthy for ACF's `0` value. Changed to truthy check.
- **Video cropping:** Changed `object-fit` from `cover` to `contain` for self-hosted `<video>` elements to prevent content cropping.

### Added
- **Self-hosted video URL support:** The Video URL field now appears for self-hosted type, allowing direct/CDN URLs that override the media library file upload.
- **Lazy-load self-hosted videos:** Non-autoplay self-hosted videos defer network requests until near the viewport via IntersectionObserver (200px root margin).
- **Smarter preload:** Self-hosted videos with a poster image and no autoplay use `preload="none"` instead of `preload="metadata"` for faster page loads.

## [2.2.4] - 2026-02-23

### Added
- **Check for Updates button:** License page now shows an Updates card with a "Check for Updates" button (when license is active) that queries the license server and reports whether a new version is available. Mirrors the GT Link Manager implementation.
- Force update check runs automatically after license activation.

## [2.2.3] - 2026-02-23

### Fixed
- **Critical:** Images uploaded via media library showed placeholder SVG instead of actual image in product-box, hero, team-member, testimonial, opinion-box, gallery, and url-preview blocks. The ACF compat layer returns raw attachment IDs from `$block['data']`, but templates expected ACF-formatted arrays. Added shared `acf_blocks_resolve_image()` helper in compat layer that handles arrays, numeric IDs, and URL strings.
- **Product List Block:** Fixed broken repeater sub-field names (`pl_price_label` instead of `pl_block_pricing_title`) that caused pricing, coupons, and buttons to never render.
- **Product List Block:** Fixed unsanitized description output (XSS risk) — now uses `wp_kses_post()`.

### Added
- **Title Heading Level:** Added configurable heading tag (p/h2-h6, default p) to product-box, product-cards, product-review, and pl-block.
- **Product List Block:** Complete overhaul — modern card design with CSS custom properties, container queries, dark mode, rank badge, pricing chips, dashed-border coupon codes, 3-tier button system (primary/secondary/text), accessibility support.
- **Product List Block:** New fields — image URL (external images), product URL (linked name), button style selector, image width override.
- **Shared Image Helper:** `acf_blocks_resolve_image()` in compat layer resolves ACF image values (array, numeric ID, URL) to `{src, alt}` with size support.

### Changed
- Product-cards title now wrapped in configurable HTML tag instead of bare text in header div.

## [2.1.8] - 2026-02-22

### Fixed
- **Critical:** Prioritize `$block['data']` over `get_field()` in compat layer. ACF 6.7+ `get_field()` was intercepting `field_` prefixed keys and returning empty values, causing pros-cons and accordion blocks to render empty shells on review CPTs.
- Extracted `acf_blocks_cast_field_value()` helper to reduce code duplication in repeater type casting.

## [2.1.7] - 2026-02-22

### Fixed
- Add `field_` prefix fallback in `acf_blocks_get_field()` and `acf_blocks_get_repeater()` for review CPTs that store block data with ACF field key format (`field_pc_pros_list` instead of `pc_pros_list`).

## [2.1.6] - 2026-02-20

### Fixed
- Switch license API to POST requests for better compatibility.

## [2.1.5] - 2026-02-19

### Fixed
- Improve compatibility for nested repeaters in block editor.

## [2.1.4] - 2026-02-19

### Fixed
- Enhance compatibility layer for nested repeaters with `acf_blocks_reconstruct_nested_repeaters()` filter.

## [2.1.3] - 2026-02-19

### Fixed
- Fix nested repeater compat and add nested key resolver (`acf_blocks_resolve_nested_key()`).
- Fix `intval()` on array bypassing nested row fallback.

## [2.1.2] - 2026-02-18

### Fixed
- Fix repeater compat layer for nested row format.

## [2.1.1] - 2026-02-18

### Changed
- Remove build script for packaging (handled by GitHub Actions).

## [2.1.0] - 2026-02-17

### Added
- Fluent Cart licensing integration (product ID 1150934).
- Auto-install SCF (Secure Custom Fields) on activation if ACF Pro is not present.

## [2.0.6] - 2026-02-16

### Fixed
- Refine theme overrides to respect host styles in dark mode.
