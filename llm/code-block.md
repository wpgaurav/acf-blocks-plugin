# Code Block — LLM Instructions

Use `<!-- wp:acf/code-block -->` with a JSON `data` attribute. Fields use the `code_` prefix (plus `highlight_lines`, `font_size`, `custom_class`).

## Field Reference

| Field Key | Type | Required | Notes |
|---|---|---|---|
| `code_content` | text | Yes | The code to display. Escape special characters as needed |
| `code_language` | select | No | Default `"plaintext"`. See language list below |
| `code_filename` | text | No | Filename shown in header, e.g. `"example.js"` |
| `highlight_lines` | text | No | Comma-separated line numbers or ranges: `"1,3,5-7"` |
| `code_theme` | select | No | `"dark"` (default) or `"light"` |
| `font_size` | select | No | `"small"` (13px), `"normal"` (14px, default), `"large"` (16px) |
| `custom_class` | text | No | Custom CSS class |

### Supported Languages

`plaintext`, `html`, `css`, `javascript`, `typescript`, `php`, `python`, `ruby`, `java`, `csharp`, `cpp`, `c`, `go`, `rust`, `swift`, `kotlin`, `sql`, `bash`, `powershell`, `json`, `xml`, `yaml`, `markdown`, `jsx`, `tsx`, `scss`, `sass`, `less`

## Common Mistakes

1. **code_content** — Must be a string. Escape double quotes inside the code as `\"`. Newlines as `\n`.
2. **highlight_lines** — Use commas for individual lines, hyphens for ranges: `"1,3,5-7"`. No spaces.
3. **code_language** — Must exactly match one of the supported values. Case-sensitive.

## Example

```html
<!-- wp:acf/code-block {"name":"acf/code-block","data":{"code_content":"function greet(name) {\n  return `Hello, ${name}!`;\n}\n\nconsole.log(greet('World'));","code_language":"javascript","code_filename":"greeting.js","highlight_lines":"2","code_theme":"dark","font_size":"normal"}} /-->
```
