# ACF Thread Builder Block — LLM Prompt

Create a social media-style conversation thread display (X/Twitter style) with multiple posts, avatars, engagement metrics, and media.

## Block Info

- **Block Name:** `acf/thread-builder`
- **Description:** Create Twitter/X-style conversation threads.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_thread_theme` | Theme | select | `light` or `dark` |
| `field_thread_width` | Width | select | `narrow`, `medium`, `wide`, `full` |
| `field_thread_show_connector` | Show Connector | true_false | `"1"` to show thread connector lines |
| `field_thread_connector_color` | Connector Color | color_picker | Color of connector lines |
| `field_thread_show_engagement` | Show Engagement | true_false | `"1"` to show reply/repost/like counts |
| `field_thread_posts_repeater` | Thread Posts | repeater | List of thread posts |
| — `field_thread_post_author_name` | Author Name | text | Display name |
| — `field_thread_post_author_handle` | Handle | text | @username handle |
| — `field_thread_post_author_avatar` | Avatar | image | Author profile image |
| — `field_thread_post_verified` | Verified | true_false | `"1"` for verified badge |
| — `field_thread_post_content` | Content | textarea | Post text content |
| — `field_thread_post_media` | Media | image | Optional attached image |
| — `field_thread_post_timestamp` | Timestamp | text | e.g. "2h", "Jan 15", "3:42 PM" |
| — `field_thread_post_replies` | Replies | number | Reply count |
| — `field_thread_post_reposts` | Reposts | number | Repost/retweet count |
| — `field_thread_post_likes` | Likes | number | Like/heart count |

## Field Rules

- All keys use `field_` prefix
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Repeaters use nested `row-N` objects
- Content supports @mentions and #hashtags (auto-linked in rendering)
- Connector lines visually link consecutive posts in the thread
- Engagement metrics (replies, reposts, likes) are optional
- Timestamp is free-text — use relative ("2h ago") or absolute ("Jan 15") format

## Instructions

1. Choose theme (light/dark) and width
2. Enable connector lines for visual thread continuity
3. Create thread posts with author info, content, and timestamps
4. Add media attachments where relevant
5. Include engagement metrics for social proof
6. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/thread-builder {"name":"acf/thread-builder","data":{"field_thread_theme":"light","field_thread_width":"medium","field_thread_show_connector":"1","field_thread_show_engagement":"1","field_thread_posts_repeater":{"row-0":{"field_thread_post_author_name":"Gaurav Tiwari","field_thread_post_author_handle":"@gauaborat","field_thread_post_verified":"1","field_thread_post_content":"Just migrated 5 client sites from shared hosting to @ScalaHosting managed VPS. Here's what happened 🧵","field_thread_post_timestamp":"2h","field_thread_post_replies":"12","field_thread_post_reposts":"45","field_thread_post_likes":"234"},"row-1":{"field_thread_post_author_name":"Gaurav Tiwari","field_thread_post_author_handle":"@gauaborat","field_thread_post_verified":"1","field_thread_post_content":"1/ Average page load time dropped from 3.2s to 0.8s. That's a 75% improvement just from better server architecture. No code changes needed.","field_thread_post_timestamp":"2h","field_thread_post_replies":"3","field_thread_post_reposts":"18","field_thread_post_likes":"89"},"row-2":{"field_thread_post_author_name":"Gaurav Tiwari","field_thread_post_author_handle":"@gauaborat","field_thread_post_verified":"1","field_thread_post_content":"2/ SPanel replaced cPanel completely. Same functionality, zero licensing fees. Saved each client ~$15/month right there.","field_thread_post_timestamp":"2h","field_thread_post_replies":"5","field_thread_post_reposts":"22","field_thread_post_likes":"112"}}}} /-->
```
