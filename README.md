# ACF Blocks Plugin

A comprehensive WordPress plugin that provides a collection of reusable, customizable ACF Pro blocks for the block editor. Uses ACF Block Version 3 with automatic field group registration.

## Features

- **Plugin Architecture**: Standalone WordPress plugin - no theme modifications required
- **Automatic Field Groups**: Field groups are registered automatically from JSON - no manual imports needed
- **Modern Block Registration**: Uses ACF Block v3 with block.json metadata
- **Zero-JavaScript Accordions**: Uses native HTML `<details>`/`<summary>` elements for accordion blocks
- **Conditional Asset Loading**: CSS and JS only load when blocks are actually used on the page
- **Modular Architecture**: Each block is self-contained with its own template, styles, and configuration
- **ACF Pro Compatible**: Requires ACF Pro 6.0+ for full functionality

## Requirements

- WordPress 6.0+
- PHP 7.4+
- [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/) 6.0+

## Installation

1. Download the plugin and upload to `wp-content/plugins/acf-blocks-plugin/`
2. Activate the plugin through the WordPress admin
3. Ensure ACF Pro is installed and activated
4. Start using blocks in the block editor!

No additional configuration required - field groups are automatically registered.

## Available Blocks

### Content Blocks
- **Accordion Block** (`acf/accordion`) - Collapsible content with FAQ schema support (searchable by "FAQ")
- **Testimonial Block** (`acf/testimonial`) - Customer testimonials with ratings
- **Hero Block** (`acf/hero`) - Hero section with headline, image, and CTA
- **CTA Block** (`acf/cta`) - Call-to-action with heading and button
- **Callout Block** (`acf/callout`) - Highlighted callout boxes
- **Opinion Box** (`acf/opinion-box`) - Editorial content with author

### Product & E-Commerce
- **Product Review** (`acf/product-review`) - Reviews with star ratings and schema
- **Product Cards** (`acf/product-cards`) - Product showcase cards
- **Product Box** (`acf/product-box`) - Single product display with CTA buttons
- **Coupon Code** (`acf/cb-coupon-code`) - Promotional code display with copy feature
- **Compare Block** (`acf/compare`) - Side-by-side comparisons
- **PL Block** (`acf/pl-block`) - Product lists with pricing

### Media & Display
- **Video Block** (`acf/video`) - Video embeds
- **Gallery Block** (`acf/gallery`) - Image galleries
- **Stats Block** (`acf/stats`) - Statistics display
- **Star Rating** (`acf/star-rating`) - Interactive ratings with AJAX submission

### Navigation & Organization
- **Tabs Block** (`acf/tabs`) - Tabbed content with multiple styles
- **Feature Grid** (`acf/feature-grid`) - Grid layout for features
- **Section Block** (`acf/section-block`) - Container wrapper with InnerBlocks
- **Post Display** (`acf/post-display`) - Custom post listings

### Team & Forms
- **Team Member** (`acf/team-member`) - Team member profiles
- **Email Form** (`acf/email-form`) - Email capture forms
- **Thread Builder** (`acf/thread-builder`) - Discussion thread layouts

## Block Structure

Each block follows a consistent directory structure:

```
blocks/
  block-name/
    ├── block.json          # Block metadata (required)
    ├── block-name.php      # Render template (required)
    ├── block-data.json     # ACF field group (auto-registered)
    ├── block-name.css      # Styles (conditionally loaded)
    └── extra.php           # Additional hooks (optional)
```

## Key Features

### Automatic Field Group Registration

Field groups are defined in JSON files (`block-data.json` or similar) within each block folder. These are automatically registered with ACF Pro when the plugin loads - no need to import/export through the ACF admin.

### Native HTML Accordions

The Accordion block uses native HTML `<details>` and `<summary>` elements instead of JavaScript-based solutions. This provides:
- Zero JavaScript required
- Native keyboard accessibility
- Built-in browser support
- Reduced page weight

### Conditional Asset Loading

CSS files are only enqueued when their respective blocks are used on a page, reducing unnecessary asset loading for better performance.

## Creating Custom Blocks

1. Create a new directory in `blocks/`:
   ```bash
   mkdir blocks/my-block
   ```

2. Create `block.json`:
   ```json
   {
     "apiVersion": 3,
     "name": "acf/my-block",
     "title": "My Block",
     "description": "Description here",
     "category": "common",
     "icon": "admin-post",
     "keywords": ["custom"],
     "acf": {
       "renderTemplate": "my-block.php",
       "blockVersion": 3
     },
     "supports": {
       "align": true,
       "mode": true,
       "jsx": true
     }
   }
   ```

3. Create `block-data.json` with your ACF field group configuration.

4. Create `my-block.php` render template.

5. Optionally add `my-block.css` for styles.

The block will be automatically registered on the next page load.

## CSS Class Naming Convention

All blocks use the `acf-` prefix for CSS classes to avoid conflicts with other plugins and themes. For example:
- `.acf-accordion` - Accordion block container
- `.acf-testimonial-block` - Testimonial block container
- `.acf-hero-block` - Hero block container

This ensures styles are properly scoped and won't interfere with your theme's styling.

## Development

Enable debug mode for detailed error logging:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

## License

GPL v2 or later

## Credits

Built for [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/) and WordPress block editor.
