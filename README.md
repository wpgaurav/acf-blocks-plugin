# ACF Blocks Collection

 

A comprehensive collection of reusable, customizable Advanced Custom Fields (ACF) blocks for WordPress using ACF Block Version 3 with block.json registration.

 

## Features

 

- **Modern Block Registration**: Uses ACF Block v3 with block.json metadata

- **Auto-loading System**: Intelligent block loader that automatically registers all blocks from subdirectories

- **Modular Architecture**: Each block is self-contained with its own template, styles, and configuration

- **Extensible**: Optional `extra.php` file support for per-block hooks and custom functionality

- **Legacy Support**: Fallback to legacy PHP registration for backwards compatibility

- **Production Ready**: Comprehensive collection of commonly needed blocks for WordPress sites

 

## Requirements

 

- WordPress 5.8+ (for block.json support)

- PHP 7.4+

- [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/) 6.0+ (for ACF Block v3 support)

 

## Installation

 

### Method 1: Direct Installation

 

1. Clone this repository into your theme's directory:

   ```bash

   cd wp-content/themes/your-theme/

   git clone https://github.com/yourusername/acf-blocks-blocks.git blocks

   ```

 

2. Include the loader in your theme's `functions.php`:

   ```php

   require_once get_stylesheet_directory() . '/blocks/functions.php';

   ```

 

### Method 2: Custom Location

 

If you prefer a different directory structure, you can customize the blocks directory using the filter:

 

```php

add_filter( 'md/acf_blocks_dir', function( $blocks_dir ) {

    return get_stylesheet_directory() . '/custom-blocks-path/';

} );

```

 

## Block Structure

 

Each block follows a consistent directory structure:

 

```

block-name/

├── block.json          # Block metadata and configuration (required)

├── block-name.php      # Block template/render file (required)

├── block-name.css      # Block styles (optional)

└── extra.php           # Additional hooks/helpers (optional, auto-loaded)

```

 

### Block Registration

 

Blocks are automatically registered using the `load_acf_blocks_from_json()` function, which:

 

1. Scans the blocks directory for subdirectories

2. Looks for `block.json` in each subdirectory

3. Registers the block using WordPress's `register_block_type()`

4. Auto-loads any `extra.php` file for additional functionality

5. Falls back to legacy `block.php` if `block.json` is not found

 

## Available Blocks

 

### Core Content Blocks

 

- **Hero Block** (`acf/hero`) - Customizable hero section with headline, subheadline, image, and CTA

- **CTA Block** (`acf/cta`) - Call-to-action block with heading, description, and button

- **Testimonial Block** (`acf/testimonial`) - Customer testimonials with quote, author, and rating

- **FAQ Block** (`acf/faq`) - Frequently asked questions with accordion-style display

- **Team Member Block** (`acf/team-member`) - Team member profiles with photo and details

 

### Media & Display Blocks

 

- **Gallery Block** (`acf/gallery`) - Customizable image gallery

- **Video Block** (`acf/video`) - Video embed block with various player options

- **Stats Block** (`acf/stats`) - Display statistics and numbers prominently

 

### Layout & Organization Blocks

 

- **Tabs Block** (`acf/tabs`) - Tabbed content organization

- **Feature Grid Block** (`acf/feature-grid`) - Grid layout for features or services

- **Accordion Block** - Collapsible content sections

- **Section Block** - General purpose section wrapper

 

### Specialized Blocks

 

- **Product Review** - Product review display with ratings

- **Product Cards** - Product showcase cards

- **Product Box** - Individual product display

- **Compare Block** - Side-by-side comparison tool

- **Coupon Code** - Display promotional codes

- **Email Form** - Email capture forms

- **Post Display** - Custom post displays

- **Callout** - Highlighted callout boxes

- **Opinion Box** - Opinion or editorial content

- **Thread Builder** - Discussion thread layouts

- **PL Block** - Pros and cons list display

- **Star Rating Block** (`acf/star-rating`) - Collect visitor star ratings and display the aggregate score

 

## Usage

 

### Creating a New Block

 

1. Create a new directory in the `blocks` folder:

   ```bash

   mkdir blocks/my-custom-block

   ```

 

2. Create a `block.json` file:

   ```json

   {

     "apiVersion": 3,

     "name": "acf/my-custom-block",

     "title": "My Custom Block",

     "description": "Description of what this block does",

     "category": "common",

     "icon": "admin-post",

     "keywords": ["custom", "example"],

     "acf": {

       "renderTemplate": "my-custom-block.php",

       "blockVersion": 3

     },

     "supports": {

       "align": true,

       "mode": true,

       "jsx": true

     },

     "style": [

       "file:./my-custom-block.css"

     ]

   }

   ```

 

3. Create the render template `my-custom-block.php`:

   ```php

   <?php

   /**

    * My Custom Block Template.

    */

 

   $field_value = get_field( 'field_name' );

   ?>

 

   <div class="my-custom-block">

       <?php echo esc_html( $field_value ); ?>

   </div>

   ```

 

4. (Optional) Create an `extra.php` file for additional functionality:

   ```php

   <?php

   // Add custom hooks, filters, or helper functions here

   // This file is automatically loaded when the block is registered

   ```

 

The block will be automatically registered on the next page load!

 

### Defining Custom Fields

 

Use the ACF field group editor to create custom fields for your blocks. Match the field group location rule to your block name (e.g., `Block is equal to My Custom Block`).

 

## Development

 

### Debug Mode

 

Enable WordPress debug mode to see detailed error messages during block registration:

 

```php

define( 'WP_DEBUG', true );

define( 'WP_DEBUG_LOG', true );

```

 

Failed block registrations and missing files will be logged to `wp-content/debug.log`.

 

### Block Version

 

All blocks use ACF Block Version 3, which provides:

- Native block.json support

- Better performance

- Improved editor experience

- Full site editing compatibility

 

## Customization

 

### Modifying Existing Blocks

 

Each block is self-contained, so you can:

- Edit the template file to change the HTML output

- Modify the CSS file to adjust styling

- Update the block.json to change block settings

- Add an extra.php file for custom functionality

 

### Styling Blocks

 

Each block includes its own CSS file. You can:

- Edit the block's CSS file directly

- Override styles in your theme's main stylesheet

- Use WordPress block editor styles

 

## Contributing

 

Contributions are welcome! To add a new block:

 

1. Fork the repository

2. Create a new branch for your block

3. Follow the block structure guidelines

4. Test thoroughly in the block editor

5. Submit a pull request

 

## License

 

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

 

## Support

 

For issues, questions, or contributions, please [open an issue](https://github.com/yourusername/acf-blocks-blocks/issues) on GitHub.

 

## Credits

 

Built with [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/) and WordPress block editor standards.
