# ACF Blocks — LLM Prompt Library

This directory contains per-block prompt documentation for generating ACF block comment markup with LLMs. Each file describes a single block's fields, rules, and example output, so an LLM can produce a valid block comment for that block in one shot.

## How to use

1. Identify which block fits the user's request from the list below.
2. Read the matching `*.md` file in this directory for the full field reference, rules, and examples.
3. Emit the WordPress block comment exactly as shown — **a single line of JSON**, no literal newlines inside the comment.

## Universal rules

These apply to **every** block in this library:

- **Single-line JSON.** The entire `<!-- wp:acf/... {...} /-->` comment must be one line. Use `\n` for line breaks inside string values (WordPress's block parser breaks silently on literal newlines in the attribute JSON).
- **Field key prefix.** Blocks use either the `field_` prefix (e.g. `field_pr_product_name`) or the `acf_` prefix (e.g. `acf_stat_number`). The two conventions are not interchangeable — each block's doc states which prefix to use. Blocks that use `acf_`: feature-grid, stats, tabs, video, team-member, testimonial, gallery, hero, cta.
- **Repeaters use `row-N` objects.** Always `{"row-0":{...},"row-1":{...}}`, never arrays.
- **All fields are optional.** Omit any field you don't need — no field is required by the block schema.
- **Style variations** go in `className` as `"is-style-<name>"`, e.g. `"className":"is-style-card"`.
- **Preview mode.** Include `"mode":"preview"` for blocks that need preview rendering in the editor (examples show when this is relevant).
- **InnerBlocks vs ACF data.** Some blocks (callout, cta, hero, section-block, testimonial) use WordPress InnerBlocks for their content. For those, put child blocks between `<!-- wp:acf/... -->` and `<!-- /wp:acf/... -->` rather than in the `data` object.

## Block index

### Content & layout

| Block | Doc | Description |
|---|---|---|
| `acf/accordion` | [accordion.md](accordion.md) | Collapsible sections with optional FAQ schema |
| `acf/callout` | [callout.md](callout.md) | Styled callout/notice box (InnerBlocks) |
| `acf/cta` | [cta.md](cta.md) | Call-to-action with heading, description, button |
| `acf/hero` | [hero.md](hero.md) | Hero section with image + CTA (InnerBlocks) |
| `acf/section-block` | [section-block.md](section-block.md) | Container wrapper for inner blocks |
| `acf/tabs` | [tabs.md](tabs.md) | Tabbed content with keyboard accessibility |
| `acf/toc` | [toc.md](toc.md) | Table of contents with schema + sticky option |

### Products & commerce

| Block | Doc | Description |
|---|---|---|
| `acf/product-box` | [product-box.md](product-box.md) | Amazon-style product listing with CTAs |
| `acf/product-cards` | [product-cards.md](product-cards.md) | Simple product card grid |
| `acf/product-review` | [product-review.md](product-review.md) | Product review with Google rich-result schema |
| `acf/pl-block` | [pl-block.md](pl-block.md) | Ranked product listing for best-of lists |
| `acf/compare` | [compare.md](compare.md) | Side-by-side comparison table |
| `acf/pros-cons` | [pros-cons.md](pros-cons.md) | Two-column pros and cons list |
| `acf/cb-coupon-code` | [coupon-code.md](coupon-code.md) | Coupon code with copy button |
| `acf/star-rating` | [star-rating.md](star-rating.md) | Interactive star rating with CreativeWork schema |

### Lists & data

| Block | Doc | Description |
|---|---|---|
| `acf/checklist` | [checklist.md](checklist.md) | Interactive checklist with progress tracking |
| `acf/stats` | [stats.md](stats.md) | Animated stat counters |
| `acf/changelog` | [changelog.md](changelog.md) | Version history / release notes |
| `acf/post-display` | [post-display.md](post-display.md) | Display selected WordPress posts |
| `acf/feature-grid` | [feature-grid.md](feature-grid.md) | Feature grid with icons and CTAs |

### Media & embeds

| Block | Doc | Description |
|---|---|---|
| `acf/video` | [video.md](video.md) | YouTube / Vimeo / self-hosted video |
| `acf/gallery` | [gallery.md](gallery.md) | Responsive image gallery |
| `acf/url-preview` | [url-preview.md](url-preview.md) | Open Graph preview card |
| `acf/code-block` | [code-block.md](code-block.md) | Syntax-highlighted code with copy button |

### Social proof & editorial

| Block | Doc | Description |
|---|---|---|
| `acf/testimonial` | [testimonial.md](testimonial.md) | Customer testimonial (InnerBlocks) |
| `acf/team-member` | [team-member.md](team-member.md) | Team member profile card |
| `acf/thread-builder` | [thread-builder.md](thread-builder.md) | Twitter/X-style thread layout |
| `acf/opinion-box` | [opinion-box.md](opinion-box.md) | Editorial opinion with author |

### Forms

| Block | Doc | Description |
|---|---|---|
| `acf/email-form` | [email-form.md](email-form.md) | Email capture form with webhook support |

## Block comment anatomy

The general shape of an ACF block comment:

```html
<!-- wp:acf/<block-slug> {"name":"acf/<block-slug>","data":{"field_foo":"value","field_bar_repeater":{"row-0":{"field_sub":"a"},"row-1":{"field_sub":"b"}}},"className":"is-style-<variation>","mode":"preview"} /-->
```

For blocks that wrap InnerBlocks, use the open/close form:

```html
<!-- wp:acf/<block-slug> {"name":"acf/<block-slug>","data":{"field_foo":"value"}} -->
<!-- wp:paragraph --><p>Inner content goes here.</p><!-- /wp:paragraph -->
<!-- /wp:acf/<block-slug> -->
```

## When in doubt

- Read the per-block doc — it lists every field key, the correct prefix, and working examples.
- If the block supports multiple style variations, the doc shows them under "Styles" in **Block Info**.
- Copy the example from the doc and adapt it rather than constructing the JSON from scratch.
