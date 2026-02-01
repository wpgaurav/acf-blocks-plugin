# Checklist Block — LLM Instructions

Use `<!-- wp:acf/checklist -->` with a JSON `data` attribute. Fields use the `checklist_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `checklist_title` | text | No | Title above the checklist |
| `checklist_items` | repeater object | Yes | Min 1, max 50 items |
| `checklist_interactive` | `"1"` or `"0"` | No | Default `"0"`. Allows users to check/uncheck items |
| `checklist_show_progress` | `"1"` or `"0"` | No | Default `"0"`. Shows progress bar |
| `checklist_strikethrough` | `"1"` or `"0"` | No | Default `"1"`. Strikes through checked items |
| `checklist_accent_color` | color string | No | Default `"#16a34a"`. Checkbox color |
| `checklist_bg_color` | color string | No | Default `"#f9fafb"`. Background color |

### Item Sub-fields

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `checklist_item_text` | text | Yes | The checklist item text |
| `checklist_item_checked` | `"1"` or `"0"` | No | Default `"0"`. Pre-checked state |

## Repeater Format

```json
"checklist_items": {
  "row-0": {"checklist_item_text": "Set up hosting account", "checklist_item_checked": "1"},
  "row-1": {"checklist_item_text": "Install WordPress", "checklist_item_checked": "1"},
  "row-2": {"checklist_item_text": "Configure SSL certificate", "checklist_item_checked": "0"},
  "row-3": {"checklist_item_text": "Install essential plugins", "checklist_item_checked": "0"}
}
```

## Example

```html
<!-- wp:acf/checklist {"name":"acf/checklist","data":{"checklist_title":"WordPress Launch Checklist","checklist_items":{"row-0":{"checklist_item_text":"Set up hosting account","checklist_item_checked":"1"},"row-1":{"checklist_item_text":"Install WordPress","checklist_item_checked":"1"},"row-2":{"checklist_item_text":"Configure SSL certificate","checklist_item_checked":"0"},"row-3":{"checklist_item_text":"Install essential plugins","checklist_item_checked":"0"},"row-4":{"checklist_item_text":"Set up backups","checklist_item_checked":"0"}},"checklist_interactive":"1","checklist_show_progress":"1","checklist_strikethrough":"1","checklist_accent_color":"#16a34a","checklist_bg_color":"#f9fafb"}} /-->
```
