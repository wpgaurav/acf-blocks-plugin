# ACF Code Block — LLM Prompt

Create a syntax-highlighted code snippet block with copy functionality, line highlighting, and theme options.

## Block Info

- **Block Name:** `acf/code-block`
- **Description:** Display code snippets with syntax highlighting and copy functionality.
- **Styles:** None (uses theme field instead)

## Fields

| Field Key | Name | Type | Notes |
|---|---|---|---|
| `field_code_block_code` | Code Content | textarea | The actual code to display |
| `field_code_block_language` | Language | select | Programming language for syntax highlighting |
| `field_code_block_filename` | Filename | text | Optional filename shown above the code |
| `field_code_block_highlight_lines` | Highlight Lines | text | Line numbers to highlight (e.g. "1,3-5,8") |
| `field_code_block_theme` | Theme | button_group | `dark` or `light` |
| `field_code_block_font_size` | Font Size | select | `small`, `normal`, `large` |
| `field_code_block_custom_class` | Custom Class | text | Optional CSS class |

## Supported Languages

`plaintext`, `html`, `css`, `javascript`, `typescript`, `php`, `python`, `ruby`, `java`, `csharp`, `cpp`, `c`, `go`, `rust`, `swift`, `kotlin`, `sql`, `bash`, `powershell`, `json`, `xml`, `yaml`, `markdown`, `jsx`, `tsx`, `scss`, `sass`, `less`

## Field Rules

- All keys use `field_` prefix
- **CRITICAL: The entire block comment must be a single line of JSON. Never use literal newlines.** Use `\n` for line breaks within HTML string values.
- Code content should be properly escaped in JSON (double quotes as `\"`, newlines as `\n`)
- Highlight lines format: comma-separated numbers or ranges (e.g. `"1,3-5,8"`)
- Long code snippets automatically get an expand/collapse toggle
- Includes copy-to-clipboard button

## Instructions

1. Paste the code content into the code field
2. Select the correct language for syntax highlighting
3. Optionally add a filename for context
4. Highlight important lines if needed
5. Choose dark or light theme
6. Output the block as a WordPress block comment

## Example

```html
<!-- wp:acf/code-block {"name":"acf/code-block","data":{"field_code_block_code":"function debounce(fn, delay) {\n  let timer;\n  return function (...args) {\n    clearTimeout(timer);\n    timer = setTimeout(() => fn.apply(this, args), delay);\n  };\n}\n\n// Usage\nconst handleSearch = debounce((query) => {\n  fetchResults(query);\n}, 300);","field_code_block_language":"javascript","field_code_block_filename":"utils/debounce.js","field_code_block_highlight_lines":"1-6","field_code_block_theme":"dark","field_code_block_font_size":"normal"}} /-->
```

## Example — PHP with light theme

```html
<!-- wp:acf/code-block {"name":"acf/code-block","data":{"field_code_block_code":"<?php\nadd_action('init', function() {\n    register_post_type('product', [\n        'labels' => ['name' => 'Products'],\n        'public' => true,\n        'has_archive' => true,\n        'supports' => ['title', 'editor', 'thumbnail'],\n    ]);\n});","field_code_block_language":"php","field_code_block_filename":"functions.php","field_code_block_highlight_lines":"3","field_code_block_theme":"light","field_code_block_font_size":"normal"}} /-->
```
