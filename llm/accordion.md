# ACF Accordion Block — LLM Prompt

Create an accordion/FAQ block using the ACF Accordion block format. Each accordion item is a collapsible section with a title and WYSIWYG content area.

## Block Info

- **Block Name:** `acf/accordion`
- **Description:** A customizable accordion block with FAQ schema support.
- **Styles:** Default

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_acf_accord_enable_faq_schema` | Enable FAQ Schema | true_false | `"1"` to enable FAQPage JSON-LD |
| `field_acf_accord_groups` | Accordion Items | repeater | List of collapsible sections |
| — `field_acf_accord_group_title` | Title | text | Question/heading for the section |
| — `field_acf_accord_group_content` | Content | wysiwyg | Answer/body content |
| `field_acf_accordion_class` | Custom Class | text | Optional CSS class |
| `field_acf_accordion_inline` | Inline Styles | text | Optional inline CSS |

## Field Rules

- All keys use `field_` prefix
- Repeaters use nested `row-N` objects
- FAQ schema outputs `FAQPage` JSON-LD when enabled — ideal for FAQ sections
- Content field supports HTML markup (WYSIWYG)
- Uses native `<details>/<summary>` HTML elements for accessibility

## Instructions

1. Create accordion items with clear question-answer pairs
2. Enable FAQ schema if the content is FAQ-style Q&A
3. Write concise, informative answers
4. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/accordion {"name":"acf/accordion","data":{"field_acf_accord_enable_faq_schema":"1","field_acf_accord_groups":{"row-0":{"field_acf_accord_group_title":"What is WordPress?","field_acf_accord_group_content":"WordPress is a free and open-source content management system (CMS) that powers over 40% of all websites on the internet. It allows users to create and manage websites without needing to write code."},"row-1":{"field_acf_accord_group_title":"How do I install WordPress?","field_acf_accord_group_content":"You can install WordPress through your hosting control panel using one-click installers, or manually by downloading it from wordpress.org and uploading it to your server via FTP."},"row-2":{"field_acf_accord_group_title":"Is WordPress free to use?","field_acf_accord_group_content":"Yes, WordPress itself is 100% free. However, you will need to pay for web hosting and a domain name. Premium themes and plugins are optional paid extras."}}}} /-->
```
