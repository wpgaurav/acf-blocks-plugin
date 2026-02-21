# ACF Checklist Block — LLM Prompt

Create an interactive checklist block with checkable items, progress tracking, and customizable styling.

## Block Info

- **Block Name:** `acf/checklist`
- **Description:** Display an interactive checklist with customizable items and styling.
- **Styles:** Default, Card (`is-style-card`), Minimal (`is-style-minimal`)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_checklist_title` | Title | text | Optional heading for the checklist |
| `field_checklist_items` | Checklist Items | repeater | List of checklist items |
| — `field_checklist_item_text` | Item Text | text | Required. The checklist item label |
| — `field_checklist_item_checked` | Pre-checked | true_false | `"1"` if item starts checked |
| `field_checklist_interactive` | Interactive | true_false | `"1"` to allow users to check/uncheck items |
| `field_checklist_show_progress` | Show Progress | true_false | `"1"` to display progress bar |
| `field_checklist_strikethrough` | Strikethrough | true_false | `"1"` to strike through completed items |
| `field_checklist_accent_color` | Accent Color | color_picker | Checkbox/progress bar color |
| `field_checklist_bg_color` | Background Color | color_picker | Block background color |

## Field Rules

- All keys use `field_` prefix
- Repeaters use nested `row-N` objects
- Interactive mode persists checkbox state in localStorage
- Progress bar shows completion percentage based on checked items
- Pre-checked items appear checked on initial load

## Instructions

1. Add a descriptive title for the checklist
2. List items in a logical order (e.g. step-by-step)
3. Pre-check items that are already completed (if applicable)
4. Enable interactive mode for user-actionable checklists
5. Enable progress tracking for multi-step processes
6. Output the block as a WordPress block comment

## Example — Interactive checklist with progress

```html
<!-- wp:acf/checklist {"name":"acf/checklist","data":{"field_checklist_title":"WordPress Launch Checklist","field_checklist_items":{"row-0":{"field_checklist_item_text":"Install SSL certificate","field_checklist_item_checked":"1"},"row-1":{"field_checklist_item_text":"Configure permalink structure","field_checklist_item_checked":"1"},"row-2":{"field_checklist_item_text":"Install essential plugins (SEO, caching, security)","field_checklist_item_checked":"0"},"row-3":{"field_checklist_item_text":"Set up contact form and test submission","field_checklist_item_checked":"0"},"row-4":{"field_checklist_item_text":"Create XML sitemap and submit to Google Search Console","field_checklist_item_checked":"0"},"row-5":{"field_checklist_item_text":"Test site speed and optimize images","field_checklist_item_checked":"0"},"row-6":{"field_checklist_item_text":"Set up automated backups","field_checklist_item_checked":"0"},"row-7":{"field_checklist_item_text":"Review and publish privacy policy","field_checklist_item_checked":"0"}},"field_checklist_interactive":"1","field_checklist_show_progress":"1","field_checklist_strikethrough":"1"}} /-->
```

## Example — Card style, non-interactive

```html
<!-- wp:acf/checklist {"name":"acf/checklist","data":{"field_checklist_title":"What's Included","field_checklist_items":{"row-0":{"field_checklist_item_text":"50GB SSD Storage","field_checklist_item_checked":"1"},"row-1":{"field_checklist_item_text":"Free SSL Certificate","field_checklist_item_checked":"1"},"row-2":{"field_checklist_item_text":"Daily Backups","field_checklist_item_checked":"1"},"row-3":{"field_checklist_item_text":"24/7 Support","field_checklist_item_checked":"1"}},"field_checklist_interactive":"0","field_checklist_show_progress":"0","field_checklist_strikethrough":"0"},"className":"is-style-card"} /-->
```
