# Thread Builder Block — LLM Instructions

Use `<!-- wp:acf/thread-builder -->` with a JSON `data` attribute. Fields use the `thread_` prefix for settings and various names for post sub-fields.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `thread_theme` | select | No | `"light"` (default) or `"dark"` |
| `thread_width` | select | No | `"narrow"` (400px), `"medium"` (500px, default), `"wide"` (600px), `"full"` |
| `thread_show_connector` | `"1"` or `"0"` | No | Default `"1"`. Shows line connecting posts |
| `thread_connector_color` | color string | No | Connector line color. Only when connector shown |
| `thread_show_engagement` | `"1"` or `"0"` | No | Default `"1"`. Shows reply/repost/like counts |
| `thread_posts` | repeater object | Yes | Min 1 post. The thread messages |

### Post Sub-fields

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `author_name` | text | No | Display name |
| `author_handle` | text | No | Username, e.g. `"@gauravtiwari"` |
| `author_avatar` | image (URL) | No | Avatar image. Return format is `url` |
| `verified` | `"1"` or `"0"` | No | Show verified badge |
| `content` | text | No | Post text content (textarea) |
| `media` | image (URL) | No | Attached image. Return format is `url` |
| `timestamp` | text | No | e.g. `"10:30 AM · Jan 15, 2026"` |
| `replies` | number string | No | Default `"0"`. Reply count |
| `reposts` | number string | No | Default `"0"`. Repost count |
| `likes` | number string | No | Default `"0"`. Like count |

## Repeater Format

```json
"thread_posts": {
  "row-0": {
    "author_name": "Gaurav Tiwari",
    "author_handle": "@developer",
    "verified": "1",
    "content": "Just migrated to ScalaHosting and the difference is night and day. Here's what I found after 30 days...",
    "timestamp": "2:15 PM · Jan 2, 2026",
    "replies": "12",
    "reposts": "45",
    "likes": "230"
  },
  "row-1": {
    "author_name": "Gaurav Tiwari",
    "author_handle": "@developer",
    "verified": "1",
    "content": "1/ SPanel is a genuine cPanel replacement. Same workflow, zero licensing fees. Saved $15/month immediately.",
    "timestamp": "2:16 PM · Jan 2, 2026",
    "replies": "3",
    "reposts": "18",
    "likes": "89"
  }
}
```

## Notes

- Designed to look like a social media thread (Twitter/X style)
- `author_handle` should include the `@` prefix
- `timestamp` is free-form text — use any readable format
- Engagement counts (`replies`, `reposts`, `likes`) are only shown when `thread_show_engagement` is `"1"`

## Example

```html
<!-- wp:acf/thread-builder {"name":"acf/thread-builder","data":{"thread_theme":"light","thread_width":"medium","thread_show_connector":"1","thread_show_engagement":"1","thread_posts":{"row-0":{"author_name":"Gaurav Tiwari","author_handle":"@developer","verified":"1","content":"After 3 years on WPX, I switched to ScalaHosting. Here's my honest take after 30 days:","timestamp":"2:15 PM · Jan 2, 2026","replies":"12","reposts":"45","likes":"230"},"row-1":{"author_name":"Gaurav Tiwari","author_handle":"@developer","verified":"1","content":"SPanel replaces cPanel completely. Same features, no licensing fees. That alone saves $15/month on managed VPS.","timestamp":"2:16 PM · Jan 2, 2026","replies":"3","reposts":"18","likes":"89"},"row-2":{"author_name":"Gaurav Tiwari","author_handle":"@developer","verified":"1","content":"SShield blocked 847 attacks in my first week. Real-time monitoring with 99.998% accuracy. No false positives so far.","timestamp":"2:17 PM · Jan 2, 2026","replies":"7","reposts":"22","likes":"156"}}}} /-->
```
