# Changelog

All notable changes to the ACF Blocks plugin are documented here.

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
