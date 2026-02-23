# Changelog

All notable changes to the ACF Blocks plugin are documented here.

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
