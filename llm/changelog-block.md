# Changelog Block — LLM Instructions

Use `<!-- wp:acf/changelog -->` with a JSON `data` attribute. Fields use the `changelog_` prefix. This block has **nested repeaters**.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `changelog_entries` | repeater object | Yes | Version entries. Contains nested `changelog_items` repeater |

### Entry Sub-fields

| Field Key | Type | Notes |
|---|---|---|
| `changelog_version` | text | Version number, e.g. `"1.0.0"` |
| `changelog_date` | date string | **Must be `Y-m-d` format**: `"2026-01-15"` |
| `changelog_items` | nested repeater | Individual changelog items |

### Item Sub-fields (nested inside each entry)

| Field Key | Type | Notes |
|---|---|---|
| `changelog_type` | select | One of: `added`, `changed`, `fixed`, `removed`, `security`, `deprecated` |
| `changelog_text` | text | Description of the change |

## Repeater Format (Nested)

```json
"changelog_entries": {
  "row-0": {
    "changelog_version": "2.0.0",
    "changelog_date": "2026-01-15",
    "changelog_items": {
      "row-0": {"changelog_type": "added", "changelog_text": "New dashboard feature"},
      "row-1": {"changelog_type": "fixed", "changelog_text": "Login timeout bug"},
      "row-2": {"changelog_type": "changed", "changelog_text": "Updated dependencies"}
    }
  },
  "row-1": {
    "changelog_version": "1.9.0",
    "changelog_date": "2025-12-01",
    "changelog_items": {
      "row-0": {"changelog_type": "added", "changelog_text": "Dark mode support"},
      "row-1": {"changelog_type": "deprecated", "changelog_text": "Legacy API endpoints"}
    }
  }
}
```

## Common Mistakes

1. **Date format** — Always `Y-m-d` (e.g. `"2026-01-15"`). Never `Ymd` or other formats.
2. **changelog_type** — Must be one of the 6 allowed values. No custom types.
3. **Nested repeater rows** — Both outer and inner repeaters use `row-0`, `row-1`, etc. independently.

## Example

```html
<!-- wp:acf/changelog {"name":"acf/changelog","data":{"changelog_entries":{"row-0":{"changelog_version":"2.0.0","changelog_date":"2026-01-15","changelog_items":{"row-0":{"changelog_type":"added","changelog_text":"New dashboard with analytics"},"row-1":{"changelog_type":"fixed","changelog_text":"Session timeout on mobile browsers"},"row-2":{"changelog_type":"security","changelog_text":"Patched XSS vulnerability in comments"}}},"row-1":{"changelog_version":"1.9.0","changelog_date":"2025-12-01","changelog_items":{"row-0":{"changelog_type":"added","changelog_text":"Dark mode support"},"row-1":{"changelog_type":"changed","changelog_text":"Improved search performance"}}}}}} /-->
```
