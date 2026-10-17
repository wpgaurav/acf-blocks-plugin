# ACF Steps Block — LLM Prompt

Create a numbered sequence such as “how it works”.

## Block Info

- **Block Name:** `acf/steps`
- **Description:** Ordered steps. The number is generated from the row order.
- **Category:** ACF Marketing
- **Styles:** horizontal is the default (no extra class). `is-style-vertical` stacks the steps.

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_acf_steps_heading` | Heading | text | Optional |
| `field_acf_steps_heading_tag` | Heading Tag | select | `h2` (default), `h3`, `h1`, `p` |
| `field_acf_steps_intro` | Introduction | textarea | Optional |
| `field_acf_steps_items` | Steps | repeater | See sub-fields |

Step sub-fields: `step_icon` (emoji or icon class), `step_title`, `step_text`.

## Field Rules

- Field keys use the `field_` prefix.
- **Single-line JSON.** Use `\n` inside `step_text` when a step needs a line break.
- Repeaters use `row-N` objects.
- Every field is optional. Empty rows are skipped and do not consume a number.

## Example

```html
<!-- wp:acf/steps {"name":"acf/steps","data":{"field_acf_steps_heading":"How it works","field_acf_steps_items":{"row-0":{"step_title":"Connect","step_text":"Add the block."},"row-1":{"step_title":"Publish","step_text":"Drop it on a landing page."}}},"mode":"preview"} /-->
```
