# ACF Changelog Block — LLM Prompt

Create a changelog/release notes block to display version history and updates.

## Block Info

- **Block Name:** `acf/changelog`
- **Description:** Display version history and release notes in a clean format.
- **Styles:** Default, Timeline (`is-style-timeline`)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_changelog_entries` | Changelog Entries | repeater | List of version entries |
| — `field_changelog_version` | Version | text | e.g. "2.1.0", "v3.0" |
| — `field_changelog_date` | Date | date_picker | Release date |
| — `field_changelog_items` | Changes | repeater (nested) | List of changes in this version |
| —— `field_changelog_type` | Change Type | select | added, changed, fixed, removed, security, deprecated |
| —— `field_changelog_text` | Description | text | What was changed |

## Change Types

| Type | Badge | Use When |
|---|---|---|
| `added` | Added | New features or functionality |
| `changed` | Changed | Updates to existing features |
| `fixed` | Fixed | Bug fixes |
| `removed` | Removed | Removed features or code |
| `security` | Security | Security patches or improvements |
| `deprecated` | Deprecated | Features marked for future removal |

## Field Rules

- All keys use `field_` prefix
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Nested repeaters: `field_changelog_entries` → `field_changelog_items` (two levels deep)
- Date format in block data: YYYYMMDD
- Entries should be ordered newest-first
- Each entry can contain multiple changes of different types

## Instructions

1. List versions in reverse chronological order (newest first)
2. Categorize each change with the appropriate type badge
3. Write clear, concise descriptions of what changed
4. Use timeline style for longer changelogs with many versions
5. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/changelog {"name":"acf/changelog","data":{"field_changelog_entries":{"row-0":{"field_changelog_version":"2.1.0","field_changelog_date":"20260215","field_changelog_items":{"row-0":{"field_changelog_type":"added","field_changelog_text":"Dark mode support for all block styles"},"row-1":{"field_changelog_type":"added","field_changelog_text":"Export changelog as markdown file"},"row-2":{"field_changelog_type":"fixed","field_changelog_text":"Star rating display issue on Safari browsers"},"row-3":{"field_changelog_type":"security","field_changelog_text":"Updated sanitization for user-submitted ratings"}}},"row-1":{"field_changelog_version":"2.0.0","field_changelog_date":"20260101","field_changelog_items":{"row-0":{"field_changelog_type":"changed","field_changelog_text":"Complete redesign of product review block layout"},"row-1":{"field_changelog_type":"removed","field_changelog_text":"Legacy shortcode support discontinued"},"row-2":{"field_changelog_type":"deprecated","field_changelog_text":"Old comparison table format — migrate to new Compare block"}}}}}} /-->
```

## Example — Timeline style

```html
<!-- wp:acf/changelog {"name":"acf/changelog","data":{"field_changelog_entries":{"row-0":{"field_changelog_version":"1.0.0","field_changelog_date":"20260101","field_changelog_items":{"row-0":{"field_changelog_type":"added","field_changelog_text":"Initial release with 29 ACF blocks"}}}}},"className":"is-style-timeline"} /-->
```
