# URL Preview Block — LLM Instructions

Use `<!-- wp:acf/url-preview -->` with a JSON `data` attribute. Fields use various prefixes (`source_`, `preview_`, `image_`, `custom_`, `button_`, `card_`, etc.).

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `source_url` | URL string | No | The URL being previewed |
| `preview_title` | text | No | Card title |
| `preview_description` | text | No | Card description (textarea) |
| `image_source` | select | No | `"external"` (default) or `"local"` |
| `external_image_url` | URL string | No | Image URL. Only when `image_source` is `"external"` |
| `local_image` | image (array) | No | Media library image. Only when `image_source` is `"local"` |
| `local_image_size` | select | No | Default `"medium_large"`. One of: `thumbnail`, `medium`, `medium_large`, `large`, `full`. Only for local |
| `image_alt` | text | No | Image alt text |
| `custom_fields` | repeater object | No | Min 0, max 10. Extra metadata fields |
| `show_button` | `"1"` or `"0"` | No | Default `"1"` |
| `button_text` | text | No | Default `"View Details"`. Only when button shown |
| `button_url` | URL string | No | Button link. Only when button shown |
| `button_new_tab` | `"1"` or `"0"` | No | Default `"1"`. Open in new tab |
| `button_nofollow` | `"1"` or `"0"` | No | Default `"0"`. Add nofollow |
| `show_secondary_button` | `"1"` or `"0"` | No | Default `"0"` |
| `secondary_button_text` | text | No | Only when secondary button shown |
| `secondary_button_url` | URL string | No | Only when secondary button shown |
| `card_layout` | select | No | `"vertical"` (default) or `"horizontal"` |
| `card_style` | select | No | `"default"`, `"compact"`, `"minimal"`, `"featured"`, or `"dark"` |
| `image_position` | select | No | `"left"` (default) or `"right"`. Only for horizontal layout |
| `custom_class` | text | No | Custom CSS class |
| `custom_inline` | text | No | Inline CSS styles |

### Custom Fields Repeater

| Field Key | Type | Notes |
|---|---|---|
| `field_label` | text | Label, e.g. `"Price"` |
| `field_value` | text | Value, e.g. `"$9.99/mo"` |
| `field_icon` | select | `none`, `price`, `calendar`, `star`, `check`, `info`, `clock`, `percent`, `gift`, `truck` |

```json
"custom_fields": {
  "row-0": {"field_label": "Price", "field_value": "$14.95/mo", "field_icon": "price"},
  "row-1": {"field_label": "Rating", "field_value": "4.6/5", "field_icon": "star"},
  "row-2": {"field_label": "Free Trial", "field_value": "30 days", "field_icon": "clock"}
}
```

## Example

```html
<!-- wp:acf/url-preview {"name":"acf/url-preview","data":{"source_url":"https://www.scalahosting.com","preview_title":"ScalaHosting — Managed VPS Hosting","preview_description":"Managed VPS hosting with SPanel control panel, SShield security, and dedicated resources. Starting at $14.95/month.","image_source":"external","external_image_url":"https://example.com/images/scalahosting-preview.jpg","image_alt":"ScalaHosting dashboard","custom_fields":{"row-0":{"field_label":"Starting Price","field_value":"$14.95/mo","field_icon":"price"},"row-1":{"field_label":"Rating","field_value":"4.6/5","field_icon":"star"},"row-2":{"field_label":"Money-Back","field_value":"30 days","field_icon":"check"}},"show_button":"1","button_text":"Visit ScalaHosting","button_url":"https://example.com/go/scalahosting/","button_new_tab":"1","button_nofollow":"1","show_secondary_button":"1","secondary_button_text":"Read Review","secondary_button_url":"https://example.com/scalahosting-review/","card_layout":"horizontal","card_style":"default","image_position":"left"}} /-->
```
