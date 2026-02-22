# ACF Post Display Block — LLM Prompt

Display selected WordPress posts in various layouts. Choose from text links, thumbnails, or grid views.

## Block Info

- **Block Name:** `acf/post-display`
- **Description:** Display selected posts in various layouts.
- **Styles:** Default, Dark (`is-style-dark`), Card (`is-style-card`), Minimal (`is-style-minimal`), Bordered (`is-style-bordered`)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_pd_selected_posts` | Selected Posts | relationship | Required. WordPress post IDs |
| `field_pd_layout` | Layout | select | `text_links`, `thumbnail`, `grid` |
| `field_pd_columns` | Columns | select | 2 or 3 (grid layout only) |
| `field_pd_show_excerpt` | Show Excerpt | true_false | Display post excerpt |
| `field_pd_show_date` | Show Date | true_false | Display publish date |
| `field_pd_show_author` | Show Author | true_false | Display author name |
| `field_pd_title_tag` | Title Tag | select | h2, h3, h4, h5, h6, p, span |
| `field_pd_custom_class` | Custom Class | text | Optional CSS class |
| `field_pd_show_read_more` | Show Read More | true_false | Display read more link |
| `field_pd_read_more_text` | Read More Text | text | Custom read more label |

## Field Rules

- All keys use `field_` prefix
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- `field_pd_selected_posts` stores an array of WordPress post IDs
- Columns field only applies when layout is `grid`
- Show excerpt and show author only appear in thumbnail/grid layouts
- Title tag field only appears in thumbnail/grid layouts

## Instructions

1. Select the posts to display by their WordPress IDs
2. Choose a layout type (text_links for simple lists, thumbnail for image+text, grid for cards)
3. Configure display options (excerpt, date, author, read more)
4. Choose a style variation for visual appearance
5. Output the block as a WordPress block comment

## Example — Grid layout with cards

```html
<!-- wp:acf/post-display {"name":"acf/post-display","data":{"field_pd_selected_posts":["12345","12346","12347","12348","12349","12350"],"field_pd_layout":"grid","field_pd_columns":"3","field_pd_show_excerpt":"1","field_pd_show_date":"1","field_pd_show_author":"1","field_pd_title_tag":"h3","field_pd_show_read_more":"1","field_pd_read_more_text":"Continue Reading"},"className":"is-style-card"} /-->
```

## Example — Simple text links

```html
<!-- wp:acf/post-display {"name":"acf/post-display","data":{"field_pd_selected_posts":["12345","12346","12347"],"field_pd_layout":"text_links","field_pd_show_date":"1","field_pd_show_read_more":"0"}} /-->
```

## Note

The relationship field requires WordPress post IDs. You need to know the specific post IDs to include in the block.
