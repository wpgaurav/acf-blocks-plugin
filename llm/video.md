# ACF Video Block — LLM Prompt

Create a responsive video block supporting YouTube, Vimeo, and self-hosted videos with customizable playback controls.

## Block Info

- **Block Name:** `acf/video`
- **Description:** A responsive video block supporting YouTube, Vimeo, and self-hosted videos.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `acf_video_type` | Video Type | select | Required. `youtube`, `vimeo`, `self-hosted` |
| `acf_video_url` | Video URL | url | YouTube or Vimeo URL (when type is youtube/vimeo) |
| `acf_video_file` | Video File | file | Self-hosted video (when type is self-hosted) |
| `acf_video_poster` | Poster Image | image | Thumbnail/poster image (self-hosted only) |
| `acf_video_title` | Title | text | Optional video title |
| `acf_video_caption` | Caption | text | Optional caption below video |
| `acf_video_aspect_ratio` | Aspect Ratio | select | `16-9`, `4-3`, `21-9`, `1-1` |
| `acf_video_autoplay` | Autoplay | true_false | `"1"` to autoplay (muted required) |
| `acf_video_loop` | Loop | true_false | `"1"` to loop playback |
| `acf_video_muted` | Muted | true_false | `"1"` to start muted |
| `acf_video_controls` | Show Controls | true_false | `"1"` to display player controls |
| `acf_video_class` | Custom Class | text | Optional CSS class |
| `acf_video_inline` | Inline Styles | text | Optional inline CSS |

## Field Rules

- Field keys use `acf_` prefix (NOT `field_`)
- Video type determines which URL/file field is used
- YouTube/Vimeo use a facade pattern for performance (loads iframe on click)
- Autoplay requires muted to work in most browsers
- Aspect ratio controls the responsive container dimensions
- Self-hosted videos support poster images for thumbnails

## Instructions

1. Choose the video source type (YouTube, Vimeo, or self-hosted)
2. Provide the video URL or file
3. Set aspect ratio to match the video dimensions
4. Configure playback options (autoplay, loop, muted, controls)
5. Optionally add title and caption
6. Output the block as a WordPress block comment

## Example — YouTube video

```html
<!-- wp:acf/video {"name":"acf/video","data":{"acf_video_type":"youtube","acf_video_url":"https://www.youtube.com/watch?v=dQw4w9WgXcQ","acf_video_title":"Getting Started with WordPress","acf_video_caption":"A complete beginner's guide to setting up your first WordPress site.","acf_video_aspect_ratio":"16-9","acf_video_autoplay":"0","acf_video_loop":"0","acf_video_muted":"0","acf_video_controls":"1"}} /-->
```

## Example — Vimeo video

```html
<!-- wp:acf/video {"name":"acf/video","data":{"acf_video_type":"vimeo","acf_video_url":"https://vimeo.com/123456789","acf_video_title":"Product Demo","acf_video_aspect_ratio":"16-9","acf_video_controls":"1"}} /-->
```

## Example — Background video (autoplay, looped, muted, no controls)

```html
<!-- wp:acf/video {"name":"acf/video","data":{"acf_video_type":"youtube","acf_video_url":"https://www.youtube.com/watch?v=XXXXX","acf_video_aspect_ratio":"21-9","acf_video_autoplay":"1","acf_video_loop":"1","acf_video_muted":"1","acf_video_controls":"0"}} /-->
```
