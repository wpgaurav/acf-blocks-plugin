# Video Block — LLM Instructions

Use `<!-- wp:acf/video -->` with a JSON `data` attribute. Fields use the `acf_video_` prefix.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_video_type` | select | Yes | `"youtube"` (default), `"vimeo"`, or `"self-hosted"` |
| `acf_video_url` | URL string | No | YouTube or Vimeo URL. Only for `"youtube"` or `"vimeo"` types |
| `acf_video_file` | file (array) | No | Self-hosted video file (mp4, webm, ogg). Only for `"self-hosted"` |
| `acf_video_poster` | image (array) | No | Poster/thumbnail for self-hosted video. Only for `"self-hosted"` |
| `acf_video_title` | text | No | Video title |
| `acf_video_caption` | text | No | Caption text below video |
| `acf_video_aspect_ratio` | select | No | `"16-9"` (default), `"4-3"`, `"21-9"`, `"1-1"` |
| `acf_video_autoplay` | `"1"` or `"0"` | No | Default `"0"` |
| `acf_video_loop` | `"1"` or `"0"` | No | Default `"0"` |
| `acf_video_muted` | `"1"` or `"0"` | No | Default `"0"` |
| `acf_video_controls` | `"1"` or `"0"` | No | Default `"1"`. Show player controls |
| `acf_video_class` | text | No | Custom CSS class |
| `acf_video_inline` | text | No | Inline CSS styles |

## Notes

- For YouTube/Vimeo, provide the standard watch URL (e.g. `https://www.youtube.com/watch?v=dQw4w9WgXcQ`)
- `acf_video_file` and `acf_video_poster` use media library attachments (not practical for LLM-generated content)
- Aspect ratio values use hyphens, not colons: `"16-9"` not `"16:9"`

## Example

```html
<!-- wp:acf/video {"name":"acf/video","data":{"acf_video_type":"youtube","acf_video_url":"https://www.youtube.com/watch?v=dQw4w9WgXcQ","acf_video_title":"Product Demo Video","acf_video_caption":"Watch our 5-minute walkthrough of the platform.","acf_video_aspect_ratio":"16-9","acf_video_autoplay":"0","acf_video_loop":"0","acf_video_muted":"0","acf_video_controls":"1"}} /-->
```
