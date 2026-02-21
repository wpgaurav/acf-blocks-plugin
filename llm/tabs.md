# ACF Tabs Block — LLM Prompt

Create a tabbed content block for organizing information into switchable sections with keyboard accessibility.

## Block Info

- **Block Name:** `acf/tabs`
- **Description:** A tabbed content block for organizing information into switchable sections.
- **Styles:** Default

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `acf_tabs_items` | Tab Items | repeater | List of tabs |
| — `acf_tab_title` | Tab Title | text | Required. Tab label |
| — `acf_tab_icon` | Tab Icon | text | Optional icon class or emoji |
| — `acf_tab_content` | Tab Content | wysiwyg | Content shown when tab is active |
| `acf_tabs_style` | Style | select | `default`, `pills`, `underline`, `boxed` |
| `acf_tabs_class` | Custom Class | text | Optional CSS class |
| `acf_tabs_inline` | Inline Styles | text | Optional inline CSS |

## Tab Styles

| Style | Description |
|---|---|
| `default` | Standard tabbed interface |
| `pills` | Rounded pill-shaped tab buttons |
| `underline` | Underline indicator on active tab |
| `boxed` | Boxed/bordered tab buttons |

## Field Rules

- Field keys use `acf_` prefix (NOT `field_`)
- Repeaters use nested `row-N` objects
- Tab content supports full WYSIWYG HTML
- Tabs include ARIA attributes for keyboard accessibility
- First tab is active by default
- Icon field accepts icon class names or emoji characters

## Instructions

1. Create tabs with clear, concise titles
2. Add content for each tab (supports HTML formatting)
3. Use icons to make tabs more scannable
4. Choose a tab style that fits the page design
5. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/tabs {"name":"acf/tabs","data":{"acf_tabs_items":{"row-0":{"acf_tab_title":"Monthly","acf_tab_icon":"📅","acf_tab_content":"<h3>Monthly Plans</h3>\n<ul>\n<li><strong>Basic:</strong> $9.99/month — 1 site, 10GB storage</li>\n<li><strong>Pro:</strong> $24.99/month — 5 sites, 50GB storage</li>\n<li><strong>Enterprise:</strong> $79.99/month — Unlimited sites, 200GB storage</li>\n</ul>"},"row-1":{"acf_tab_title":"Annual","acf_tab_icon":"💰","acf_tab_content":"<h3>Annual Plans (Save 20%)</h3>\n<ul>\n<li><strong>Basic:</strong> $7.99/month — 1 site, 10GB storage</li>\n<li><strong>Pro:</strong> $19.99/month — 5 sites, 50GB storage</li>\n<li><strong>Enterprise:</strong> $63.99/month — Unlimited sites, 200GB storage</li>\n</ul>"},"row-2":{"acf_tab_title":"Free Trial","acf_tab_icon":"🎁","acf_tab_content":"<h3>14-Day Free Trial</h3>\n<p>Try any plan free for 14 days. No credit card required. Full access to all features.</p>"}},"acf_tabs_style":"pills"}} /-->
```

## Example — Technical documentation tabs

```html
<!-- wp:acf/tabs {"name":"acf/tabs","data":{"acf_tabs_items":{"row-0":{"acf_tab_title":"Installation","acf_tab_content":"<p>Run the following command to install:</p>\n<code>npm install acf-blocks</code>"},"row-1":{"acf_tab_title":"Configuration","acf_tab_content":"<p>Add the following to your <code>functions.php</code>:</p>\n<code>add_theme_support('acf-blocks');</code>"},"row-2":{"acf_tab_title":"Usage","acf_tab_content":"<p>Insert blocks through the WordPress block editor. Search for 'ACF' to find all available blocks.</p>"}},"acf_tabs_style":"underline"}} /-->
```
