# ACF Team Member Block — LLM Prompt

Create a team member profile block with photo, name, title, bio, contact info, and social links. Supports InnerBlocks for flexible content.

## Block Info

- **Block Name:** `acf/team-member`
- **Description:** A team member block with photo, core blocks for name/title/bio, and social links.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `acf_team_member_photo` | Photo | image (array) | Team member photo from media library |
| `acf_team_member_photo_url` | Photo URL | url | Direct photo URL (alternative) |
| `acf_team_member_name` | Name | text | Full name |
| `acf_team_member_title` | Title/Role | text | Job title or role |
| `acf_team_member_bio` | Bio | wysiwyg | Short biography |
| `acf_team_member_email` | Email | email | Contact email address |
| `acf_team_member_phone` | Phone | text | Contact phone number |
| `acf_team_member_social_links` | Social Links | repeater | Social media profiles |
| — `acf_social_platform` | Platform | select | LinkedIn, Twitter, Facebook, Instagram, GitHub, Website |
| — `acf_social_url` | URL | url | Profile URL |
| `acf_team_member_class` | Custom Class | text | Optional CSS class |
| `acf_team_member_inline` | Inline Styles | text | Optional inline CSS |

## Field Rules

- Field keys use `acf_` prefix (NOT `field_`)
- Repeaters use nested `row-N` objects
- Photo supports both media library upload and direct URL
- This block supports InnerBlocks for modern WordPress; ACF fields are legacy fallback
- Social platform options: LinkedIn, Twitter, Facebook, Instagram, GitHub, Website

## Instructions

1. Add the team member's photo (upload or URL)
2. Fill in name, title, and bio
3. Add contact information if appropriate
4. Add relevant social media links
5. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/team-member {"name":"acf/team-member","data":{"acf_team_member_photo_url":"https://example.com/gaurav-photo.jpg","acf_team_member_name":"Gaurav Tiwari","acf_team_member_title":"Founder & CEO","acf_team_member_bio":"<p>Gaurav is a tech entrepreneur with over 10 years of experience in web hosting and WordPress development. He founded GauravTiwari.org to help businesses make informed technology decisions.</p>","acf_team_member_email":"gaurav@example.com","acf_team_member_social_links":{"row-0":{"acf_social_platform":"Twitter","acf_social_url":"https://twitter.com/gauaborat"},"row-1":{"acf_social_platform":"LinkedIn","acf_social_url":"https://linkedin.com/in/gauravtiwari"},"row-2":{"acf_social_platform":"GitHub","acf_social_url":"https://github.com/wpgaurav"}}}} /-->
```

## Example — Minimal team card

```html
<!-- wp:acf/team-member {"name":"acf/team-member","data":{"acf_team_member_photo_url":"https://example.com/jane-photo.jpg","acf_team_member_name":"Jane Smith","acf_team_member_title":"Lead Developer","acf_team_member_bio":"<p>Full-stack developer specializing in WordPress and React.</p>","acf_team_member_social_links":{"row-0":{"acf_social_platform":"GitHub","acf_social_url":"https://github.com/janesmith"}}}} /-->
```
