# Email Form Block — LLM Instructions

Use `<!-- wp:acf/email-form -->` with a JSON `data` attribute. Fields use various prefixes. This block has conditional fields based on `form_type`.

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `form_type` | select | No | `"form_action"` (default) or `"webhook"` |
| `form_action_url` | URL string | No | Form action URL. Only when `form_type` is `"form_action"` |
| `webhook_url` | URL string | No | Webhook endpoint. Only when `form_type` is `"webhook"` |
| `webhook_auth_headers` | text | No | Auth headers for webhook. Only when `form_type` is `"webhook"` |
| `display_name_field` | `"1"` or `"0"` | No | Default `"1"`. Show name field |
| `name_field_required` | `"1"` or `"0"` | No | Make name field required. Only when `display_name_field` is `"1"` |
| `name_field_attributes` | group | No | `{"id":"","class":"","inline_css":""}`. Only when name field shown |
| `email_field_attributes` | group | No | `{"id":"","class":"","inline_css":""}` |
| `hidden_fields` | repeater object | No | Hidden form fields |
| `button_text` | text | No | Default `"Submit"` |
| `button_attributes` | group | No | `{"id":"","class":"","inline_css":""}` |
| `success_message` | text | No | Message shown after form submission |
| `form_attributes` | group | No | `{"id":"","class":"","inline_css":""}` |

### Hidden Fields Repeater

```json
"hidden_fields": {
  "row-0": {
    "field_name": "source",
    "field_value": "landing-page",
    "attributes": {"id": "", "class": "", "inline_css": ""}
  }
}
```

## Notes

- Group fields (`name_field_attributes`, `email_field_attributes`, etc.) use sub-keys `id`, `class`, `inline_css`
- Hidden fields are useful for passing tracking data or form identifiers

## Example

```html
<!-- wp:acf/email-form {"name":"acf/email-form","data":{"form_type":"form_action","form_action_url":"https://example.com/subscribe","display_name_field":"1","name_field_required":"1","button_text":"Subscribe","success_message":"Thanks for subscribing!","hidden_fields":{"row-0":{"field_name":"list_id","field_value":"main-newsletter","attributes":{"id":"","class":"","inline_css":""}}}}} /-->
```
