# Team Member Block — LLM Instructions

Use `<!-- wp:acf/team-member -->` with a JSON `data` attribute. Fields use the `acf_team_member_` prefix. This is an InnerBlocks block.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `acf_team_member_photo` | image (array) | No | Photo from media library. Return format is `array` |
| `acf_team_member_photo_url` | URL string | No | Direct photo URL. Alternative to media library |
| `acf_team_member_name` | text | No | Default `"Team Member Name"` |
| `acf_team_member_title` | text | No | Default `"Position Title"` |
| `acf_team_member_bio` | text/HTML | No | WYSIWYG content (basic toolbar) |
| `acf_team_member_email` | email string | No | Email address |
| `acf_team_member_phone` | text | No | Phone number |
| `acf_team_member_social_links` | repeater object | No | Social media links |
| `acf_team_member_class` | text | No | Custom CSS class |
| `acf_team_member_inline` | text | No | Inline CSS styles |

### Social Link Sub-fields

| Field Key | Type | Notes |
|---|---|---|
| `acf_social_platform` | select | One of: `LinkedIn`, `Twitter`, `Facebook`, `Instagram`, `GitHub`, `Website` |
| `acf_social_url` | URL string | Profile URL |

## Repeater Format

```json
"acf_team_member_social_links": {
  "row-0": {"acf_social_platform": "LinkedIn", "acf_social_url": "https://linkedin.com/in/username"},
  "row-1": {"acf_social_platform": "Twitter", "acf_social_url": "https://twitter.com/username"},
  "row-2": {"acf_social_platform": "GitHub", "acf_social_url": "https://github.com/username"}
}
```

## Common Mistakes

1. **acf_social_platform** — Must exactly match one of the 6 allowed values. Case-sensitive (e.g. `"LinkedIn"` not `"linkedin"`).
2. **Photo** — Use `acf_team_member_photo` (attachment) OR `acf_team_member_photo_url` (URL), not both.

## Example

```html
<!-- wp:acf/team-member {"name":"acf/team-member","data":{"acf_team_member_photo_url":"https://example.com/images/gaurav.jpg","acf_team_member_name":"Gaurav Tiwari","acf_team_member_title":"Founder & Developer","acf_team_member_bio":"<p>WordPress developer with 10+ years of experience building performant websites and plugins.</p>","acf_team_member_email":"hello@example.com","acf_team_member_social_links":{"row-0":{"acf_social_platform":"Twitter","acf_social_url":"https://twitter.com/developer"},"row-1":{"acf_social_platform":"GitHub","acf_social_url":"https://github.com/developer"},"row-2":{"acf_social_platform":"Website","acf_social_url":"https://example.com"}}}} -->
<!-- wp:paragraph -->
<p>Specializing in custom ACF blocks and headless WordPress.</p>
<!-- /wp:paragraph -->
<!-- /wp:acf/team-member -->
```
