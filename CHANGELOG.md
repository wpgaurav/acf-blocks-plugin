# Changelog

All notable changes to the ACF Blocks plugin are documented here.

## [2.8.1] - 2026-06-23

### Added — Migrator improvements
- **Batched migration:** the Migrator now processes **30 posts per run** with a **Continue — Migrate Next 30** button and a progress bar, instead of one large request — friendlier on big sites and PHP time limits.
- **Affected-post visibility:** **Scan** now lists every affected post (title, status, edit/view links) with colour-coded badges showing exactly what will change; each migration batch shows the same per-post detail for what it just changed.
- **New repair — legacy accordion field schema:** `acf/accordion` blocks whose data used the old `acf_accord_heading` / `acf_accord_content` sub-fields (which render blank on the current template) are remapped in place to `acf_accord_group_title` / `acf_accord_group_content`, preserving FAQ schema and classes. This is the cause behind blank FAQ sections on older posts.
- **Restore points across batches:** revert now covers the whole multi-batch session, and a new **Discard restore points** action clears backups without changing migrated content. WP-CLI: `wp acf-blocks migrate [--limit=<n>] [--dry-run] [--revert] [--discard]`.

## [2.8.0] - 2026-06-23

### Fixed
- **Block recovery (all InnerBlocks blocks):** Resolved the "This block contains unexpected or invalid content" / **Attempt Recovery** error that emptied ACF InnerBlocks blocks (Callout, CTA, Hero, Section, Feature Grid, Testimonial, Team Member, Opinion Box, …). The cause was inner content saved as undelimited raw HTML (e.g. a bare `<p>` instead of `<!-- wp:paragraph -->`), which the editor treats as invalid InnerBlocks markup. Clicking *Attempt Recovery* then rebuilt the block from its template default, wiping the author's content.

### Added
- **`includes/block-recovery.php`** — a self-healing helper that re-wraps orphaned inner HTML into proper core blocks (paragraph, heading, list, quote; anything unrecognised is preserved verbatim in a `core/html` block, so no content is ever lost). It is idempotent and runs in two ways:
  - **Live self-heal:** filters the block editor's REST `content.raw` (edit context only) so existing posts open cleanly with no action required; saving then persists the repaired markup.
  - **Permanent bulk repair:** `wp acf-blocks repair-content [--dry-run] [--post=<id>]` rewrites affected posts in the database.
- The InnerBlocks block set is derived automatically — unioning each `block.json` (`supports.jsx`) with the live block registry (ACF can enable InnerBlocks at runtime without declaring it in `block.json`) — with a `acf_blocks_recovery_innerblock_names` filter to customise it.

### Added — Block Migrator
- **`includes/block-migrator.php` + visual Migrator on the options page** (Settings → ACF Blocks License). A new **Block Migrator & Repair** card with **Scan**, **Dry Run**, and **Migrate All** actions. Every change is saved through `wp_update_post()` (a revision is stored), so migrations are reversible. WP-CLI parity: `wp acf-blocks migrate [--dry-run]`.
- **Reversible migrations** — before each post is migrated, its original content is snapshotted both as a native WordPress revision (visible in the editor's Revisions browser) and as a per-post restore point. A **Revert Last Migration** button (and `wp acf-blocks migrate --revert`) rolls the entire batch back to its pre-migration content, byte-for-byte, independent of the site's revision settings.
- **Legacy / renamed block migrations** — blocks saved under names the plugin no longer registers are remapped to the current block *with field-data translation*, not just renamed:
  - `acf/table-of-contents`, `acf/table-of-content` → `acf/toc`
  - `acf/productbox` → `acf/product-box` (incl. the legacy features repeater)
  - `acf/accordion-item` → `acf/accordion` (consecutive items merged into one block)
  - `acf/accordion-group` → `acf/accordion` (wrapper + children merged, FAQ schema preserved)
  - `acf/acf-accordion` → `acf/accordion` (`acc_question`/`acc_answer` sub-fields remapped)
  - `acf/poll` has no current equivalent — reported and left untouched for manual handling.
- **Unparseable-markup repair** — fixes content the block parser chokes on:
  - **Orphaned closing delimiters** (e.g. a stray `<!-- /wp:post-content -->` with no opener) that silently push every following block into freeform text. Removed with a delimiter stack so only genuinely unmatched closers are stripped.
  - **Dangling openers** (a truncated `<!-- wp:… ` fragment with no `-->`).
  - Literal `-->` inside ACF block JSON, HTML-encoded to `--&gt;` (identical when rendered).

### Compatibility
- **WordPress 7.0:** Added `Tested up to: 7.0` header. Audited for WordPress 7 / ACF Pro 6.x — all 29 blocks already use Block API v3, register via `block.json` (no deprecated `acf_register_block_type`), retain the ACF 6.7+ `acf_setup_meta` compatibility layer, and contain no PHP 8.2–8.5 deprecation patterns (verified against PHP 8.5).

## [2.6.0] - 2026-04-11

### Changed
- **All blocks:** Every ACF field is now optional. Removed `required: 1` flags and `min: 1` constraints on repeaters/relationships so any block can be inserted without being forced to fill specific fields.

### Removed
- **Chat block:** Completely removed the `acf/chat-block` and its directory (`blocks/chat-block/`).

### Improved
- **LLM prompt library (`llm/`):** Added `llm/README.md` as an index covering all 29 blocks grouped by category, universal rules (single-line JSON, field-key prefix, repeater format, InnerBlocks vs ACF data), and a block-comment anatomy reference. Removed stale "Required" annotations from nine per-block docs now that no fields are required.

## [2.5.9] - 2026-04-03

### Fixed
- **Product Review block:** Fixed 4 Google Search Console structured data errors: missing `price`, `hasMerchantReturnPolicy`, `shippingDetails` in offers, and missing `image` fallback.

### Added
- **Product Review block:** New **Product Type** field — choose between `Product` (physical/generic) and `SoftwareApplication` (apps/SaaS) schema types. SoftwareApplication includes `applicationCategory` and `operatingSystem` fields.
- **Product Review block:** New **Return Policy** field with options: No Returns (digital), Finite Return Window (with configurable days), Unlimited Returns. Maps to `hasMerchantReturnPolicy` in offers schema.
- **Product Review block:** New **Delivery Type** field — Digital (instant/$0 shipping) or Physical. Digital products get zero-cost instant delivery in `shippingDetails`.
- **Product Review block:** New **Shipping Country** field (ISO country code) used for both shipping destination and return policy region.
- **Product Review block:** Image now falls back to post featured image when no product image is set, preventing GSC "Missing field image" errors.
- **Product Review block:** Offers schema is now always generated (price defaults to `"0"` when empty) to prevent GSC missing field warnings.

## [2.2.9] - 2026-03-09

### Fixed
- **Callout block:** Block appeared blank when inserted — no InnerBlocks template was provided. Added default heading + paragraph template matching the CTA/Hero pattern. Also added `uniqid()` fallback for block ID.
- **Compare block:** Backward-compat code fetched old `comp_list_class` sub-field but template always read `comp_list_content`, silently dropping feature lists from older blocks. Old field values are now mapped to `comp_list_content`.
- **Compare block:** Added empty-state preview message when no columns are configured (consistent with accordion/tabs blocks).
- **Compare block:** Cleaned up inline PHP for title styles and CTA button attributes for better readability.

## [2.2.8] - 2026-03-09

### Added
- **Product Box — Top Image variation:** New `is-style-top-image` block style shows image above content cropped to 16:9 aspect ratio. New `product-box-wide` (800x450) image size registered for this variation.

### Fixed
- **Critical:** Section block custom CSS never rendered on frontend — `md_store_block_css()` stored CSS in a static variable but `md_output_stored_block_css()` read from a global, so the footer `<style>` tag was always empty. Fixed by using a shared global variable.
- **Critical:** Post Display block crashed (fatal error) when ACF compat layer returned raw post IDs instead of WP_Post objects. Now normalizes entries via `get_post()` before rendering.
- **Changelog block:** Nested repeater items (type/description) failed to parse in flat data format with ACF 6.7+. Now falls back to `acf_blocks_get_nested_repeater()` when items aren't already arrays.
- **Email Form block:** Wrong text domain `'gauravtiwari'` replaced with `'acf-blocks'` for translatable strings.
- **Product Review block:** Removed dead `$halfSvg` variable with broken `uniqid()` gradient IDs.

### Improved
- **Product Box design refresh:** Refined color palette (slate scale), softer shadows, larger border-radius, badge shadow, smoother hover transitions, and better dark mode Amazon button contrast.

## [2.2.7] - 2026-02-27

- **Fixed:** priceCurrency fix.

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
