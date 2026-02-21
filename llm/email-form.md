# ACF Email Form Block — LLM Prompt

Create an email capture form block with support for form actions and webhook submissions.

## Block Info

- **Block Name:** `acf/email-form`
- **Description:** A customizable email capture form block.
- **Styles:** None

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_form_type` | Form Type | select | `form_action` or `webhook` |
| `field_form_action_url` | Form Action URL | url | URL for form submission (when type is form_action) |
| `field_webhook_url` | Webhook URL | url | Webhook endpoint (when type is webhook) |
| `field_webhook_auth_headers` | Auth Headers | textarea | JSON auth headers for webhook |
| `field_display_name_field` | Show Name Field | true_false | `"1"` to display name input |
| `field_name_field_required` | Name Required | true_false | `"1"` to make name required |
| `field_name_field_attributes` | Name Field Attrs | group | Attributes for name field |
| — `field_name_attr_id` | ID | text | HTML id attribute |
| — `field_name_attr_class` | Class | text | CSS class |
| — `field_name_attr_inline_css` | Inline CSS | text | Inline styles |
| `field_email_field_attributes` | Email Field Attrs | group | Attributes for email field |
| — `field_email_attr_id` | ID | text | HTML id attribute |
| — `field_email_attr_class` | Class | text | CSS class |
| — `field_email_attr_inline_css` | Inline CSS | text | Inline styles |
| `field_hidden_fields` | Hidden Fields | repeater | Hidden form inputs |
| — `field_hidden_field_name` | Field Name | text | HTML name attribute |
| — `field_hidden_field_value` | Field Value | text | Hidden input value |
| `field_button_text` | Button Text | text | Submit button label |
| `field_button_attributes` | Button Attrs | group | Button HTML attributes |
| — `field_button_attr_id` | ID | text | HTML id attribute |
| — `field_button_attr_class` | Class | text | CSS class |
| — `field_button_attr_inline_css` | Inline CSS | text | Inline styles |
| `field_success_message` | Success Message | textarea | Message shown after submission |
| `field_form_attributes` | Form Attrs | group | Form element attributes |
| — `field_form_attr_id` | ID | text | HTML id attribute |
| — `field_form_attr_class` | Class | text | CSS class |
| — `field_form_attr_inline_css` | Inline CSS | text | Inline styles |

## Field Rules

- All keys use `field_` prefix
- Group fields use nested objects (NOT row-N format)
- Hidden fields use repeater format with `row-N` objects
- Form type determines which URL field is used (form_action or webhook)
- Webhook type sends data via JavaScript fetch API

## Instructions

1. Choose form submission type (form action or webhook)
2. Configure form/action URL
3. Decide whether to show the name field
4. Add any hidden fields needed for the form processor
5. Set button text and success message
6. Output the block as a WordPress block comment

## Example — Newsletter signup with form action

```html
<!-- wp:acf/email-form {"name":"acf/email-form","data":{"field_form_type":"form_action","field_form_action_url":"https://example.com/subscribe","field_display_name_field":"1","field_name_field_required":"1","field_hidden_fields":{"row-0":{"field_hidden_field_name":"list_id","field_hidden_field_value":"newsletter_main"}},"field_button_text":"Subscribe","field_success_message":"Thanks for subscribing! Check your email to confirm."}} /-->
```

## Example — Webhook integration

```html
<!-- wp:acf/email-form {"name":"acf/email-form","data":{"field_form_type":"webhook","field_webhook_url":"https://hooks.zapier.com/hooks/catch/123456/abcdef/","field_display_name_field":"0","field_button_text":"Get Updates","field_success_message":"You're in! We'll keep you posted."}} /-->
```
