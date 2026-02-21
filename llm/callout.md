# ACF Callout Block — LLM Prompt

Create a styled callout/notice box using the ACF Callout block. This block uses InnerBlocks for content, so it wraps around standard WordPress blocks (paragraphs, lists, etc.).

## Block Info

- **Block Name:** `acf/callout`
- **Description:** Display a styled callout box with customizable content using core blocks.
- **Styles:** Default, Dark, Testimonial, Dashed Light, Dashed Dark, Highlight

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_callout_label` | Label | text | Optional label text (e.g. "Note", "Warning", "Tip") |
| `field_callout_label_position` | Label Position | select | `top` or `bottom` |
| `field_callout_iconImage` | Icon Image | image | Optional icon/image for the callout |
| `field_callout_bgColor` | Background Color | color_picker | Custom background color |
| `field_callout_textColor` | Text Color | color_picker | Custom text color |
| `field_callout_borderColor` | Border Color | color_picker | Custom border color |

## Style Variations

- `is-style-default` — Light background, standard border
- `is-style-dark` — Dark background with light text
- `is-style-testimonial` — Quote-style callout
- `is-style-dashed-light` — Light with dashed border
- `is-style-dashed-dark` — Dark with dashed border
- `is-style-highlight` — Highlighted/emphasized callout

## Field Rules

- All keys use `field_` prefix
- This block uses **InnerBlocks** — content goes as nested WordPress blocks, not in ACF fields
- Color fields are optional; the style variation handles default colors
- Label field is optional; useful for categorizing callouts (Tip, Warning, Note, etc.)

## Instructions

1. Choose an appropriate style variation for the callout type
2. Set a label if needed (e.g. "Pro Tip", "Warning", "Important")
3. Place standard WordPress blocks (paragraphs, lists) inside as InnerBlocks content
4. Optionally customize colors to match the theme
5. Output the block as a WordPress block comment with inner content

## Example — Default with label

```html
<!-- wp:acf/callout {"name":"acf/callout","data":{"field_callout_label":"Pro Tip","field_callout_label_position":"top"},"mode":"preview"} -->
<p>Always back up your WordPress site before running major updates. Use a plugin like UpdraftPlus or your hosting provider's backup tool to create a full snapshot.</p>
<!-- /wp:acf/callout -->
```

## Example — Dark style

```html
<!-- wp:acf/callout {"name":"acf/callout","data":{"field_callout_label":"Warning","field_callout_label_position":"top"},"className":"is-style-dark","mode":"preview"} -->
<p>This action is irreversible. Once you delete your account, all your data will be permanently removed and cannot be recovered.</p>
<!-- /wp:acf/callout -->
```

## Example — Highlight style with custom colors

```html
<!-- wp:acf/callout {"name":"acf/callout","data":{"field_callout_label":"Important","field_callout_label_position":"top","field_callout_bgColor":"#fef3c7","field_callout_textColor":"#92400e","field_callout_borderColor":"#f59e0b"},"className":"is-style-highlight","mode":"preview"} -->
<p>Your subscription expires in 3 days. <a href="/pricing">Renew now</a> to avoid service interruption.</p>
<!-- /wp:acf/callout -->
```
