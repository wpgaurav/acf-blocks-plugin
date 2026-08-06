<?php
/**
 * Generated file. Run php tools/generate-manifest.php after block metadata changes.
 */
return array (
  'accordion-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/accordion',
      'title' => 'Accordion',
      'description' => 'A customizable accordion block for FAQs and collapsible content.',
      'category' => 'acf-blocks',
      'icon' => 'list-view',
      'keywords' => 
      array (
        0 => 'accordion',
        1 => 'faq',
        2 => 'toggle',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'accordion-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'jsx' => true,
        'anchor' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
      ),
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'acf_accord_groups' => 
            array (
              0 => 
              array (
                'acf_accord_group_title' => 'What is this plugin about?',
                'acf_accord_group_content' => 'This plugin provides a collection of customizable ACF blocks for the WordPress block editor.',
              ),
              1 => 
              array (
                'acf_accord_group_title' => 'How do I customize the styles?',
                'acf_accord_group_content' => 'You can use the block style variations or add custom CSS classes for advanced styling.',
              ),
              2 => 
              array (
                'acf_accord_group_title' => 'Does it need JavaScript?',
                'acf_accord_group_content' => 'No. The block renders native details and summary elements, so it works without any JavaScript.',
              ),
            ),
          ),
        ),
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_accord_block',
        'title' => 'Accordion Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_acf_accord_groups',
            'label' => 'Accordion Groups',
            'name' => 'acf_accord_groups',
            'type' => 'repeater',
            'instructions' => 'Add accordion groups.',
            'required' => 0,
            'min' => 0,
            'layout' => 'block',
            'button_label' => 'Add Group',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_acf_accord_group_title',
                'label' => 'Group Title',
                'name' => 'acf_accord_group_title',
                'type' => 'text',
                'instructions' => 'Enter the title for this accordion group.',
                'required' => 0,
              ),
              1 => 
              array (
                'key' => 'field_acf_accord_group_content',
                'label' => 'Group Content',
                'name' => 'acf_accord_group_content',
                'type' => 'wysiwyg',
                'instructions' => 'Enter the content for this accordion group.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
              ),
            ),
          ),
          1 => 
          array (
            'key' => 'field_acf_accordion_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_accordion_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          2 => 
          array (
            'key' => 'field_acf_accordion_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_accordion_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/accordion',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => 'Field group for the Accordion block.',
      ),
    ),
  ),
  'callout' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/callout',
      'title' => 'Callout',
      'description' => 'Display a styled callout box with customizable content using core blocks.',
      'category' => 'acf-blocks',
      'icon' => 'megaphone',
      'keywords' => 
      array (
        0 => 'callout',
        1 => 'cta',
        2 => 'button',
        3 => 'box',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'template.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'anchor' => true,
        'jsx' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
        1 => 
        array (
          'name' => 'dark',
          'label' => 'Dark',
        ),
        2 => 
        array (
          'name' => 'testimonial',
          'label' => 'Testimonial',
        ),
        3 => 
        array (
          'name' => 'dashed-light',
          'label' => 'Dashed Light',
        ),
        4 => 
        array (
          'name' => 'dashed-dark',
          'label' => 'Dashed Dark',
        ),
        5 => 
        array (
          'name' => 'highlight',
          'label' => 'Highlight',
        ),
      ),
      'style' => 
      array (
        0 => 'file:./callout.css',
        1 => 'file:./callout-variations.css',
      ),
      'editorStyle' => 
      array (
        0 => 'file:./callout.css',
        1 => 'file:./callout-variations.css',
      ),
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'is_preview' => true,
          ),
        ),
        'innerBlocks' => 
        array (
          0 => 
          array (
            'name' => 'core/heading',
            'attributes' => 
            array (
              'level' => 3,
              'content' => 'Callout Title',
            ),
          ),
          1 => 
          array (
            'name' => 'core/paragraph',
            'attributes' => 
            array (
              'content' => 'This is an example callout block with InnerBlocks support.',
            ),
          ),
        ),
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_callout_block',
        'title' => 'Callout Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_callout_options_tab',
            'label' => 'Options',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
            'endpoint' => 0,
          ),
          1 => 
          array (
            'key' => 'field_callout_label',
            'label' => 'Label',
            'name' => 'callout_label',
            'type' => 'text',
            'instructions' => 'Optional label text (e.g., \'CASE STUDY\', \'PRO TIP\')',
            'required' => 0,
          ),
          2 => 
          array (
            'key' => 'field_callout_label_position',
            'label' => 'Label Position',
            'name' => 'callout_label_position',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'choices' => 
            array (
              'top' => 'Top',
              'bottom' => 'Bottom',
            ),
            'default_value' => 'top',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_callout_label',
                  'operator' => '!=empty',
                ),
              ),
            ),
          ),
          3 => 
          array (
            'key' => 'field_callout_iconImage',
            'label' => 'Icon/Image',
            'name' => 'callout_iconImage',
            'type' => 'image',
            'instructions' => 'Optional image to display above the content (e.g., lightbulb icon)',
            'required' => 0,
            'return_format' => 'url',
            'preview_size' => 'thumbnail',
            'library' => 'all',
          ),
          4 => 
          array (
            'key' => 'field_callout_colors_tab',
            'label' => 'Colors',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
            'endpoint' => 0,
          ),
          5 => 
          array (
            'key' => 'field_callout_bgColor',
            'label' => 'Background Color',
            'name' => 'callout_bgColor',
            'type' => 'color_picker',
            'instructions' => 'Override the style variation background color',
            'required' => 0,
          ),
          6 => 
          array (
            'key' => 'field_callout_textColor',
            'label' => 'Text Color',
            'name' => 'callout_textColor',
            'type' => 'color_picker',
            'instructions' => 'Override the style variation text color',
            'required' => 0,
          ),
          7 => 
          array (
            'key' => 'field_callout_borderColor',
            'label' => 'Border Color',
            'name' => 'callout_borderColor',
            'type' => 'color_picker',
            'instructions' => 'Override the style variation border color',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/callout',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
      ),
    ),
  ),
  'changelog-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/changelog',
      'title' => 'Changelog',
      'description' => 'Display version history and release notes in a clean format.',
      'category' => 'acf-blocks',
      'icon' => 'backup',
      'keywords' => 
      array (
        0 => 'changelog',
        1 => 'version',
        2 => 'release',
        3 => 'history',
        4 => 'updates',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'changelog-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'anchor' => true,
        'mode' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
        1 => 
        array (
          'name' => 'timeline',
          'label' => 'Timeline',
        ),
      ),
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'changelog_entries' => 
            array (
              0 => 
              array (
                'changelog_version' => '1.2.0',
                'changelog_date' => '2024-01-15',
                'changelog_items' => 
                array (
                  0 => 
                  array (
                    'changelog_type' => 'added',
                    'changelog_text' => 'New feature for user profiles',
                  ),
                  1 => 
                  array (
                    'changelog_type' => 'fixed',
                    'changelog_text' => 'Fixed login redirect issue',
                  ),
                  2 => 
                  array (
                    'changelog_type' => 'changed',
                    'changelog_text' => 'Improved dashboard performance',
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      'style' => 'file:./changelog.css',
      'editorStyle' => 'file:./changelog.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_changelog_block',
        'title' => 'Changelog Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_changelog_entries',
            'label' => 'Versions',
            'name' => 'changelog_entries',
            'type' => 'repeater',
            'layout' => 'block',
            'button_label' => 'Add Version',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_changelog_version',
                'label' => 'Version',
                'name' => 'changelog_version',
                'type' => 'text',
                'placeholder' => '1.0.0',
                'wrapper' => 
                array (
                  'width' => '50',
                ),
              ),
              1 => 
              array (
                'key' => 'field_changelog_date',
                'label' => 'Date',
                'name' => 'changelog_date',
                'type' => 'date_picker',
                'display_format' => 'F j, Y',
                'return_format' => 'F j, Y',
                'wrapper' => 
                array (
                  'width' => '50',
                ),
              ),
              2 => 
              array (
                'key' => 'field_changelog_items',
                'label' => 'Changes',
                'name' => 'changelog_items',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Change',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'key' => 'field_changelog_type',
                    'label' => 'Type',
                    'name' => 'changelog_type',
                    'type' => 'select',
                    'choices' => 
                    array (
                      'added' => 'Added',
                      'changed' => 'Changed',
                      'fixed' => 'Fixed',
                      'removed' => 'Removed',
                      'security' => 'Security',
                      'deprecated' => 'Deprecated',
                    ),
                    'default_value' => 'added',
                    'wrapper' => 
                    array (
                      'width' => '25',
                    ),
                  ),
                  1 => 
                  array (
                    'key' => 'field_changelog_text',
                    'label' => 'Description',
                    'name' => 'changelog_text',
                    'type' => 'text',
                    'wrapper' => 
                    array (
                      'width' => '75',
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/changelog',
            ),
          ),
        ),
        'active' => true,
      ),
    ),
  ),
  'checklist-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/checklist',
      'title' => 'Checklist',
      'description' => 'Display an interactive checklist with customizable items and styling.',
      'category' => 'acf-blocks',
      'icon' => 'yes-alt',
      'keywords' => 
      array (
        0 => 'checklist',
        1 => 'todo',
        2 => 'tasks',
        3 => 'list',
        4 => 'checkbox',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'checklist-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'anchor' => true,
        'mode' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
        1 => 
        array (
          'name' => 'card',
          'label' => 'Card',
        ),
        2 => 
        array (
          'name' => 'minimal',
          'label' => 'Minimal',
        ),
      ),
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'checklist_title' => 'Getting Started',
            'checklist_items' => 
            array (
              0 => 
              array (
                'checklist_item_text' => 'Create your account',
                'checklist_item_checked' => true,
              ),
              1 => 
              array (
                'checklist_item_text' => 'Complete your profile',
                'checklist_item_checked' => true,
              ),
              2 => 
              array (
                'checklist_item_text' => 'Explore the dashboard',
                'checklist_item_checked' => false,
              ),
              3 => 
              array (
                'checklist_item_text' => 'Invite team members',
                'checklist_item_checked' => false,
              ),
            ),
          ),
        ),
      ),
      'style' => 'file:./checklist.css',
      'editorStyle' => 'file:./checklist.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_checklist_block',
        'title' => 'Checklist Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_checklist_content_tab',
            'label' => 'Content',
            'type' => 'tab',
            'placement' => 'top',
          ),
          1 => 
          array (
            'key' => 'field_checklist_title',
            'label' => 'Title',
            'name' => 'checklist_title',
            'type' => 'text',
            'instructions' => 'Optional title for the checklist',
            'default_value' => '',
          ),
          2 => 
          array (
            'key' => 'field_checklist_items',
            'label' => 'Checklist Items',
            'name' => 'checklist_items',
            'type' => 'repeater',
            'instructions' => 'Add items to your checklist',
            'min' => 0,
            'max' => 50,
            'layout' => 'block',
            'button_label' => 'Add Item',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_checklist_item_text',
                'label' => 'Item Text',
                'name' => 'checklist_item_text',
                'type' => 'text',
                'required' => 0,
              ),
              1 => 
              array (
                'key' => 'field_checklist_item_checked',
                'label' => 'Checked',
                'name' => 'checklist_item_checked',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
              ),
            ),
          ),
          3 => 
          array (
            'key' => 'field_checklist_options_tab',
            'label' => 'Options',
            'type' => 'tab',
            'placement' => 'top',
          ),
          4 => 
          array (
            'key' => 'field_checklist_interactive',
            'label' => 'Interactive',
            'name' => 'checklist_interactive',
            'type' => 'true_false',
            'instructions' => 'Allow visitors to check/uncheck items (stored in browser only)',
            'default_value' => 0,
            'ui' => 1,
          ),
          5 => 
          array (
            'key' => 'field_checklist_show_progress',
            'label' => 'Show Progress',
            'name' => 'checklist_show_progress',
            'type' => 'true_false',
            'instructions' => 'Display a progress bar showing completion status',
            'default_value' => 0,
            'ui' => 1,
          ),
          6 => 
          array (
            'key' => 'field_checklist_strikethrough',
            'label' => 'Strikethrough Completed',
            'name' => 'checklist_strikethrough',
            'type' => 'true_false',
            'instructions' => 'Apply strikethrough style to checked items',
            'default_value' => 1,
            'ui' => 1,
          ),
          7 => 
          array (
            'key' => 'field_checklist_colors_tab',
            'label' => 'Colors',
            'type' => 'tab',
            'placement' => 'top',
          ),
          8 => 
          array (
            'key' => 'field_checklist_accent_color',
            'label' => 'Accent Color',
            'name' => 'checklist_accent_color',
            'type' => 'color_picker',
            'instructions' => 'Color for checkboxes and progress bar',
            'default_value' => '#16a34a',
          ),
          9 => 
          array (
            'key' => 'field_checklist_bg_color',
            'label' => 'Background Color',
            'name' => 'checklist_bg_color',
            'type' => 'color_picker',
            'instructions' => 'Background color (for Card style)',
            'default_value' => '#f9fafb',
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/checklist',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
      ),
    ),
  ),
  'code-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/code-block',
      'title' => 'Code Block',
      'description' => 'Display code snippets with syntax highlighting and copy functionality.',
      'category' => 'acf-blocks',
      'icon' => 'editor-code',
      'keywords' => 
      array (
        0 => 'code',
        1 => 'syntax',
        2 => 'snippet',
        3 => 'programming',
        4 => 'highlight',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'code-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'anchor' => true,
      ),
      'style' => 'file:./code-block.css',
      'editorStyle' => 'file:./code-block.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_code_block',
        'title' => 'Code Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_code_block_tab_content',
            'label' => 'Content',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          1 => 
          array (
            'key' => 'field_code_block_code',
            'label' => 'Code',
            'name' => 'code_content',
            'type' => 'textarea',
            'instructions' => 'Enter the code to display.',
            'required' => 0,
            'rows' => 12,
            'placeholder' => '// Enter your code here...',
          ),
          2 => 
          array (
            'key' => 'field_code_block_language',
            'label' => 'Language',
            'name' => 'code_language',
            'type' => 'select',
            'instructions' => 'Select the programming language for syntax highlighting.',
            'required' => 0,
            'choices' => 
            array (
              'plaintext' => 'Plain Text',
              'html' => 'HTML',
              'css' => 'CSS',
              'javascript' => 'JavaScript',
              'typescript' => 'TypeScript',
              'php' => 'PHP',
              'python' => 'Python',
              'ruby' => 'Ruby',
              'java' => 'Java',
              'csharp' => 'C#',
              'cpp' => 'C++',
              'c' => 'C',
              'go' => 'Go',
              'rust' => 'Rust',
              'swift' => 'Swift',
              'kotlin' => 'Kotlin',
              'sql' => 'SQL',
              'bash' => 'Bash/Shell',
              'powershell' => 'PowerShell',
              'json' => 'JSON',
              'xml' => 'XML',
              'yaml' => 'YAML',
              'markdown' => 'Markdown',
              'jsx' => 'JSX',
              'tsx' => 'TSX',
              'scss' => 'SCSS',
              'sass' => 'Sass',
              'less' => 'Less',
            ),
            'default_value' => 'plaintext',
            'allow_null' => 0,
          ),
          3 => 
          array (
            'key' => 'field_code_block_filename',
            'label' => 'Filename (Optional)',
            'name' => 'code_filename',
            'type' => 'text',
            'instructions' => 'Display a filename header above the code block.',
            'required' => 0,
            'placeholder' => 'example.js',
          ),
          4 => 
          array (
            'key' => 'field_code_block_tab_options',
            'label' => 'Options',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          5 => 
          array (
            'key' => 'field_code_block_highlight_lines',
            'label' => 'Highlight Lines',
            'name' => 'highlight_lines',
            'type' => 'text',
            'instructions' => 'Enter line numbers to highlight (e.g., 1,3,5-7).',
            'required' => 0,
            'placeholder' => '1,3,5-7',
          ),
          6 => 
          array (
            'key' => 'field_code_block_tab_style',
            'label' => 'Style',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          7 => 
          array (
            'key' => 'field_code_block_theme',
            'label' => 'Theme',
            'name' => 'code_theme',
            'type' => 'button_group',
            'instructions' => '',
            'required' => 0,
            'choices' => 
            array (
              'dark' => 'Dark',
              'light' => 'Light',
            ),
            'default_value' => 'dark',
            'layout' => 'horizontal',
          ),
          8 => 
          array (
            'key' => 'field_code_block_font_size',
            'label' => 'Font Size',
            'name' => 'font_size',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'choices' => 
            array (
              'small' => 'Small (13px)',
              'normal' => 'Normal (14px)',
              'large' => 'Large (16px)',
            ),
            'default_value' => 'normal',
          ),
          9 => 
          array (
            'key' => 'field_code_block_custom_class',
            'label' => 'Custom CSS Class',
            'name' => 'custom_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS classes (space-separated).',
            'required' => 0,
            'placeholder' => 'my-custom-class',
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/code-block',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'show_in_rest' => 0,
      ),
    ),
  ),
  'compare-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/compare',
      'title' => 'Compare Block',
      'description' => 'A customizable compare card block.',
      'category' => 'acf-blocks',
      'icon' => 'grid-view',
      'keywords' => 
      array (
        0 => 'compare',
        1 => 'vs',
        2 => 'custom',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'compare-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'anchor' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
      ),
      'style' => 'file:./compare-block.css',
      'editorStyle' => 'file:./compare-block.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_compare_block',
        'title' => 'Compare Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_comp_columns',
            'label' => 'Number of Columns',
            'name' => 'comp_columns',
            'type' => 'number',
            'default_value' => 3,
            'min' => 2,
            'max' => 4,
            'instructions' => 'Number of columns (2-4).',
          ),
          1 => 
          array (
            'key' => 'field_comp_columns_data',
            'label' => 'Columns',
            'name' => 'comp_columns_data',
            'type' => 'repeater',
            'layout' => 'block',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_comp_title',
                'label' => 'Title',
                'name' => 'comp_title',
                'type' => 'text',
              ),
              1 => 
              array (
                'key' => 'field_comp_title_bg',
                'label' => 'Title Background',
                'name' => 'comp_title_bg',
                'type' => 'color_picker',
              ),
              2 => 
              array (
                'key' => 'field_comp_title_color',
                'label' => 'Title Text Color',
                'name' => 'comp_title_color',
                'type' => 'color_picker',
              ),
              3 => 
              array (
                'key' => 'field_comp_text',
                'label' => 'Subtitle',
                'name' => 'comp_text',
                'type' => 'text',
                'instructions' => 'Short description below the title (e.g. \'Best for performance\').',
              ),
              4 => 
              array (
                'key' => 'field_comp_list_content',
                'label' => 'Features List',
                'name' => 'comp_list_content',
                'type' => 'wysiwyg',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'tabs' => 'visual',
                'instructions' => 'Use a bullet list. Each item becomes a feature with a checkmark.',
              ),
              5 => 
              array (
                'key' => 'field_comp_column_style',
                'label' => 'Custom CSS',
                'name' => 'comp_column_style',
                'type' => 'text',
                'instructions' => 'Optional inline CSS for this column.',
              ),
            ),
            'instructions' => 'Add columns with title, subtitle, and feature list.',
          ),
          2 => 
          array (
            'key' => 'field_comp_cta_text',
            'label' => 'CTA Button Text',
            'name' => 'comp_cta_text',
            'type' => 'text',
          ),
          3 => 
          array (
            'key' => 'field_comp_cta_url',
            'label' => 'CTA Button URL',
            'name' => 'comp_cta_url',
            'type' => 'url',
          ),
          4 => 
          array (
            'key' => 'field_comp_cta_url_rel_tag',
            'label' => 'CTA Rel Attribute',
            'name' => 'comp_cta_url_rel_tag',
            'type' => 'text',
            'instructions' => 'e.g. nofollow, noopener, sponsored',
          ),
          5 => 
          array (
            'key' => 'field_comp_cta_bg',
            'label' => 'CTA Button Color',
            'name' => 'comp_cta_bg',
            'type' => 'color_picker',
            'instructions' => 'Background color for the CTA button.',
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/compare',
            ),
          ),
        ),
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
      ),
    ),
  ),
  'coupon-code' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/cb-coupon-code',
      'title' => 'Coupon Code',
      'description' => 'A coupon code block with offer details, copyable coupon code, and discount activation button.',
      'category' => 'acf-blocks',
      'icon' => 'tickets',
      'keywords' => 
      array (
        0 => 'coupon',
        1 => 'discount',
        2 => 'offer',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'coupon-code.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'anchor' => true,
      ),
      'style' => 'file:./coupon-code.css',
      'editorStyle' => 'file:./coupon-code.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_coupon_code_block',
        'title' => 'Coupon Code Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_cb_offer_details',
            'label' => 'Offer Details',
            'name' => 'cb_offer_details',
            'type' => 'text',
            'instructions' => 'Enter the offer details or headline.',
            'required' => 0,
          ),
          1 => 
          array (
            'key' => 'field_cb_code',
            'label' => 'Coupon Code',
            'name' => 'cb_code',
            'type' => 'text',
            'instructions' => 'Enter the coupon code.',
            'required' => 0,
          ),
          2 => 
          array (
            'key' => 'field_cb_copy_text',
            'label' => 'Copy Button Text',
            'name' => 'cb_copy_text',
            'type' => 'text',
            'instructions' => 'Text for the copy button.',
            'required' => 0,
            'default_value' => 'Copy Coupon',
          ),
          3 => 
          array (
            'key' => 'field_cb_activate_text',
            'label' => 'Activate Button Text',
            'name' => 'cb_activate_text',
            'type' => 'text',
            'instructions' => 'Text for the activate discount button.',
            'required' => 0,
            'default_value' => 'Activate Discount',
          ),
          4 => 
          array (
            'key' => 'field_cb_activate_url',
            'label' => 'Activate URL',
            'name' => 'cb_activate_url',
            'type' => 'url',
            'instructions' => 'URL for the activate discount button (optional).',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/cb-coupon-code',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => 'Field group for the Coupon Code block.',
      ),
    ),
  ),
  'cta-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/cta',
      'title' => 'Call to Action',
      'description' => 'A customizable call-to-action block with heading, description, and button using core blocks.',
      'category' => 'acf-blocks',
      'icon' => 'megaphone',
      'keywords' => 
      array (
        0 => 'cta',
        1 => 'call to action',
        2 => 'button',
        3 => 'conversion',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'cta-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'jsx' => true,
      ),
      'style' => 'file:./cta.css',
      'editorStyle' => 'file:./cta.css',
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'is_preview' => true,
          ),
        ),
        'innerBlocks' => 
        array (
          0 => 
          array (
            'name' => 'core/heading',
            'attributes' => 
            array (
              'level' => 2,
              'content' => 'Ready to Get Started?',
            ),
          ),
          1 => 
          array (
            'name' => 'core/paragraph',
            'attributes' => 
            array (
              'content' => 'Join thousands of satisfied customers and take your business to the next level.',
            ),
          ),
          2 => 
          array (
            'name' => 'core/buttons',
            'innerBlocks' => 
            array (
              0 => 
              array (
                'name' => 'core/button',
                'attributes' => 
                array (
                  'text' => 'Get Started Now',
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_cta_block',
        'title' => 'Call to Action Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'acf_cta_heading',
            'label' => 'Heading',
            'name' => 'acf_cta_heading',
            'type' => 'text',
            'instructions' => 'Enter the heading for the CTA block.',
            'required' => 0,
            'default_value' => 'Ready to Get Started?',
          ),
          1 => 
          array (
            'key' => 'acf_cta_heading_tag',
            'label' => 'Heading Tag',
            'name' => 'acf_cta_heading_tag',
            'type' => 'select',
            'instructions' => 'Choose the HTML tag for the heading.',
            'required' => 0,
            'choices' => 
            array (
              'h2' => 'H2 (default)',
              'h1' => 'H1',
              'h3' => 'H3',
              'h4' => 'H4',
              'h5' => 'H5',
              'h6' => 'H6',
              'p' => 'Paragraph (p)',
              'span' => 'Span',
            ),
            'default_value' => 'h2',
          ),
          2 => 
          array (
            'key' => 'acf_cta_description',
            'label' => 'Description',
            'name' => 'acf_cta_description',
            'type' => 'wysiwyg',
            'instructions' => 'Enter the description text.',
            'required' => 0,
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'default_value' => 'Take the next step and discover what we can do for you.',
          ),
          3 => 
          array (
            'key' => 'acf_cta_button_text',
            'label' => 'Button Text',
            'name' => 'acf_cta_button_text',
            'type' => 'text',
            'instructions' => 'Enter the button text.',
            'required' => 0,
            'default_value' => 'Get Started',
          ),
          4 => 
          array (
            'key' => 'acf_cta_button_url',
            'label' => 'Button URL',
            'name' => 'acf_cta_button_url',
            'type' => 'url',
            'instructions' => 'Enter the button URL.',
            'required' => 0,
          ),
          5 => 
          array (
            'key' => 'acf_cta_button_style',
            'label' => 'Button Style',
            'name' => 'acf_cta_button_style',
            'type' => 'select',
            'instructions' => 'Select the button style.',
            'required' => 0,
            'choices' => 
            array (
              'primary' => 'Primary',
              'secondary' => 'Secondary',
              'outline' => 'Outline',
            ),
            'default_value' => 'primary',
          ),
          6 => 
          array (
            'key' => 'acf_cta_background_color',
            'label' => 'Background Color',
            'name' => 'acf_cta_background_color',
            'type' => 'color_picker',
            'instructions' => 'Select a background color for the CTA block.',
            'required' => 0,
          ),
          7 => 
          array (
            'key' => 'acf_cta_text_color',
            'label' => 'Text Color',
            'name' => 'acf_cta_text_color',
            'type' => 'color_picker',
            'instructions' => 'Select a text color for the CTA block.',
            'required' => 0,
          ),
          8 => 
          array (
            'key' => 'acf_cta_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_cta_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          9 => 
          array (
            'key' => 'acf_cta_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_cta_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/cta',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'acf' => 
        array (
          'blockVersion' => 3,
        ),
      ),
    ),
  ),
  'email-form' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/email-form',
      'title' => 'Email Form',
      'description' => 'A customizable email capture form block.',
      'category' => 'acf-blocks',
      'icon' => 'email',
      'keywords' => 
      array (
        0 => 'email',
        1 => 'form',
        2 => 'subscribe',
        3 => 'newsletter',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'email-form.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'jsx' => true,
      ),
      'style' => 'file:./email-form.css',
      'editorStyle' => 'file:./email-form.css',
      'viewScript' => 'file:./email-form.js',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_email_form_block',
        'title' => 'Email Form Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_form_type',
            'label' => 'Form Type',
            'name' => 'form_type',
            'type' => 'select',
            'choices' => 
            array (
              'form_action' => 'Form Action',
              'webhook' => 'Webhook',
            ),
            'default_value' => 'form_action',
            'ui' => 1,
          ),
          1 => 
          array (
            'key' => 'field_form_action_url',
            'label' => 'Form Action URL',
            'name' => 'form_action_url',
            'type' => 'url',
            'instructions' => 'URL to submit the form if using form action.',
            'conditional_logic' => 
            array (
              0 => 
              array (
                'field' => 'field_form_type',
                'operator' => '==',
                'value' => 'form_action',
              ),
            ),
          ),
          2 => 
          array (
            'key' => 'field_webhook_url',
            'label' => 'Webhook URL',
            'name' => 'webhook_url',
            'type' => 'url',
            'instructions' => 'Webhook URL to submit the form data.',
            'conditional_logic' => 
            array (
              0 => 
              array (
                'field' => 'field_form_type',
                'operator' => '==',
                'value' => 'webhook',
              ),
            ),
          ),
          3 => 
          array (
            'key' => 'field_webhook_auth_headers',
            'label' => 'Webhook Authentication Headers',
            'name' => 'webhook_auth_headers',
            'type' => 'textarea',
            'instructions' => 'Add authentication headers (e.g., Bearer token) for the webhook.',
            'conditional_logic' => 
            array (
              0 => 
              array (
                'field' => 'field_form_type',
                'operator' => '==',
                'value' => 'webhook',
              ),
            ),
          ),
          4 => 
          array (
            'key' => 'field_display_name_field',
            'label' => 'Display Name Field',
            'name' => 'display_name_field',
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 1,
          ),
          5 => 
          array (
            'key' => 'field_name_field_required',
            'label' => 'Name Field Required',
            'name' => 'name_field_required',
            'type' => 'true_false',
            'ui' => 1,
            'instructions' => 'If unchecked, the name field will be optional.',
            'conditional_logic' => 
            array (
              0 => 
              array (
                'field' => 'field_display_name_field',
                'operator' => '==',
                'value' => '1',
              ),
            ),
          ),
          6 => 
          array (
            'key' => 'field_name_field_attributes',
            'label' => 'Name Field Attributes',
            'name' => 'name_field_attributes',
            'type' => 'group',
            'instructions' => 'Set custom ID, classes, and inline CSS for the name field.',
            'conditional_logic' => 
            array (
              0 => 
              array (
                'field' => 'field_display_name_field',
                'operator' => '==',
                'value' => '1',
              ),
            ),
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_name_attr_id',
                'label' => 'ID',
                'name' => 'id',
                'type' => 'text',
              ),
              1 => 
              array (
                'key' => 'field_name_attr_class',
                'label' => 'Class',
                'name' => 'class',
                'type' => 'text',
              ),
              2 => 
              array (
                'key' => 'field_name_attr_inline_css',
                'label' => 'Inline CSS',
                'name' => 'inline_css',
                'type' => 'text',
              ),
            ),
          ),
          7 => 
          array (
            'key' => 'field_email_field_attributes',
            'label' => 'Email Field Attributes',
            'name' => 'email_field_attributes',
            'type' => 'group',
            'instructions' => 'Set custom ID, classes, and inline CSS for the email field.',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_email_attr_id',
                'label' => 'ID',
                'name' => 'id',
                'type' => 'text',
              ),
              1 => 
              array (
                'key' => 'field_email_attr_class',
                'label' => 'Class',
                'name' => 'class',
                'type' => 'text',
              ),
              2 => 
              array (
                'key' => 'field_email_attr_inline_css',
                'label' => 'Inline CSS',
                'name' => 'inline_css',
                'type' => 'text',
              ),
            ),
          ),
          8 => 
          array (
            'key' => 'field_hidden_fields',
            'label' => 'Hidden Fields',
            'name' => 'hidden_fields',
            'type' => 'repeater',
            'instructions' => 'Add hidden input fields (e.g., honeypot, custom fields).',
            'button_label' => 'Add Hidden Field',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_hidden_field_name',
                'label' => 'Field Name',
                'name' => 'field_name',
                'type' => 'text',
              ),
              1 => 
              array (
                'key' => 'field_hidden_field_value',
                'label' => 'Field Value',
                'name' => 'field_value',
                'type' => 'text',
              ),
              2 => 
              array (
                'key' => 'field_hidden_field_attributes',
                'label' => 'Attributes',
                'name' => 'attributes',
                'type' => 'group',
                'sub_fields' => 
                array (
                  0 => 
                  array (
                    'key' => 'field_hidden_attr_id',
                    'label' => 'ID',
                    'name' => 'id',
                    'type' => 'text',
                  ),
                  1 => 
                  array (
                    'key' => 'field_hidden_attr_class',
                    'label' => 'Class',
                    'name' => 'class',
                    'type' => 'text',
                  ),
                  2 => 
                  array (
                    'key' => 'field_hidden_attr_inline_css',
                    'label' => 'Inline CSS',
                    'name' => 'inline_css',
                    'type' => 'text',
                  ),
                ),
              ),
            ),
          ),
          9 => 
          array (
            'key' => 'field_button_text',
            'label' => 'Button Text',
            'name' => 'button_text',
            'type' => 'text',
            'default_value' => 'Submit',
          ),
          10 => 
          array (
            'key' => 'field_button_attributes',
            'label' => 'Button Attributes',
            'name' => 'button_attributes',
            'type' => 'group',
            'instructions' => 'Set custom ID, classes, and inline CSS for the button.',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_button_attr_id',
                'label' => 'ID',
                'name' => 'id',
                'type' => 'text',
              ),
              1 => 
              array (
                'key' => 'field_button_attr_class',
                'label' => 'Class',
                'name' => 'class',
                'type' => 'text',
              ),
              2 => 
              array (
                'key' => 'field_button_attr_inline_css',
                'label' => 'Inline CSS',
                'name' => 'inline_css',
                'type' => 'text',
              ),
            ),
          ),
          11 => 
          array (
            'key' => 'field_success_message',
            'label' => 'Success Message',
            'name' => 'success_message',
            'type' => 'textarea',
            'instructions' => 'Message displayed upon successful form submission.',
          ),
          12 => 
          array (
            'key' => 'field_form_attributes',
            'label' => 'Form Attributes',
            'name' => 'form_attributes',
            'type' => 'group',
            'instructions' => 'Set custom ID, classes, and inline CSS for the form element.',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_form_attr_id',
                'label' => 'ID',
                'name' => 'id',
                'type' => 'text',
              ),
              1 => 
              array (
                'key' => 'field_form_attr_class',
                'label' => 'Class',
                'name' => 'class',
                'type' => 'text',
              ),
              2 => 
              array (
                'key' => 'field_form_attr_inline_css',
                'label' => 'Inline CSS',
                'name' => 'inline_css',
                'type' => 'text',
              ),
            ),
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/email-form',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Field group for the Email Form block.',
      ),
    ),
  ),
  'feature-grid-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/feature-grid',
      'title' => 'Feature Grid',
      'description' => 'A grid layout to showcase features with icons, titles, descriptions, and buttons. Supports native blocks for header content.',
      'category' => 'acf-blocks',
      'icon' => 'grid-view',
      'keywords' => 
      array (
        0 => 'features',
        1 => 'grid',
        2 => 'services',
        3 => 'benefits',
        4 => 'cards',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'feature-grid-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'jsx' => true,
        'anchor' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
        1 => 
        array (
          'name' => 'card',
          'label' => 'Card',
        ),
        2 => 
        array (
          'name' => 'dark',
          'label' => 'Dark',
        ),
        3 => 
        array (
          'name' => 'minimal',
          'label' => 'Minimal',
        ),
        4 => 
        array (
          'name' => 'bordered',
          'label' => 'Bordered',
        ),
        5 => 
        array (
          'name' => 'gradient',
          'label' => 'Gradient Cards',
        ),
      ),
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'acf_feature_grid_heading' => 'Our Features',
            'acf_feature_grid_subheading' => 'Discover what makes us different',
            'acf_feature_grid_columns' => '3',
            'acf_feature_grid_items' => 
            array (
              0 => 
              array (
                'acf_feature_icon' => '⚡',
                'acf_feature_title' => 'Lightning Fast',
                'acf_feature_description' => 'Optimized for performance with minimal footprint.',
              ),
              1 => 
              array (
                'acf_feature_icon' => '🎨',
                'acf_feature_title' => 'Customizable',
                'acf_feature_description' => 'Multiple style variations to match your design.',
              ),
              2 => 
              array (
                'acf_feature_icon' => '🔧',
                'acf_feature_title' => 'Easy Setup',
                'acf_feature_description' => 'Simple configuration with powerful options.',
              ),
            ),
          ),
        ),
      ),
      'style' => 
      array (
        0 => 'file:./feature-grid.css',
        1 => 'file:./feature-grid-variations.css',
      ),
      'editorStyle' => 
      array (
        0 => 'file:./feature-grid.css',
        1 => 'file:./feature-grid-variations.css',
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_feature_grid_block',
        'title' => 'Feature Grid Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'acf_fg_header_tab',
            'label' => 'Header',
            'type' => 'tab',
            'placement' => 'top',
          ),
          1 => 
          array (
            'key' => 'acf_fg_use_innerblocks',
            'label' => 'Use Native Blocks for Header',
            'name' => 'acf_fg_use_innerblocks',
            'type' => 'true_false',
            'instructions' => 'Enable to use WordPress blocks (headings, paragraphs, etc.) for the header section.',
            'default_value' => 0,
            'ui' => 1,
          ),
          2 => 
          array (
            'key' => 'acf_feature_grid_heading',
            'label' => 'Heading',
            'name' => 'acf_feature_grid_heading',
            'type' => 'text',
            'instructions' => 'Enter a heading for the feature grid.',
            'required' => 0,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'acf_fg_use_innerblocks',
                  'operator' => '!=',
                  'value' => '1',
                ),
              ),
            ),
          ),
          3 => 
          array (
            'key' => 'acf_feature_grid_subheading',
            'label' => 'Subheading',
            'name' => 'acf_feature_grid_subheading',
            'type' => 'textarea',
            'instructions' => 'Enter a subheading or description.',
            'required' => 0,
            'rows' => 3,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'acf_fg_use_innerblocks',
                  'operator' => '!=',
                  'value' => '1',
                ),
              ),
            ),
          ),
          4 => 
          array (
            'key' => 'acf_fg_features_tab',
            'label' => 'Features',
            'type' => 'tab',
            'placement' => 'top',
          ),
          5 => 
          array (
            'key' => 'acf_feature_grid_items',
            'label' => 'Feature Items',
            'name' => 'acf_feature_grid_items',
            'type' => 'repeater',
            'instructions' => 'Add feature items to the grid.',
            'required' => 0,
            'min' => 0,
            'layout' => 'block',
            'button_label' => 'Add Feature',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'acf_feature_icon',
                'label' => 'Icon',
                'name' => 'acf_feature_icon',
                'type' => 'text',
                'instructions' => 'Enter an icon (emoji, text, or icon class).',
                'required' => 0,
              ),
              1 => 
              array (
                'key' => 'acf_feature_image',
                'label' => 'Image (alternative to icon)',
                'name' => 'acf_feature_image',
                'type' => 'image',
                'instructions' => 'Upload an image instead of using an icon.',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
              ),
              2 => 
              array (
                'key' => 'acf_feature_title',
                'label' => 'Title',
                'name' => 'acf_feature_title',
                'type' => 'text',
                'instructions' => 'Enter the feature title.',
                'required' => 0,
              ),
              3 => 
              array (
                'key' => 'acf_feature_description',
                'label' => 'Description',
                'name' => 'acf_feature_description',
                'type' => 'textarea',
                'instructions' => 'Enter the feature description.',
                'required' => 0,
                'rows' => 4,
              ),
              4 => 
              array (
                'key' => 'acf_feature_link',
                'label' => 'Link',
                'name' => 'acf_feature_link',
                'type' => 'link',
                'instructions' => 'Add an optional link for this feature.',
                'required' => 0,
              ),
              5 => 
              array (
                'key' => 'acf_feature_button',
                'label' => 'Button',
                'name' => 'acf_feature_button',
                'type' => 'link',
                'instructions' => 'Add a button for this feature (appears below description).',
                'required' => 0,
              ),
              6 => 
              array (
                'key' => 'acf_feature_button_style',
                'label' => 'Button Style',
                'name' => 'acf_feature_button_style',
                'type' => 'select',
                'instructions' => 'Choose the button style.',
                'choices' => 
                array (
                  'primary' => 'Primary (Filled)',
                  'secondary' => 'Secondary (Outline)',
                  'text' => 'Text Link',
                ),
                'default_value' => 'primary',
                'conditional_logic' => 
                array (
                  0 => 
                  array (
                    0 => 
                    array (
                      'field' => 'acf_feature_button',
                      'operator' => '!=empty',
                    ),
                  ),
                ),
              ),
            ),
          ),
          6 => 
          array (
            'key' => 'acf_fg_layout_tab',
            'label' => 'Layout',
            'type' => 'tab',
            'placement' => 'top',
          ),
          7 => 
          array (
            'key' => 'acf_feature_grid_columns',
            'label' => 'Number of Columns',
            'name' => 'acf_feature_grid_columns',
            'type' => 'select',
            'instructions' => 'Select the number of columns for the grid.',
            'required' => 0,
            'choices' => 
            array (
              2 => '2 Columns',
              3 => '3 Columns',
              4 => '4 Columns',
            ),
            'default_value' => '3',
          ),
          8 => 
          array (
            'key' => 'acf_feature_grid_layout',
            'label' => 'Layout Style',
            'name' => 'acf_feature_grid_layout',
            'type' => 'select',
            'instructions' => 'Select the layout style for the features.',
            'required' => 0,
            'choices' => 
            array (
              'default' => 'Default (Left Aligned)',
              'centered' => 'Centered',
            ),
            'default_value' => 'default',
          ),
          9 => 
          array (
            'key' => 'acf_fg_cta_tab',
            'label' => 'CTA Button',
            'type' => 'tab',
            'placement' => 'top',
          ),
          10 => 
          array (
            'key' => 'acf_fg_cta_button',
            'label' => 'Footer CTA Button',
            'name' => 'acf_fg_cta_button',
            'type' => 'link',
            'instructions' => 'Add a call-to-action button at the bottom of the feature grid.',
          ),
          11 => 
          array (
            'key' => 'acf_fg_cta_style',
            'label' => 'CTA Button Style',
            'name' => 'acf_fg_cta_style',
            'type' => 'select',
            'choices' => 
            array (
              'primary' => 'Primary (Filled)',
              'secondary' => 'Secondary (Outline)',
              'large' => 'Large Primary',
            ),
            'default_value' => 'primary',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'acf_fg_cta_button',
                  'operator' => '!=empty',
                ),
              ),
            ),
          ),
          12 => 
          array (
            'key' => 'acf_fg_advanced_tab',
            'label' => 'Advanced',
            'type' => 'tab',
            'placement' => 'top',
          ),
          13 => 
          array (
            'key' => 'acf_feature_grid_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_feature_grid_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          14 => 
          array (
            'key' => 'acf_feature_grid_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_feature_grid_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/feature-grid',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
      ),
    ),
  ),
  'gallery-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/gallery',
      'title' => 'Gallery',
      'description' => 'A responsive gallery block with multiple layout options.',
      'category' => 'acf-blocks',
      'icon' => 'format-gallery',
      'keywords' => 
      array (
        0 => 'gallery',
        1 => 'images',
        2 => 'photos',
        3 => 'grid',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'gallery-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'jsx' => true,
      ),
      'style' => 'file:./gallery.css',
      'editorStyle' => 'file:./gallery.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_gallery_block',
        'title' => 'Gallery Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'acf_gallery_images',
            'label' => 'Gallery Images',
            'name' => 'acf_gallery_images',
            'type' => 'gallery',
            'instructions' => 'Select images for the gallery.',
            'required' => 0,
            'return_format' => 'array',
          ),
          1 => 
          array (
            'key' => 'acf_gallery_layout',
            'label' => 'Gallery Layout',
            'name' => 'acf_gallery_layout',
            'type' => 'select',
            'instructions' => 'Select the gallery layout style.',
            'required' => 0,
            'choices' => 
            array (
              'grid' => 'Grid',
              'masonry' => 'Masonry',
              'carousel' => 'Carousel',
            ),
            'default_value' => 'grid',
          ),
          2 => 
          array (
            'key' => 'acf_gallery_columns',
            'label' => 'Number of Columns',
            'name' => 'acf_gallery_columns',
            'type' => 'select',
            'instructions' => 'Select the number of columns for the gallery.',
            'required' => 0,
            'choices' => 
            array (
              2 => '2 Columns',
              3 => '3 Columns',
              4 => '4 Columns',
              5 => '5 Columns',
            ),
            'default_value' => '3',
          ),
          3 => 
          array (
            'key' => 'acf_gallery_gap',
            'label' => 'Gap Size',
            'name' => 'acf_gallery_gap',
            'type' => 'select',
            'instructions' => 'Select the gap size between images.',
            'required' => 0,
            'choices' => 
            array (
              'small' => 'Small',
              'medium' => 'Medium',
              'large' => 'Large',
            ),
            'default_value' => 'medium',
          ),
          4 => 
          array (
            'key' => 'acf_gallery_enable_lightbox',
            'label' => 'Enable Lightbox',
            'name' => 'acf_gallery_enable_lightbox',
            'type' => 'true_false',
            'instructions' => 'Enable lightbox for viewing images in full size.',
            'required' => 0,
            'default_value' => 1,
          ),
          5 => 
          array (
            'key' => 'acf_gallery_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_gallery_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          6 => 
          array (
            'key' => 'acf_gallery_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_gallery_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/gallery',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'acf' => 
        array (
          'blockVersion' => 3,
        ),
      ),
    ),
  ),
  'hero-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/hero',
      'title' => 'Hero',
      'description' => 'A customizable hero block with image and core blocks for headline, subheadline, and CTA.',
      'category' => 'acf-blocks',
      'icon' => 'cover-image',
      'keywords' => 
      array (
        0 => 'hero',
        1 => 'banner',
        2 => 'header',
        3 => 'featured',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'hero-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'jsx' => true,
      ),
      'style' => 'file:./hero.css',
      'editorStyle' => 'file:./hero.css',
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'is_preview' => true,
          ),
        ),
        'innerBlocks' => 
        array (
          0 => 
          array (
            'name' => 'core/heading',
            'attributes' => 
            array (
              'level' => 1,
              'content' => 'Welcome to Our Platform',
            ),
          ),
          1 => 
          array (
            'name' => 'core/paragraph',
            'attributes' => 
            array (
              'content' => 'Build something amazing with our powerful tools and resources.',
            ),
          ),
          2 => 
          array (
            'name' => 'core/buttons',
            'innerBlocks' => 
            array (
              0 => 
              array (
                'name' => 'core/button',
                'attributes' => 
                array (
                  'text' => 'Get Started',
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_hero_block',
        'title' => 'Hero Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'acf_hero_headline',
            'label' => 'Headline',
            'name' => 'acf_hero_headline',
            'type' => 'text',
            'instructions' => 'Enter the main headline for the hero block.',
            'required' => 0,
            'default_value' => 'Your Headline Here',
          ),
          1 => 
          array (
            'key' => 'acf_hero_headline_tag',
            'label' => 'Headline Tag',
            'name' => 'acf_hero_headline_tag',
            'type' => 'select',
            'instructions' => 'Choose the HTML tag for the headline.',
            'required' => 0,
            'choices' => 
            array (
              'h1' => 'H1 (default)',
              'h2' => 'H2',
              'h3' => 'H3',
              'h4' => 'H4',
              'h5' => 'H5',
              'h6' => 'H6',
              'p' => 'Paragraph (p)',
              'span' => 'Span',
            ),
            'default_value' => 'h1',
          ),
          2 => 
          array (
            'key' => 'acf_hero_subheadline',
            'label' => 'Subheadline',
            'name' => 'acf_hero_subheadline',
            'type' => 'wysiwyg',
            'instructions' => 'Enter the subheadline or description.',
            'required' => 0,
            'default_value' => 'Add a compelling subheadline to engage your visitors.',
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
          ),
          3 => 
          array (
            'key' => 'acf_hero_image',
            'label' => 'Background/Featured Image',
            'name' => 'acf_hero_image',
            'type' => 'image',
            'instructions' => 'Upload an image from the media library.',
            'required' => 0,
            'return_format' => 'array',
            'preview_size' => 'medium',
          ),
          4 => 
          array (
            'key' => 'acf_hero_image_url',
            'label' => 'Or Image URL',
            'name' => 'acf_hero_image_url',
            'type' => 'url',
            'instructions' => 'Alternatively, enter a direct image URL. This takes priority over the uploaded image.',
            'required' => 0,
          ),
          5 => 
          array (
            'key' => 'acf_hero_cta_text',
            'label' => 'CTA Button Text',
            'name' => 'acf_hero_cta_text',
            'type' => 'text',
            'instructions' => 'Enter the call-to-action button text.',
            'required' => 0,
            'default_value' => 'Get Started',
          ),
          6 => 
          array (
            'key' => 'acf_hero_cta_url',
            'label' => 'CTA Button URL',
            'name' => 'acf_hero_cta_url',
            'type' => 'url',
            'instructions' => 'Enter the URL for the call-to-action button.',
            'required' => 0,
          ),
          7 => 
          array (
            'key' => 'acf_hero_cta_style',
            'label' => 'CTA Button Style',
            'name' => 'acf_hero_cta_style',
            'type' => 'select',
            'instructions' => 'Select the button style.',
            'required' => 0,
            'choices' => 
            array (
              'primary' => 'Primary',
              'secondary' => 'Secondary',
              'outline' => 'Outline',
            ),
            'default_value' => 'primary',
          ),
          8 => 
          array (
            'key' => 'acf_hero_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_hero_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          9 => 
          array (
            'key' => 'acf_hero_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_hero_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/hero',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'acf' => 
        array (
          'blockVersion' => 3,
        ),
      ),
    ),
  ),
  'opinion-box' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/opinion-box',
      'title' => 'Opinion Box',
      'description' => 'A custom opinion box block for sharing thoughts.',
      'category' => 'acf-blocks',
      'icon' => 'admin-comments',
      'keywords' => 
      array (
        0 => 'opinion',
        1 => 'box',
        2 => 'feedback',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'opinion-box.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'anchor' => true,
        'jsx' => true,
      ),
      'style' => 'file:./opinion-box.css',
      'editorStyle' => 'file:./opinion-box.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_opinion_box_block',
        'title' => 'Opinion Box Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_ob_avatar',
            'label' => 'Avatar',
            'name' => 'ob_avatar',
            'type' => 'image',
            'instructions' => 'Select an avatar image from the media library.',
            'required' => 0,
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'all',
          ),
          1 => 
          array (
            'key' => 'field_ob_avatar_url',
            'label' => 'Or Avatar URL',
            'name' => 'ob_avatar_url',
            'type' => 'url',
            'instructions' => 'Alternatively, enter a direct image URL. This takes priority over the uploaded image.',
            'required' => 0,
          ),
          2 => 
          array (
            'key' => 'field_ob_name',
            'label' => 'Author Name',
            'name' => 'ob_name',
            'type' => 'text',
            'instructions' => 'Enter the author\'s name.',
            'required' => 0,
            'default_value' => 'Author Name',
          ),
          3 => 
          array (
            'key' => 'field_ob_designation',
            'label' => 'Designation',
            'name' => 'ob_designation',
            'type' => 'text',
            'instructions' => 'Enter the author\'s title or designation.',
            'required' => 0,
            'default_value' => 'Title or Designation',
          ),
          4 => 
          array (
            'key' => 'field_ob_citation',
            'label' => 'Citation',
            'name' => 'ob_citation',
            'type' => 'text',
            'instructions' => 'Enter source citation or additional context.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/opinion-box',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => 'Field group for the Opinion Box block.',
      ),
    ),
  ),
  'pl-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/pl-block',
      'title' => 'Product List Block',
      'description' => 'Ranked product listing with icon, pricing tiers, coupon codes, and CTA buttons. Designed for best-of lists and product roundups.',
      'category' => 'acf-blocks',
      'icon' => 'products',
      'keywords' => 
      array (
        0 => 'product',
        1 => 'list',
        2 => 'rank',
        3 => 'pricing',
        4 => 'coupon',
        5 => 'deal',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'pl-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'anchor' => true,
        'multiple' => true,
      ),
      'style' => 'file:./pl-block.css',
      'editorStyle' => 'file:./pl-block.css',
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'pl_block_rank' => '1',
            'pl_block_product_name' => 'ScalaHosting',
            'pl_block_description' => '<p>Managed VPS hosting with SPanel control panel. Best for WordPress users who want dedicated resources.</p>',
            'pl_block_pricing_0_pl_block_pricing_title' => 'Mini',
            'pl_block_pricing_0_pl_block_pricing_amount' => '$14.95/mo',
            'pl_block_pricing_1_pl_block_pricing_title' => 'Start',
            'pl_block_pricing_1_pl_block_pricing_amount' => '$26.95/mo',
            'pl_block_coupons_0_pl_block_coupon_code' => 'STARTER50',
            'pl_block_coupons_0_pl_block_coupon_offer' => '50% off first 3 months',
            'pl_block_buttons_0_pl_block_button_text' => 'Visit ScalaHosting',
            'pl_block_buttons_0_pl_block_button_url' => '#',
            'pl_block_buttons_0_pl_block_button_style' => 'primary',
          ),
        ),
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_pl_block',
        'title' => 'Product List Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_pl_block_rank',
            'label' => 'Rank',
            'name' => 'pl_block_rank',
            'type' => 'text',
            'instructions' => 'Position number or label (e.g. \'1\', \'#1\', \'Top Pick\').',
            'required' => 0,
            'placeholder' => '#1',
          ),
          1 => 
          array (
            'key' => 'field_pl_block_icon',
            'label' => 'Icon / Logo',
            'name' => 'pl_block_icon',
            'type' => 'image',
            'instructions' => 'Select a product icon or logo from the media library.',
            'required' => 0,
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
            'library' => 'all',
          ),
          2 => 
          array (
            'key' => 'field_pl_block_image_url',
            'label' => 'Or Image URL',
            'name' => 'pl_block_image_url',
            'type' => 'url',
            'instructions' => 'Alternatively, enter a direct image URL. Takes priority over uploaded image.',
            'required' => 0,
          ),
          3 => 
          array (
            'key' => 'field_pl_block_image_width',
            'label' => 'Image Width',
            'name' => 'pl_block_image_width',
            'type' => 'text',
            'instructions' => 'Optional CSS width for the icon (e.g. \'48px\', \'80px\'). Default: 64px.',
            'required' => 0,
            'placeholder' => '64px',
          ),
          4 => 
          array (
            'key' => 'field_pl_block_product_name',
            'label' => 'Product Name',
            'name' => 'pl_block_product_name',
            'type' => 'text',
            'instructions' => 'Enter the product or service name.',
            'required' => 0,
            'default_value' => 'Product Name',
          ),
          5 => 
          array (
            'key' => 'field_pl_block_product_url',
            'label' => 'Product URL',
            'name' => 'pl_block_product_url',
            'type' => 'url',
            'instructions' => 'Optional URL to link the product name.',
            'required' => 0,
          ),
          6 => 
          array (
            'key' => 'field_pl_block_title_tag',
            'label' => 'Title Heading Level',
            'name' => 'pl_block_title_tag',
            'type' => 'select',
            'instructions' => 'Choose the HTML tag for the product name. Defaults to paragraph.',
            'required' => 0,
            'choices' => 
            array (
              'p' => 'Paragraph (default)',
              'h2' => 'H2',
              'h3' => 'H3',
              'h4' => 'H4',
              'h5' => 'H5',
              'h6' => 'H6',
            ),
            'default_value' => 'p',
          ),
          7 => 
          array (
            'key' => 'field_pl_block_description',
            'label' => 'Description',
            'name' => 'pl_block_description',
            'type' => 'wysiwyg',
            'instructions' => 'Product description with formatting support.',
            'required' => 0,
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'default_value' => '',
          ),
          8 => 
          array (
            'key' => 'field_pl_block_pricing',
            'label' => 'Pricing',
            'name' => 'pl_block_pricing',
            'type' => 'repeater',
            'instructions' => 'Add pricing tiers or plans.',
            'required' => 0,
            'min' => 0,
            'max' => 10,
            'layout' => 'table',
            'button_label' => 'Add Pricing Tier',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_pl_block_pricing_title',
                'label' => 'Plan',
                'name' => 'pl_block_pricing_title',
                'type' => 'text',
                'required' => 0,
                'placeholder' => 'e.g. Starter',
              ),
              1 => 
              array (
                'key' => 'field_pl_block_pricing_amount',
                'label' => 'Price',
                'name' => 'pl_block_pricing_amount',
                'type' => 'text',
                'required' => 0,
                'placeholder' => '$9.99/mo',
              ),
            ),
          ),
          9 => 
          array (
            'key' => 'field_pl_block_coupons',
            'label' => 'Coupons',
            'name' => 'pl_block_coupons',
            'type' => 'repeater',
            'instructions' => 'Add coupon codes with discount details.',
            'required' => 0,
            'min' => 0,
            'max' => 5,
            'layout' => 'table',
            'button_label' => 'Add Coupon',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_pl_block_coupon_code',
                'label' => 'Code',
                'name' => 'pl_block_coupon_code',
                'type' => 'text',
                'required' => 0,
                'placeholder' => 'SAVE50',
              ),
              1 => 
              array (
                'key' => 'field_pl_block_coupon_offer',
                'label' => 'Offer Details',
                'name' => 'pl_block_coupon_offer',
                'type' => 'text',
                'required' => 0,
                'placeholder' => '50% off first 3 months',
              ),
            ),
          ),
          10 => 
          array (
            'key' => 'field_pl_block_buttons',
            'label' => 'Action Buttons',
            'name' => 'pl_block_buttons',
            'type' => 'repeater',
            'instructions' => 'Add CTA buttons.',
            'required' => 0,
            'min' => 0,
            'max' => 4,
            'layout' => 'block',
            'button_label' => 'Add Button',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_pl_block_button_text',
                'label' => 'Button Text',
                'name' => 'pl_block_button_text',
                'type' => 'text',
                'required' => 0,
                'default_value' => 'Get Offer',
              ),
              1 => 
              array (
                'key' => 'field_pl_block_button_url',
                'label' => 'Button URL',
                'name' => 'pl_block_button_url',
                'type' => 'url',
                'required' => 0,
              ),
              2 => 
              array (
                'key' => 'field_pl_block_button_style',
                'label' => 'Button Style',
                'name' => 'pl_block_button_style',
                'type' => 'select',
                'instructions' => 'Choose button appearance.',
                'required' => 0,
                'choices' => 
                array (
                  'primary' => 'Primary (Filled)',
                  'secondary' => 'Secondary (Outline)',
                  'text' => 'Text Link',
                ),
                'default_value' => 'primary',
              ),
              3 => 
              array (
                'key' => 'field_pl_block_button_rel',
                'label' => 'Rel Attribute',
                'name' => 'pl_block_button_rel',
                'type' => 'text',
                'instructions' => 'e.g. nofollow sponsored noopener',
                'required' => 0,
              ),
              4 => 
              array (
                'key' => 'field_pl_block_button_class',
                'label' => 'CSS Class',
                'name' => 'pl_block_button_class',
                'type' => 'text',
                'instructions' => 'Optional additional CSS class.',
                'required' => 0,
              ),
            ),
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/pl-block',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => 'Ranked product listing block for best-of lists and product roundups.',
      ),
    ),
  ),
  'post-display' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/post-display',
      'title' => 'Post Display',
      'description' => 'Display selected posts in various layouts.',
      'category' => 'acf-blocks',
      'icon' => 'admin-post',
      'keywords' => 
      array (
        0 => 'post',
        1 => 'article',
        2 => 'display',
        3 => 'grid',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'post-display.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'anchor' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
        1 => 
        array (
          'name' => 'dark',
          'label' => 'Dark',
        ),
        2 => 
        array (
          'name' => 'card',
          'label' => 'Card',
        ),
        3 => 
        array (
          'name' => 'minimal',
          'label' => 'Minimal',
        ),
        4 => 
        array (
          'name' => 'bordered',
          'label' => 'Bordered',
        ),
      ),
      'style' => 
      array (
        0 => 'file:./post-display.css',
        1 => 'file:./post-display-variations.css',
      ),
      'editorStyle' => 
      array (
        0 => 'file:./post-display.css',
        1 => 'file:./post-display-variations.css',
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_post_display',
        'title' => 'Post Display Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_pd_selected_posts',
            'label' => 'Select Posts',
            'name' => 'pd_selected_posts',
            'aria-label' => '',
            'type' => 'relationship',
            'instructions' => 'Select the posts you want to display.',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'post_type' => 
            array (
              0 => 'post',
              1 => 'page',
              2 => 'deal',
              3 => 'snippet',
              4 => 'tool',
            ),
            'filters' => 
            array (
              0 => 'search',
              1 => 'post_type',
            ),
            'elements' => 
            array (
              0 => 'featured_image',
            ),
            'min' => 0,
            'max' => '',
            'return_format' => 'object',
            'taxonomy' => 
            array (
            ),
            'bidirectional_target' => 
            array (
            ),
            'acfe_add_post' => 0,
            'acfe_edit_post' => 0,
          ),
          1 => 
          array (
            'key' => 'field_pd_layout',
            'label' => 'Layout',
            'name' => 'pd_layout',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => 'Choose how to display the posts',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => 
            array (
              'text_links' => 'Text Links Only',
              'thumbnail' => 'Thumbnail with Text',
              'grid' => 'Grid Layout',
            ),
            'default_value' => 'text_links',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
            'allow_custom' => 0,
            'search_placeholder' => '',
            'prepend' => '',
            'append' => '',
            'min' => '',
            'max' => '',
          ),
          2 => 
          array (
            'key' => 'field_pd_columns',
            'label' => 'Number of Columns',
            'name' => 'pd_columns',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => 'Choose the number of columns for grid layout',
            'required' => 0,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pd_layout',
                  'operator' => '==',
                  'value' => 'grid',
                ),
              ),
            ),
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => 
            array (
              2 => '2 Columns',
              3 => '3 Columns',
            ),
            'default_value' => 2,
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
            'allow_custom' => 0,
            'search_placeholder' => '',
            'prepend' => '',
            'append' => '',
            'min' => '',
            'max' => '',
          ),
          3 => 
          array (
            'key' => 'field_pd_show_excerpt',
            'label' => 'Show Excerpt',
            'name' => 'pd_show_excerpt',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => 'Show post excerpt',
            'required' => 0,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pd_layout',
                  'operator' => '!=',
                  'value' => 'text_links',
                ),
              ),
            ),
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'ui' => 1,
            'default_value' => 0,
            'message' => '',
            'ui_on_text' => '',
            'ui_off_text' => '',
            'style' => '',
          ),
          4 => 
          array (
            'key' => 'field_pd_show_date',
            'label' => 'Show Date',
            'name' => 'pd_show_date',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => 'Show post date',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'ui' => 1,
            'default_value' => 0,
            'message' => '',
            'ui_on_text' => '',
            'ui_off_text' => '',
            'style' => '',
          ),
          5 => 
          array (
            'key' => 'field_pd_show_author',
            'label' => 'Show Author',
            'name' => 'pd_show_author',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => 'Show post author',
            'required' => 0,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pd_layout',
                  'operator' => '!=',
                  'value' => 'text_links',
                ),
              ),
            ),
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'ui' => 1,
            'default_value' => 0,
            'message' => '',
            'ui_on_text' => '',
            'ui_off_text' => '',
            'style' => '',
          ),
          6 => 
          array (
            'key' => 'field_pd_title_tag',
            'label' => 'Title HTML Tag',
            'name' => 'pd_title_tag',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => 'Choose the HTML tag for post titles',
            'required' => 0,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pd_layout',
                  'operator' => '!=',
                  'value' => 'text_links',
                ),
              ),
            ),
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => 
            array (
              'h2' => 'H2',
              'h3' => 'H3',
              'h4' => 'H4',
              'h5' => 'H5',
              'h6' => 'H6',
              'p' => 'Paragraph (p)',
              'span' => 'Span',
            ),
            'default_value' => 'h3',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
            'allow_custom' => 0,
            'search_placeholder' => '',
            'prepend' => '',
            'append' => '',
            'min' => '',
            'max' => '',
          ),
          7 => 
          array (
            'key' => 'field_pd_custom_class',
            'label' => 'Custom CSS Class',
            'name' => 'pd_custom_class',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => 'Add a custom CSS class to the block',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
          ),
          8 => 
          array (
            'key' => 'field_pd_show_read_more',
            'label' => 'Show Read More Button',
            'name' => 'pd_show_read_more',
            'type' => 'true_false',
            'instructions' => 'Display a \'Read More\' button for each post.',
            'required' => 0,
            'conditional_logic' => 0,
            'ui' => 1,
            'default_value' => 0,
            'ui_on_text' => 'Yes',
            'ui_off_text' => 'No',
          ),
          9 => 
          array (
            'key' => 'field_pd_read_more_text',
            'label' => 'Read More Button Text',
            'name' => 'pd_read_more_text',
            'type' => 'text',
            'instructions' => 'Customize the text for the \'Read More\' button.',
            'required' => 0,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pd_show_read_more',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
            'default_value' => 'Read More',
            'placeholder' => 'Read More',
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/post-display',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Configuration for Post Display Block',
        'show_in_rest' => 0,
        'acfe_autosync' => '',
        'acfe_form' => 0,
        'acfe_display_title' => '',
        'acfe_meta' => '',
        'acfe_note' => '',
      ),
    ),
  ),
  'product-box' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/product-box',
      'title' => 'Product Box',
      'description' => 'Amazon-style product listing with image, pricing, features, ratings, and multiple CTA buttons. Supports dark and light mode.',
      'category' => 'acf-blocks',
      'icon' => 'cart',
      'keywords' => 
      array (
        0 => 'product',
        1 => 'box',
        2 => 'rating',
        3 => 'amazon',
        4 => 'price',
        5 => 'deal',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'product-box.php',
        'blockVersion' => 3,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
        1 => 
        array (
          'name' => 'top-image',
          'label' => 'Top Image',
        ),
        2 => 
        array (
          'name' => 'no-image',
          'label' => 'No Image',
        ),
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'anchor' => true,
      ),
      'style' => 'file:./product-box.css',
      'editorStyle' => 'file:./product-box.css',
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'pb_title' => 'PHILIPS Brilliance 49" SuperWide Curved Monitor',
            'pb_badge_text' => 'SAVE 6%',
            'pb_badge_color' => '#22c55e',
            'pb_rating' => 4.5,
            'pb_rating_count' => '1,234 ratings',
            'pb_original_price' => '$988.15',
            'pb_discount_percent' => '-6%',
            'pb_current_price' => '$927.58',
            'pb_features_0_pb_feature_text' => '49-Inch Class Super wide 32:9 LED monitor',
            'pb_features_1_pb_feature_text' => '1800R curved VA screen for immersive viewing',
            'pb_buttons_0_pb_cta_text' => 'Grab This',
            'pb_buttons_0_pb_cta_url' => '#',
            'pb_buttons_0_pb_cta_style' => 'amazon',
            'pb_buttons_0_pb_cta_icon' => 'amazon',
          ),
        ),
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_product_box_block',
        'title' => 'Product Box Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_pb_image',
            'label' => 'Product Image',
            'name' => 'pb_image',
            'type' => 'image',
            'instructions' => 'Select the product image from the media library.',
            'required' => 0,
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
          ),
          1 => 
          array (
            'key' => 'field_pb_image_url',
            'label' => 'Or Image URL',
            'name' => 'pb_image_url',
            'type' => 'url',
            'instructions' => 'Alternatively, enter a direct image URL. This takes priority over the uploaded image.',
            'required' => 0,
          ),
          2 => 
          array (
            'key' => 'field_pb_badge_text',
            'label' => 'Sale Badge Text',
            'name' => 'pb_badge_text',
            'type' => 'text',
            'instructions' => 'Optional badge text shown in corner (e.g., \'SAVE 10%\', \'HOT DEAL\', \'BEST SELLER\').',
            'required' => 0,
            'placeholder' => 'SAVE 10%',
          ),
          3 => 
          array (
            'key' => 'field_pb_badge_color',
            'label' => 'Badge Background Color',
            'name' => 'pb_badge_color',
            'type' => 'color_picker',
            'instructions' => 'Background color for the badge.',
            'required' => 0,
            'default_value' => '#22c55e',
          ),
          4 => 
          array (
            'key' => 'field_pb_title',
            'label' => 'Product Title',
            'name' => 'pb_title',
            'type' => 'text',
            'instructions' => 'Enter the product title.',
            'required' => 0,
            'default_value' => 'Product Title',
          ),
          5 => 
          array (
            'key' => 'field_pb_title_url',
            'label' => 'Title Link URL',
            'name' => 'pb_title_url',
            'type' => 'url',
            'instructions' => 'Optional URL to link the title to the product page.',
            'required' => 0,
          ),
          6 => 
          array (
            'key' => 'field_pb_title_tag',
            'label' => 'Title Heading Level',
            'name' => 'pb_title_tag',
            'type' => 'select',
            'instructions' => 'Choose the HTML tag for the title. Defaults to paragraph.',
            'required' => 0,
            'choices' => 
            array (
              'p' => 'Paragraph (default)',
              'h2' => 'H2',
              'h3' => 'H3',
              'h4' => 'H4',
              'h5' => 'H5',
              'h6' => 'H6',
            ),
            'default_value' => 'p',
          ),
          7 => 
          array (
            'key' => 'field_pb_rating',
            'label' => 'Rating',
            'name' => 'pb_rating',
            'type' => 'number',
            'instructions' => 'Enter a rating from 0 to 5 (supports half stars like 4.5). Leave at 0 to hide.',
            'required' => 0,
            'min' => 0,
            'max' => 5,
            'step' => 0.5,
            'default_value' => 0,
          ),
          8 => 
          array (
            'key' => 'field_pb_rating_count',
            'label' => 'Rating Count',
            'name' => 'pb_rating_count',
            'type' => 'text',
            'instructions' => 'Number of ratings/reviews (e.g., \'1,234 ratings\').',
            'required' => 0,
          ),
          9 => 
          array (
            'key' => 'field_pb_features',
            'label' => 'Product Features',
            'name' => 'pb_features',
            'type' => 'repeater',
            'instructions' => 'Add bullet point features/highlights.',
            'required' => 0,
            'min' => 0,
            'max' => 10,
            'layout' => 'table',
            'button_label' => 'Add Feature',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_pb_feature_text',
                'label' => 'Feature',
                'name' => 'pb_feature_text',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
              ),
            ),
          ),
          10 => 
          array (
            'key' => 'field_pb_original_price',
            'label' => 'Original Price',
            'name' => 'pb_original_price',
            'type' => 'text',
            'instructions' => 'Original/list price to show strikethrough (e.g., \'$99.99\').',
            'required' => 0,
          ),
          11 => 
          array (
            'key' => 'field_pb_discount_percent',
            'label' => 'Discount Percentage',
            'name' => 'pb_discount_percent',
            'type' => 'text',
            'instructions' => 'Discount percentage to display (e.g., \'-15%\').',
            'required' => 0,
          ),
          12 => 
          array (
            'key' => 'field_pb_current_price',
            'label' => 'Current Price',
            'name' => 'pb_current_price',
            'type' => 'text',
            'instructions' => 'Current/sale price (e.g., \'$79.99\').',
            'required' => 0,
          ),
          13 => 
          array (
            'key' => 'field_pb_price_note',
            'label' => 'Price Note',
            'name' => 'pb_price_note',
            'type' => 'text',
            'instructions' => 'Optional note below price (e.g., \'Free shipping\', \'Prime eligible\').',
            'required' => 0,
          ),
          14 => 
          array (
            'key' => 'field_pb_description',
            'label' => 'Description',
            'name' => 'pb_description',
            'type' => 'wysiwyg',
            'instructions' => 'Enter additional product description (optional).',
            'required' => 0,
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'default_value' => '',
          ),
          15 => 
          array (
            'key' => 'field_pb_buttons',
            'label' => 'Call to Action Buttons',
            'name' => 'pb_buttons',
            'type' => 'repeater',
            'instructions' => 'Add call-to-action buttons.',
            'required' => 0,
            'min' => 0,
            'max' => 4,
            'layout' => 'block',
            'button_label' => 'Add Button',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_pb_cta_text',
                'label' => 'Button Text',
                'name' => 'pb_cta_text',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'default_value' => 'Buy Now',
              ),
              1 => 
              array (
                'key' => 'field_pb_cta_url',
                'label' => 'Button URL',
                'name' => 'pb_cta_url',
                'type' => 'url',
                'instructions' => '',
                'required' => 0,
              ),
              2 => 
              array (
                'key' => 'field_pb_cta_style',
                'label' => 'Button Style',
                'name' => 'pb_cta_style',
                'type' => 'select',
                'instructions' => 'Choose button appearance.',
                'required' => 0,
                'choices' => 
                array (
                  'primary' => 'Primary (Filled)',
                  'secondary' => 'Secondary (Outline)',
                  'amazon' => 'Amazon Style',
                  'custom' => 'Custom (use CSS class)',
                ),
                'default_value' => 'primary',
              ),
              3 => 
              array (
                'key' => 'field_pb_cta_icon',
                'label' => 'Button Icon',
                'name' => 'pb_cta_icon',
                'type' => 'select',
                'instructions' => 'Optional icon before button text.',
                'required' => 0,
                'choices' => 
                array (
                  'none' => 'None',
                  'cart' => 'Shopping Cart',
                  'amazon' => 'Amazon',
                  'external' => 'External Link',
                  'check' => 'Checkmark',
                ),
                'default_value' => 'none',
              ),
              4 => 
              array (
                'key' => 'field_pb_cta_class',
                'label' => 'CSS Class',
                'name' => 'pb_cta_class',
                'type' => 'text',
                'instructions' => 'Optional additional CSS class for the button.',
                'required' => 0,
              ),
              5 => 
              array (
                'key' => 'field_pb_cta_rel',
                'label' => 'Rel Attribute',
                'name' => 'pb_cta_rel',
                'type' => 'text',
                'instructions' => 'Optional rel attribute (e.g., nofollow, sponsored).',
                'required' => 0,
              ),
            ),
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/product-box',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => 'Field group for the Product Box block with Amazon-style product listing features.',
      ),
    ),
  ),
  'product-cards' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/product-cards',
      'title' => 'Product Cards',
      'description' => 'A customizable product card block.',
      'category' => 'acf-blocks',
      'icon' => 'grid-view',
      'keywords' => 
      array (
        0 => 'product',
        1 => 'card',
        2 => 'custom',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'product-cards.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
      ),
      'style' => 'file:./product-cards.css',
      'editorStyle' => 'file:./product-cards.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_product_cards',
        'title' => 'Product Cards',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_pc_block_title',
            'label' => 'Product Title',
            'name' => 'pc_block_title',
            'type' => 'text',
            'default_value' => 'Product Name',
          ),
          1 => 
          array (
            'key' => 'field_pc_block_title_color',
            'label' => 'Title Text Color',
            'name' => 'pc_block_title_color',
            'type' => 'color_picker',
            'default_value' => '#FFFFFF',
          ),
          2 => 
          array (
            'key' => 'field_pc_block_title_bg_color',
            'label' => 'Title Background Color',
            'name' => 'pc_block_title_bg_color',
            'type' => 'color_picker',
            'default_value' => '#007bff',
          ),
          3 => 
          array (
            'key' => 'field_pc_block_title_tag',
            'label' => 'Title Heading Level',
            'name' => 'pc_block_title_tag',
            'type' => 'select',
            'instructions' => 'Choose the HTML tag for the title. Defaults to paragraph.',
            'required' => 0,
            'choices' => 
            array (
              'p' => 'Paragraph (default)',
              'h2' => 'H2',
              'h3' => 'H3',
              'h4' => 'H4',
              'h5' => 'H5',
              'h6' => 'H6',
            ),
            'default_value' => 'p',
          ),
          4 => 
          array (
            'key' => 'field_pc_block_product_image',
            'label' => 'Product Image',
            'name' => 'pc_block_product_image',
            'type' => 'image',
            'return_format' => 'url',
            'preview_size' => 'medium',
          ),
          5 => 
          array (
            'key' => 'field_pc_block_description',
            'label' => 'Product Description',
            'name' => 'pc_block_description',
            'type' => 'textarea',
            'default_value' => 'This product is cool and everything. 1rem font-size, visual text.',
          ),
          6 => 
          array (
            'key' => 'field_pc_block_root_class',
            'label' => 'Custom CSS Class',
            'name' => 'pc_block_root_class',
            'type' => 'text',
            'instructions' => 'Optional. Add a custom class to the root div.',
          ),
          7 => 
          array (
            'key' => 'field_pc_block_button_text',
            'label' => 'Button Text',
            'name' => 'pc_block_button_text',
            'type' => 'text',
            'default_value' => 'Learn more',
          ),
          8 => 
          array (
            'key' => 'field_pc_block_button_url',
            'label' => 'Button URL',
            'name' => 'pc_block_button_url',
            'type' => 'url',
          ),
          9 => 
          array (
            'key' => 'field_pc_block_button_rel',
            'label' => 'Button Rel Attribute',
            'name' => 'pc_block_button_rel',
            'type' => 'text',
            'instructions' => 'Optional. Add a rel attribute (e.g., nofollow, noopener).',
          ),
          10 => 
          array (
            'key' => 'field_pc_block_text_link',
            'label' => 'Additional Text Link',
            'name' => 'pc_block_text_link',
            'type' => 'text',
            'instructions' => 'Text for the additional link below the button.',
          ),
          11 => 
          array (
            'key' => 'field_pc_block_text_link_url',
            'label' => 'Additional Link URL',
            'name' => 'pc_block_text_link_url',
            'type' => 'url',
          ),
          12 => 
          array (
            'key' => 'field_pc_block_text_link_rel',
            'label' => 'Additional Link Rel Attribute',
            'name' => 'pc_block_text_link_rel',
            'type' => 'text',
            'instructions' => 'Optional. Add a rel attribute (e.g., nofollow, noopener).',
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/product-cards',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'A customizable product card with an image, title, description, button, and additional link.',
      ),
    ),
  ),
  'product-review' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/product-review',
      'title' => 'Product Review',
      'description' => 'A product review block with structured schema data for Google rich results.',
      'category' => 'acf-blocks',
      'icon' => 'star-filled',
      'keywords' => 
      array (
        0 => 'review',
        1 => 'product',
        2 => 'rating',
        3 => 'schema',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'product-review.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'anchor' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
      ),
      'style' => 'file:./product-review.css',
      'editorStyle' => 'file:./product-review.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_product_review_block',
        'title' => 'Product Review Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_pr_content_tab',
            'label' => 'Product Info',
            'type' => 'tab',
            'placement' => 'top',
          ),
          1 => 
          array (
            'key' => 'field_pr_product_name',
            'label' => 'Product Name',
            'name' => 'product_name',
            'type' => 'text',
            'required' => 0,
            'default_value' => 'Product Name',
          ),
          2 => 
          array (
            'key' => 'field_pr_show_title',
            'label' => 'Show Title',
            'name' => 'show_title',
            'type' => 'true_false',
            'instructions' => 'Display the product title. The title is still used in schema markup even when hidden.',
            'default_value' => 1,
            'ui' => 1,
          ),
          3 => 
          array (
            'key' => 'field_pr_title_tag',
            'label' => 'Title Heading Level',
            'name' => 'title_tag',
            'type' => 'select',
            'instructions' => 'Choose the HTML tag for the title. Defaults to paragraph.',
            'required' => 0,
            'choices' => 
            array (
              'p' => 'Paragraph (default)',
              'h2' => 'H2',
              'h3' => 'H3',
              'h4' => 'H4',
              'h5' => 'H5',
              'h6' => 'H6',
            ),
            'default_value' => 'p',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pr_show_title',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
          ),
          4 => 
          array (
            'key' => 'field_pr_product_image',
            'label' => 'Product Image',
            'name' => 'product_image',
            'type' => 'image',
            'instructions' => 'Upload an image from the media library.',
            'return_format' => 'id',
            'preview_size' => 'medium',
          ),
          5 => 
          array (
            'key' => 'field_pr_product_image_url',
            'label' => 'Or Image URL',
            'name' => 'product_image_url',
            'type' => 'url',
            'instructions' => 'Alternatively, enter a direct image URL. This takes priority over the uploaded image.',
          ),
          6 => 
          array (
            'key' => 'field_pr_overall_rating',
            'label' => 'Overall Rating',
            'name' => 'overall_rating',
            'type' => 'number',
            'instructions' => 'Rating from 1 to 5',
            'min' => 1,
            'max' => 5,
            'step' => 0.1,
            'default_value' => 4.5,
          ),
          7 => 
          array (
            'key' => 'field_pr_summary',
            'label' => 'Summary',
            'name' => 'summary',
            'type' => 'wysiwyg',
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'default_value' => 'Write your product review summary here.',
          ),
          8 => 
          array (
            'key' => 'field_pr_features_tab',
            'label' => 'Feature Ratings',
            'type' => 'tab',
            'placement' => 'top',
          ),
          9 => 
          array (
            'key' => 'field_pr_features',
            'label' => 'Features',
            'name' => 'features',
            'type' => 'repeater',
            'layout' => 'table',
            'button_label' => 'Add Feature',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_pr_feature_name',
                'label' => 'Feature Name',
                'name' => 'feature_name',
                'type' => 'text',
              ),
              1 => 
              array (
                'key' => 'field_pr_feature_rating',
                'label' => 'Rating',
                'name' => 'feature_rating',
                'type' => 'number',
                'min' => 1,
                'max' => 5,
                'step' => 0.5,
              ),
            ),
          ),
          10 => 
          array (
            'key' => 'field_pr_pros_cons_tab',
            'label' => 'Pros & Cons',
            'type' => 'tab',
            'placement' => 'top',
          ),
          11 => 
          array (
            'key' => 'field_pr_pros',
            'label' => 'Pros',
            'name' => 'pros',
            'type' => 'repeater',
            'layout' => 'table',
            'button_label' => 'Add Pro',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_pr_pro_text',
                'label' => 'Pro',
                'name' => 'pro_text',
                'type' => 'text',
              ),
            ),
          ),
          12 => 
          array (
            'key' => 'field_pr_cons',
            'label' => 'Cons',
            'name' => 'cons',
            'type' => 'repeater',
            'layout' => 'table',
            'button_label' => 'Add Con',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_pr_con_text',
                'label' => 'Con',
                'name' => 'con_text',
                'type' => 'text',
              ),
            ),
          ),
          13 => 
          array (
            'key' => 'field_pr_offer_tab',
            'label' => 'Offer/Pricing',
            'type' => 'tab',
            'placement' => 'top',
          ),
          14 => 
          array (
            'key' => 'field_pr_offer_url',
            'label' => 'Offer URL',
            'name' => 'offer_url',
            'type' => 'url',
          ),
          15 => 
          array (
            'key' => 'field_pr_offer_cta_text',
            'label' => 'CTA Button Text',
            'name' => 'offer_cta_text',
            'type' => 'text',
            'default_value' => 'Get Offer',
          ),
          16 => 
          array (
            'key' => 'field_pr_link_rel',
            'label' => 'Link Rel Attribute',
            'name' => 'link_rel',
            'type' => 'text',
            'instructions' => 'Rel attribute for the offer link (e.g., nofollow, sponsored, noopener)',
            'default_value' => 'nofollow sponsored',
          ),
          17 => 
          array (
            'key' => 'field_pr_link_target',
            'label' => 'Link Target',
            'name' => 'link_target',
            'type' => 'select',
            'instructions' => 'Where to open the offer link',
            'choices' => 
            array (
              '_blank' => 'New Tab (_blank)',
              '_self' => 'Same Tab (_self)',
            ),
            'default_value' => '_blank',
          ),
          18 => 
          array (
            'key' => 'field_pr_offer_price_currency',
            'label' => 'Currency',
            'name' => 'offer_price_currency',
            'type' => 'select',
            'choices' => 
            array (
              'USD' => 'USD ($)',
              'EUR' => 'EUR (€)',
              'GBP' => 'GBP (£)',
              'INR' => 'INR (₹)',
              'CAD' => 'CAD ($)',
              'AUD' => 'AUD ($)',
            ),
            'default_value' => 'USD',
          ),
          19 => 
          array (
            'key' => 'field_pr_offer_price',
            'label' => 'Price',
            'name' => 'offer_price',
            'type' => 'text',
          ),
          20 => 
          array (
            'key' => 'field_pr_payment_term',
            'label' => 'Payment Term',
            'name' => 'payment_term',
            'type' => 'text',
            'instructions' => 'e.g., /month, /year, one-time',
          ),
          21 => 
          array (
            'key' => 'field_pr_price_valid_until',
            'label' => 'Price Valid Until',
            'name' => 'price_valid_until',
            'type' => 'date_picker',
            'instructions' => 'Date until which the price is valid (recommended for Offer schema)',
            'display_format' => 'F j, Y',
            'return_format' => 'Y-m-d',
          ),
          22 => 
          array (
            'key' => 'field_pr_schema_tab',
            'label' => 'Schema & SEO',
            'type' => 'tab',
            'placement' => 'top',
          ),
          23 => 
          array (
            'key' => 'field_pr_enable_json_ld',
            'label' => 'Enable Schema Markup',
            'name' => 'enable_json_ld',
            'type' => 'true_false',
            'default_value' => 1,
            'ui' => 1,
          ),
          24 => 
          array (
            'key' => 'field_pr_author_name',
            'label' => 'Reviewer Name',
            'name' => 'author_name',
            'type' => 'text',
            'instructions' => 'Name of the person reviewing this product',
          ),
          25 => 
          array (
            'key' => 'field_pr_date_modified',
            'label' => 'Review Last Updated',
            'name' => 'review_date_modified',
            'type' => 'date_picker',
            'instructions' => 'Date when the review was last updated (signals freshness to Google)',
            'display_format' => 'F j, Y',
            'return_format' => 'Y-m-d',
          ),
          26 => 
          array (
            'key' => 'field_pr_brand',
            'label' => 'Brand',
            'name' => 'product_brand',
            'type' => 'text',
          ),
          27 => 
          array (
            'key' => 'field_pr_sku',
            'label' => 'SKU',
            'name' => 'product_sku',
            'type' => 'text',
          ),
          28 => 
          array (
            'key' => 'field_pr_availability',
            'label' => 'Availability',
            'name' => 'product_availability',
            'type' => 'select',
            'choices' => 
            array (
              'InStock' => 'In Stock',
              'OutOfStock' => 'Out of Stock',
              'PreOrder' => 'Pre-Order',
              'Discontinued' => 'Discontinued',
            ),
            'default_value' => 'InStock',
          ),
          29 => 
          array (
            'key' => 'field_pr_product_type',
            'label' => 'Product Type',
            'name' => 'product_type',
            'type' => 'select',
            'instructions' => 'Schema @type. Use SoftwareApplication for apps/SaaS (no offers fields required by Google).',
            'choices' => 
            array (
              'Product' => 'Product (physical or generic)',
              'SoftwareApplication' => 'Software / SaaS / App',
            ),
            'default_value' => 'Product',
          ),
          30 => 
          array (
            'key' => 'field_pr_return_policy',
            'label' => 'Return Policy',
            'name' => 'return_policy',
            'type' => 'select',
            'instructions' => 'Merchant return policy for the offers schema. Required by Google for Product type.',
            'choices' => 
            array (
              'MerchantReturnNotPermitted' => 'No Returns (digital goods)',
              'MerchantReturnFiniteReturnWindow' => 'Finite Return Window',
              'MerchantReturnUnlimitedWindow' => 'Unlimited Returns',
            ),
            'default_value' => 'MerchantReturnNotPermitted',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pr_product_type',
                  'operator' => '==',
                  'value' => 'Product',
                ),
              ),
            ),
          ),
          31 => 
          array (
            'key' => 'field_pr_return_days',
            'label' => 'Return Window (days)',
            'name' => 'return_days',
            'type' => 'number',
            'instructions' => 'Number of days for the return window.',
            'default_value' => 30,
            'min' => 1,
            'max' => 365,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pr_return_policy',
                  'operator' => '==',
                  'value' => 'MerchantReturnFiniteReturnWindow',
                ),
              ),
            ),
          ),
          32 => 
          array (
            'key' => 'field_pr_shipping_type',
            'label' => 'Delivery Type',
            'name' => 'shipping_type',
            'type' => 'select',
            'instructions' => 'Digital = instant delivery with $0 shipping. Physical = standard shipping.',
            'choices' => 
            array (
              'digital' => 'Digital / Instant Download',
              'physical' => 'Physical Shipping',
            ),
            'default_value' => 'digital',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pr_product_type',
                  'operator' => '==',
                  'value' => 'Product',
                ),
              ),
            ),
          ),
          33 => 
          array (
            'key' => 'field_pr_shipping_country',
            'label' => 'Shipping Country',
            'name' => 'shipping_country',
            'type' => 'text',
            'instructions' => 'ISO 3166-1 alpha-2 country code (e.g., US, GB, IN). Used for shipping destination and return policy.',
            'default_value' => 'US',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pr_product_type',
                  'operator' => '==',
                  'value' => 'Product',
                ),
              ),
            ),
          ),
          34 => 
          array (
            'key' => 'field_pr_app_category',
            'label' => 'Application Category',
            'name' => 'app_category',
            'type' => 'select',
            'instructions' => 'Category for SoftwareApplication schema.',
            'choices' => 
            array (
              'WebApplication' => 'Web Application',
              'MobileApplication' => 'Mobile App',
              'DesktopApplication' => 'Desktop App',
              'BusinessApplication' => 'Business App',
              'DeveloperApplication' => 'Developer Tool',
              'EducationalApplication' => 'Education App',
              'MultimediaApplication' => 'Multimedia App',
              'SecurityApplication' => 'Security App',
              'BrowserApplication' => 'Browser',
              'UtilitiesApplication' => 'Utility',
            ),
            'default_value' => 'WebApplication',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pr_product_type',
                  'operator' => '==',
                  'value' => 'SoftwareApplication',
                ),
              ),
            ),
          ),
          35 => 
          array (
            'key' => 'field_pr_app_os',
            'label' => 'Operating System',
            'name' => 'app_os',
            'type' => 'text',
            'instructions' => 'e.g., Windows, macOS, Linux, Android, iOS, Web',
            'default_value' => 'Web',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_pr_product_type',
                  'operator' => '==',
                  'value' => 'SoftwareApplication',
                ),
              ),
            ),
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/product-review',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
      ),
    ),
  ),
  'pros-cons' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/pros-cons',
      'title' => 'Pros & Cons',
      'description' => 'Display a two-column pros and cons comparison block.',
      'category' => 'acf-blocks',
      'icon' => 'columns',
      'keywords' => 
      array (
        0 => 'pros',
        1 => 'cons',
        2 => 'comparison',
        3 => 'versus',
        4 => 'positive',
        5 => 'negative',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'pros-cons.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'anchor' => true,
        'mode' => true,
      ),
      'style' => 'file:./pros-cons.css',
      'editorStyle' => 'file:./pros-cons.css',
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'pc_show_first' => 'positive',
            'pc_pros_title' => 'Pros',
            'pc_pros_list' => '<ul><li><strong>Easy to use:</strong> Simple and intuitive interface</li><li><strong>Fast performance:</strong> Optimized for speed</li><li><strong>Great support:</strong> 24/7 customer assistance</li></ul>',
            'pc_cons_title' => 'Cons',
            'pc_cons_list' => '<ul><li><strong>Learning curve:</strong> Takes time to master advanced features</li><li><strong>Price:</strong> Premium features require subscription</li></ul>',
          ),
        ),
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_pros_cons_block',
        'title' => 'Pros & Cons Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_pc_content_tab',
            'label' => 'Content',
            'type' => 'tab',
            'placement' => 'top',
          ),
          1 => 
          array (
            'key' => 'field_pc_show_first',
            'label' => 'Show First',
            'name' => 'pc_show_first',
            'type' => 'select',
            'instructions' => 'Choose which side to display first',
            'choices' => 
            array (
              'negative' => 'Negative (Cons)',
              'positive' => 'Positive (Pros)',
            ),
            'default_value' => 'negative',
          ),
          2 => 
          array (
            'key' => 'field_pc_negative_tab',
            'label' => 'Negative Side',
            'type' => 'tab',
            'placement' => 'top',
          ),
          3 => 
          array (
            'key' => 'field_pc_cons_title',
            'label' => 'Title',
            'name' => 'pc_cons_title',
            'type' => 'text',
            'default_value' => 'Cons',
          ),
          4 => 
          array (
            'key' => 'field_pc_cons_list',
            'label' => 'List Items',
            'name' => 'pc_cons_list',
            'type' => 'wysiwyg',
            'instructions' => 'Add list items. Use bold for headings within items.',
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'default_value' => '<ul>
<li><strong>Item heading:</strong> Description text here.</li>
</ul>',
          ),
          5 => 
          array (
            'key' => 'field_pc_positive_tab',
            'label' => 'Positive Side',
            'type' => 'tab',
            'placement' => 'top',
          ),
          6 => 
          array (
            'key' => 'field_pc_pros_title',
            'label' => 'Title',
            'name' => 'pc_pros_title',
            'type' => 'text',
            'default_value' => 'Pros',
          ),
          7 => 
          array (
            'key' => 'field_pc_pros_list',
            'label' => 'List Items',
            'name' => 'pc_pros_list',
            'type' => 'wysiwyg',
            'instructions' => 'Add list items. Use bold for headings within items.',
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'default_value' => '<ul>
<li><strong>Item heading:</strong> Description text here.</li>
</ul>',
          ),
          8 => 
          array (
            'key' => 'field_pc_colors_tab',
            'label' => 'Colors',
            'type' => 'tab',
            'placement' => 'top',
          ),
          9 => 
          array (
            'key' => 'field_pc_neg_bg_color',
            'label' => 'Negative Background',
            'name' => 'pc_neg_bg_color',
            'type' => 'color_picker',
            'default_value' => '#fef2f2',
          ),
          10 => 
          array (
            'key' => 'field_pc_neg_border_color',
            'label' => 'Negative Border',
            'name' => 'pc_neg_border_color',
            'type' => 'color_picker',
            'default_value' => '#dc2626',
          ),
          11 => 
          array (
            'key' => 'field_pc_neg_title_color',
            'label' => 'Negative Title Color',
            'name' => 'pc_neg_title_color',
            'type' => 'color_picker',
            'default_value' => '#dc2626',
          ),
          12 => 
          array (
            'key' => 'field_pc_neg_icon_color',
            'label' => 'Negative Icon Color',
            'name' => 'pc_neg_icon_color',
            'type' => 'color_picker',
            'default_value' => '#dc2626',
          ),
          13 => 
          array (
            'key' => 'field_pc_pos_bg_color',
            'label' => 'Positive Background',
            'name' => 'pc_pos_bg_color',
            'type' => 'color_picker',
            'default_value' => '#f0fdf4',
          ),
          14 => 
          array (
            'key' => 'field_pc_pos_border_color',
            'label' => 'Positive Border',
            'name' => 'pc_pos_border_color',
            'type' => 'color_picker',
            'default_value' => '#16a34a',
          ),
          15 => 
          array (
            'key' => 'field_pc_pos_title_color',
            'label' => 'Positive Title Color',
            'name' => 'pc_pos_title_color',
            'type' => 'color_picker',
            'default_value' => '#16a34a',
          ),
          16 => 
          array (
            'key' => 'field_pc_pos_icon_color',
            'label' => 'Positive Icon Color',
            'name' => 'pc_pos_icon_color',
            'type' => 'color_picker',
            'default_value' => '#16a34a',
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/pros-cons',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
      ),
    ),
  ),
  'section-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/section-block',
      'title' => 'Section Block',
      'description' => 'A customizable container block that wraps inner blocks.',
      'category' => 'acf-blocks',
      'icon' => 'editor-insertmore',
      'keywords' => 
      array (
        0 => 'section',
        1 => 'container',
        2 => 'wrapper',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'section-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'jsx' => true,
        'mode' => true,
        'multiple' => true,
      ),
      'style' => 'file:./section-block.css',
      'editorStyle' => 'file:./section-block.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_section_block',
        'title' => 'Section Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_section_structure_tab',
            'label' => 'Structure',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
            'endpoint' => 0,
          ),
          1 => 
          array (
            'key' => 'field_acf_section_html_tag',
            'label' => 'HTML Tag',
            'name' => 'acf_section_html_tag',
            'type' => 'select',
            'instructions' => 'Choose the HTML tag for this section',
            'choices' => 
            array (
              'div' => 'div',
              'section' => 'section',
              'article' => 'article',
              'aside' => 'aside',
              'header' => 'header',
              'footer' => 'footer',
              'main' => 'main',
              'custom' => 'Custom tag',
            ),
            'default_value' => 'section',
            'wrapper' => 
            array (
              'width' => '50',
            ),
          ),
          2 => 
          array (
            'key' => 'field_acf_section_custom_tag',
            'label' => 'Custom HTML Tag',
            'name' => 'acf_section_custom_tag',
            'type' => 'text',
            'instructions' => 'Enter a custom HTML tag (only if \'Custom tag\' is selected above)',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_acf_section_html_tag',
                  'operator' => '==',
                  'value' => 'custom',
                ),
              ),
            ),
            'wrapper' => 
            array (
              'width' => '50',
            ),
          ),
          3 => 
          array (
            'key' => 'field_acf_section_id',
            'label' => 'Section ID',
            'name' => 'acf_section_id',
            'type' => 'text',
            'instructions' => 'Optional ID attribute for the section',
            'prepend' => '#',
            'wrapper' => 
            array (
              'width' => '50',
            ),
          ),
          4 => 
          array (
            'key' => 'field_acf_section_custom_class',
            'label' => 'Custom Classes',
            'name' => 'acf_section_custom_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS classes (space separated)',
            'wrapper' => 
            array (
              'width' => '50',
            ),
          ),
          5 => 
          array (
            'key' => 'field_section_utility_tab',
            'label' => 'Utility Classes',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
            'endpoint' => 0,
          ),
          6 => 
          array (
            'key' => 'field_acf_layout_class',
            'label' => 'Layout Classes',
            'name' => 'acf_layout_class',
            'type' => 'text',
            'instructions' => 'Add layout utility classes (container, flex, grid, etc.)',
            'placeholder' => 'e.g. container d-flex justify-content-between',
          ),
          7 => 
          array (
            'key' => 'field_acf_spacing_class',
            'label' => 'Spacing Classes',
            'name' => 'acf_spacing_class',
            'type' => 'text',
            'instructions' => 'Add spacing utility classes',
            'placeholder' => 'e.g. py-5 mt-4 mb-lg-5',
          ),
          8 => 
          array (
            'key' => 'field_acf_bg_class',
            'label' => 'Background Classes',
            'name' => 'acf_bg_class',
            'type' => 'text',
            'instructions' => 'Add background utility classes',
            'placeholder' => 'e.g. bg-dark bg-opacity-75',
          ),
          9 => 
          array (
            'key' => 'field_acf_text_class',
            'label' => 'Text Classes',
            'name' => 'acf_text_class',
            'type' => 'text',
            'instructions' => 'Add text utility classes',
            'placeholder' => 'e.g. text-center text-md-start',
          ),
          10 => 
          array (
            'key' => 'field_acf_responsive_class',
            'label' => 'Responsive Classes',
            'name' => 'acf_responsive_class',
            'type' => 'text',
            'instructions' => 'Add responsive utility classes',
            'placeholder' => 'e.g. d-none d-md-block',
          ),
          11 => 
          array (
            'key' => 'field_section_background_tab',
            'label' => 'Background',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
            'endpoint' => 0,
          ),
          12 => 
          array (
            'key' => 'field_acf_bg_color',
            'label' => 'Background Color',
            'name' => 'acf_bg_color',
            'type' => 'color_picker',
            'enable_opacity' => 1,
            'wrapper' => 
            array (
              'width' => '50',
            ),
          ),
          13 => 
          array (
            'key' => 'field_acf_bg_image',
            'label' => 'Background Image',
            'name' => 'acf_bg_image',
            'type' => 'image',
            'return_format' => 'url',
            'preview_size' => 'medium',
            'wrapper' => 
            array (
              'width' => '50',
            ),
          ),
          14 => 
          array (
            'key' => 'field_acf_bg_overlay',
            'label' => 'Background Overlay',
            'name' => 'acf_bg_overlay',
            'type' => 'color_picker',
            'instructions' => 'Overlay color (with transparency)',
            'enable_opacity' => 1,
            'wrapper' => 
            array (
              'width' => '50',
            ),
          ),
          15 => 
          array (
            'key' => 'field_acf_bg_video',
            'label' => 'Background Video',
            'name' => 'acf_bg_video',
            'type' => 'file',
            'instructions' => 'Upload MP4 video',
            'return_format' => 'url',
            'mime_types' => 'mp4',
            'wrapper' => 
            array (
              'width' => '50',
            ),
          ),
          16 => 
          array (
            'key' => 'field_section_custom_css_tab',
            'label' => 'Custom CSS',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
            'endpoint' => 0,
          ),
          17 => 
          array (
            'key' => 'field_acf_custom_css',
            'label' => 'Inline CSS',
            'name' => 'acf_custom_css',
            'type' => 'textarea',
            'instructions' => 'Add custom CSS for this block. Will be printed in the footer.',
            'placeholder' => '.section-content h2 { color: red; }
.section-content p { margin-bottom: 2em; }',
            'rows' => 10,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/section-block',
            ),
          ),
        ),
      ),
    ),
  ),
  'star-rating-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/star-rating',
      'title' => 'Star Rating',
      'description' => 'Collects visitor star ratings and displays the aggregate score with CreativeWork schema.',
      'category' => 'acf-blocks',
      'icon' => 'star-filled',
      'keywords' => 
      array (
        0 => 'rating',
        1 => 'feedback',
        2 => 'stars',
        3 => 'review',
        4 => 'schema',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'star-rating-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'anchor' => true,
        'jsx' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
      ),
      'style' => 'file:./star-rating-block.css',
      'editorStyle' => 'file:./star-rating-block.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_md_star_rating_block',
        'title' => 'Star Rating Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_md_sr_content_tab',
            'label' => 'Content',
            'type' => 'tab',
            'placement' => 'top',
          ),
          1 => 
          array (
            'key' => 'field_md_sr_heading',
            'label' => 'Heading',
            'name' => 'md_sr_heading',
            'type' => 'text',
            'instructions' => 'Optional heading displayed above the rating widget.',
            'required' => 0,
          ),
          2 => 
          array (
            'key' => 'field_md_sr_description',
            'label' => 'Description',
            'name' => 'md_sr_description',
            'type' => 'textarea',
            'instructions' => 'Add context or a call-to-action for the rating block.',
            'required' => 0,
            'rows' => 3,
          ),
          3 => 
          array (
            'key' => 'field_md_sr_button_label',
            'label' => 'Submit Button Label',
            'name' => 'md_sr_button_label',
            'type' => 'text',
            'instructions' => 'Text displayed on the submit button.',
            'default_value' => 'Submit Rating',
            'required' => 0,
          ),
          4 => 
          array (
            'key' => 'field_md_sr_thank_you',
            'label' => 'Thank You Message',
            'name' => 'md_sr_thank_you',
            'type' => 'text',
            'instructions' => 'Message displayed after a visitor submits a rating.',
            'default_value' => 'Thanks for rating!',
            'required' => 0,
          ),
          5 => 
          array (
            'key' => 'field_md_sr_prevoter_tab',
            'label' => 'Pre-Voter Data',
            'type' => 'tab',
            'placement' => 'top',
          ),
          6 => 
          array (
            'key' => 'field_md_sr_initial_count',
            'label' => 'Initial Vote Count',
            'name' => 'md_sr_initial_count',
            'type' => 'number',
            'instructions' => 'Add pre-existing vote count (e.g., 170 votes). Leave empty for 0.',
            'min' => 0,
            'default_value' => 0,
          ),
          7 => 
          array (
            'key' => 'field_md_sr_initial_rating',
            'label' => 'Initial Average Rating',
            'name' => 'md_sr_initial_rating',
            'type' => 'number',
            'instructions' => 'Add pre-existing average rating (e.g., 4.5 stars). Leave empty for 0.',
            'min' => 0,
            'max' => 5,
            'step' => 0.1,
            'default_value' => 0,
          ),
          8 => 
          array (
            'key' => 'field_md_sr_schema_tab',
            'label' => 'Schema & SEO',
            'type' => 'tab',
            'placement' => 'top',
          ),
          9 => 
          array (
            'key' => 'field_md_sr_enable_schema',
            'label' => 'Enable Schema Markup',
            'name' => 'md_sr_enable_schema',
            'type' => 'true_false',
            'instructions' => 'Add CreativeWork schema with AggregateRating for SEO.',
            'default_value' => 1,
            'ui' => 1,
          ),
          10 => 
          array (
            'key' => 'field_md_sr_schema_type',
            'label' => 'Schema Type',
            'name' => 'md_sr_schema_type',
            'type' => 'select',
            'instructions' => 'Choose the schema type for this content.',
            'choices' => 
            array (
              'CreativeWork' => 'Creative Work',
              'Article' => 'Article',
              'BlogPosting' => 'Blog Post',
              'WebPage' => 'Web Page',
              'HowTo' => 'How To Guide',
              'Recipe' => 'Recipe',
              'SoftwareApplication' => 'Software/App',
              'Product' => 'Product',
            ),
            'default_value' => 'CreativeWork',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_md_sr_enable_schema',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
          ),
          11 => 
          array (
            'key' => 'field_md_sr_schema_name',
            'label' => 'Item Name (Optional)',
            'name' => 'md_sr_schema_name',
            'type' => 'text',
            'instructions' => 'Override the page/post title for schema. Leave empty to use page title.',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_md_sr_enable_schema',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/star-rating',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
      ),
    ),
  ),
  'stats-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/stats',
      'title' => 'Stats/Counter',
      'description' => 'A stats block with animated counters to showcase numbers and achievements.',
      'category' => 'acf-blocks',
      'icon' => 'chart-bar',
      'keywords' => 
      array (
        0 => 'stats',
        1 => 'counter',
        2 => 'numbers',
        3 => 'metrics',
        4 => 'achievements',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'stats-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'jsx' => true,
        'anchor' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
      ),
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'acf_stats_layout' => 'horizontal',
            'acf_stats_items' => 
            array (
              0 => 
              array (
                'acf_stat_number' => 1000,
                'acf_stat_suffix' => '+',
                'acf_stat_label' => 'Happy Customers',
              ),
              1 => 
              array (
                'acf_stat_number' => 50,
                'acf_stat_suffix' => 'M',
                'acf_stat_label' => 'Downloads',
              ),
              2 => 
              array (
                'acf_stat_number' => 99,
                'acf_stat_suffix' => '%',
                'acf_stat_label' => 'Satisfaction Rate',
              ),
              3 => 
              array (
                'acf_stat_number' => 24,
                'acf_stat_suffix' => '/7',
                'acf_stat_label' => 'Support Available',
              ),
            ),
          ),
        ),
      ),
      'style' => 'file:./stats.css',
      'editorStyle' => 'file:./stats.css',
      'viewScript' => 'file:./stats-counter.js',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_stats_block',
        'title' => 'Stats/Counter Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'acf_stats_items',
            'label' => 'Stats Items',
            'name' => 'acf_stats_items',
            'type' => 'repeater',
            'instructions' => 'Add stats/counter items.',
            'required' => 0,
            'min' => 0,
            'layout' => 'block',
            'button_label' => 'Add Stat',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'acf_stat_number',
                'label' => 'Number',
                'name' => 'acf_stat_number',
                'type' => 'number',
                'instructions' => 'Enter the number to display.',
                'required' => 0,
              ),
              1 => 
              array (
                'key' => 'acf_stat_label',
                'label' => 'Label',
                'name' => 'acf_stat_label',
                'type' => 'text',
                'instructions' => 'Enter the label for this stat.',
                'required' => 0,
              ),
              2 => 
              array (
                'key' => 'acf_stat_prefix',
                'label' => 'Prefix',
                'name' => 'acf_stat_prefix',
                'type' => 'text',
                'instructions' => 'Enter a prefix (e.g., $, #).',
                'required' => 0,
              ),
              3 => 
              array (
                'key' => 'acf_stat_suffix',
                'label' => 'Suffix',
                'name' => 'acf_stat_suffix',
                'type' => 'text',
                'instructions' => 'Enter a suffix (e.g., +, %, K, M).',
                'required' => 0,
              ),
              4 => 
              array (
                'key' => 'acf_stat_icon',
                'label' => 'Icon',
                'name' => 'acf_stat_icon',
                'type' => 'text',
                'instructions' => 'Enter an icon (emoji, text, or CSS class like \'fa-star\').',
                'required' => 0,
              ),
            ),
          ),
          1 => 
          array (
            'key' => 'acf_stats_layout',
            'label' => 'Layout',
            'name' => 'acf_stats_layout',
            'type' => 'select',
            'instructions' => 'Select the layout for the stats.',
            'required' => 0,
            'choices' => 
            array (
              'horizontal' => 'Horizontal',
              'vertical' => 'Vertical',
              'grid' => 'Grid',
            ),
            'default_value' => 'horizontal',
          ),
          2 => 
          array (
            'key' => 'acf_stats_enable_animation',
            'label' => 'Enable Counter Animation',
            'name' => 'acf_stats_enable_animation',
            'type' => 'true_false',
            'instructions' => 'Enable animated counting effect.',
            'required' => 0,
            'default_value' => 1,
          ),
          3 => 
          array (
            'key' => 'acf_stats_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_stats_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          4 => 
          array (
            'key' => 'acf_stats_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_stats_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/stats',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'acf' => 
        array (
          'blockVersion' => 3,
        ),
      ),
    ),
  ),
  'tabs-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/tabs',
      'title' => 'Tabs',
      'description' => 'A tabbed content block for organizing information into switchable sections.',
      'category' => 'acf-blocks',
      'icon' => 'index-card',
      'keywords' => 
      array (
        0 => 'tabs',
        1 => 'tabbed',
        2 => 'content',
        3 => 'sections',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'tabs-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'jsx' => true,
        'anchor' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
      ),
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'acf_tabs_items' => 
            array (
              0 => 
              array (
                'acf_tab_title' => 'Overview',
                'acf_tab_content' => 'This is the overview content. Add your main introduction and key points here.',
              ),
              1 => 
              array (
                'acf_tab_title' => 'Features',
                'acf_tab_content' => 'List your features and benefits in this tab. Perfect for highlighting key aspects.',
              ),
              2 => 
              array (
                'acf_tab_title' => 'Pricing',
                'acf_tab_content' => 'Display pricing information and packages. Make it easy for visitors to compare options.',
              ),
            ),
          ),
        ),
      ),
      'style' => 'file:./tabs.css',
      'editorStyle' => 'file:./tabs.css',
      'viewScript' => 'file:./tabs.js',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_tabs_block',
        'title' => 'Tabs Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'acf_tabs_items',
            'label' => 'Tab Items',
            'name' => 'acf_tabs_items',
            'type' => 'repeater',
            'instructions' => 'Add tab items.',
            'required' => 0,
            'min' => 0,
            'layout' => 'block',
            'button_label' => 'Add Tab',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'acf_tab_title',
                'label' => 'Tab Title',
                'name' => 'acf_tab_title',
                'type' => 'text',
                'instructions' => 'Enter the title for this tab.',
                'required' => 0,
              ),
              1 => 
              array (
                'key' => 'acf_tab_icon',
                'label' => 'Tab Icon',
                'name' => 'acf_tab_icon',
                'type' => 'text',
                'instructions' => 'Enter an icon (emoji, text, or CSS class like \'fa-star\') for this tab.',
                'required' => 0,
              ),
              2 => 
              array (
                'key' => 'acf_tab_content',
                'label' => 'Tab Content',
                'name' => 'acf_tab_content',
                'type' => 'wysiwyg',
                'instructions' => 'Enter the content for this tab.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
              ),
            ),
          ),
          1 => 
          array (
            'key' => 'acf_tabs_style',
            'label' => 'Tab Style',
            'name' => 'acf_tabs_style',
            'type' => 'select',
            'instructions' => 'Select the style for the tabs.',
            'required' => 0,
            'choices' => 
            array (
              'default' => 'Default',
              'pills' => 'Pills',
              'underline' => 'Underline',
              'boxed' => 'Boxed',
            ),
            'default_value' => 'default',
          ),
          2 => 
          array (
            'key' => 'acf_tabs_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_tabs_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          3 => 
          array (
            'key' => 'acf_tabs_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_tabs_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/tabs',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'acf' => 
        array (
          'blockVersion' => 3,
        ),
      ),
    ),
  ),
  'team-member-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/team-member',
      'title' => 'Team Member',
      'description' => 'A team member block with photo, core blocks for name/title/bio, and social links.',
      'category' => 'acf-blocks',
      'icon' => 'groups',
      'keywords' => 
      array (
        0 => 'team',
        1 => 'member',
        2 => 'staff',
        3 => 'person',
        4 => 'profile',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'team-member-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'jsx' => true,
      ),
      'style' => 'file:./team-member.css',
      'editorStyle' => 'file:./team-member.css',
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'is_preview' => true,
          ),
        ),
        'innerBlocks' => 
        array (
          0 => 
          array (
            'name' => 'core/heading',
            'attributes' => 
            array (
              'level' => 3,
              'content' => 'Jane Smith',
            ),
          ),
          1 => 
          array (
            'name' => 'core/paragraph',
            'attributes' => 
            array (
              'content' => 'Product Manager',
              'className' => 'acf-team-member-title',
            ),
          ),
          2 => 
          array (
            'name' => 'core/paragraph',
            'attributes' => 
            array (
              'content' => 'Jane leads our product team with 10 years of experience in tech.',
            ),
          ),
        ),
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_team_member_block',
        'title' => 'Team Member Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'acf_team_member_photo',
            'label' => 'Photo',
            'name' => 'acf_team_member_photo',
            'type' => 'image',
            'instructions' => 'Upload a photo from the media library.',
            'required' => 0,
            'return_format' => 'array',
            'preview_size' => 'medium',
          ),
          1 => 
          array (
            'key' => 'acf_team_member_photo_url',
            'label' => 'Or Photo URL',
            'name' => 'acf_team_member_photo_url',
            'type' => 'url',
            'instructions' => 'Alternatively, enter a direct image URL. This takes priority over the uploaded image.',
            'required' => 0,
          ),
          2 => 
          array (
            'key' => 'acf_team_member_name',
            'label' => 'Name',
            'name' => 'acf_team_member_name',
            'type' => 'text',
            'instructions' => 'Enter the team member\'s name.',
            'required' => 0,
            'default_value' => 'Team Member Name',
          ),
          3 => 
          array (
            'key' => 'acf_team_member_title',
            'label' => 'Title/Position',
            'name' => 'acf_team_member_title',
            'type' => 'text',
            'instructions' => 'Enter the team member\'s title or position.',
            'required' => 0,
            'default_value' => 'Position Title',
          ),
          4 => 
          array (
            'key' => 'acf_team_member_bio',
            'label' => 'Bio',
            'name' => 'acf_team_member_bio',
            'type' => 'wysiwyg',
            'instructions' => 'Enter a short bio for the team member.',
            'required' => 0,
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'default_value' => 'A brief biography about this team member and their expertise.',
          ),
          5 => 
          array (
            'key' => 'acf_team_member_email',
            'label' => 'Email',
            'name' => 'acf_team_member_email',
            'type' => 'email',
            'instructions' => 'Enter the team member\'s email address.',
            'required' => 0,
          ),
          6 => 
          array (
            'key' => 'acf_team_member_phone',
            'label' => 'Phone',
            'name' => 'acf_team_member_phone',
            'type' => 'text',
            'instructions' => 'Enter the team member\'s phone number.',
            'required' => 0,
          ),
          7 => 
          array (
            'key' => 'acf_team_member_social_links',
            'label' => 'Social Links',
            'name' => 'acf_team_member_social_links',
            'type' => 'repeater',
            'instructions' => 'Add social media links for the team member.',
            'required' => 0,
            'min' => 0,
            'layout' => 'table',
            'button_label' => 'Add Social Link',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'acf_social_platform',
                'label' => 'Platform',
                'name' => 'acf_social_platform',
                'type' => 'select',
                'instructions' => 'Select the social media platform.',
                'required' => 0,
                'choices' => 
                array (
                  'LinkedIn' => 'LinkedIn',
                  'Twitter' => 'Twitter',
                  'Facebook' => 'Facebook',
                  'Instagram' => 'Instagram',
                  'GitHub' => 'GitHub',
                  'Website' => 'Website',
                ),
              ),
              1 => 
              array (
                'key' => 'acf_social_url',
                'label' => 'URL',
                'name' => 'acf_social_url',
                'type' => 'url',
                'instructions' => 'Enter the URL for this social platform.',
                'required' => 0,
              ),
            ),
          ),
          8 => 
          array (
            'key' => 'acf_team_member_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_team_member_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          9 => 
          array (
            'key' => 'acf_team_member_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_team_member_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/team-member',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'acf' => 
        array (
          'blockVersion' => 3,
        ),
      ),
    ),
  ),
  'testimonial-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/testimonial',
      'title' => 'Testimonial',
      'description' => 'A customizable testimonial block with core blocks for quote content, plus author info and rating.',
      'category' => 'acf-blocks',
      'icon' => 'format-quote',
      'keywords' => 
      array (
        0 => 'testimonial',
        1 => 'review',
        2 => 'quote',
        3 => 'customer',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'testimonial-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'jsx' => true,
        'anchor' => true,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
      ),
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'acf_testimonial_author_name' => 'Jane Smith',
            'acf_testimonial_author_title' => 'Product Manager at TechCorp',
            'acf_testimonial_rating' => 5,
          ),
        ),
        'innerBlocks' => 
        array (
          0 => 
          array (
            'name' => 'core/paragraph',
            'attributes' => 
            array (
              'content' => 'This plugin has transformed how we build our WordPress sites. The blocks are beautifully designed and incredibly easy to use.',
            ),
          ),
        ),
      ),
      'style' => 'file:./testimonial.css',
      'editorStyle' => 'file:./testimonial.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_testimonial_block',
        'title' => 'Testimonial Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'acf_testimonial_quote',
            'label' => 'Testimonial Quote',
            'name' => 'acf_testimonial_quote',
            'type' => 'wysiwyg',
            'instructions' => 'Enter the testimonial quote.',
            'required' => 0,
            'tabs' => 'visual',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'default_value' => 'This is an amazing product that has completely transformed my workflow.',
          ),
          1 => 
          array (
            'key' => 'acf_testimonial_author_name',
            'label' => 'Author Name',
            'name' => 'acf_testimonial_author_name',
            'type' => 'text',
            'instructions' => 'Enter the name of the person giving the testimonial.',
            'required' => 0,
            'default_value' => 'John Doe',
          ),
          2 => 
          array (
            'key' => 'acf_testimonial_author_title',
            'label' => 'Author Title/Company',
            'name' => 'acf_testimonial_author_title',
            'type' => 'text',
            'instructions' => 'Enter the author\'s title or company name.',
            'required' => 0,
            'default_value' => 'CEO, Company Name',
          ),
          3 => 
          array (
            'key' => 'acf_testimonial_author_image',
            'label' => 'Author Image',
            'name' => 'acf_testimonial_author_image',
            'type' => 'image',
            'instructions' => 'Upload an image from the media library.',
            'required' => 0,
            'return_format' => 'array',
            'preview_size' => 'thumbnail',
          ),
          4 => 
          array (
            'key' => 'acf_testimonial_author_image_url',
            'label' => 'Or Image URL',
            'name' => 'acf_testimonial_author_image_url',
            'type' => 'url',
            'instructions' => 'Alternatively, enter a direct image URL. This takes priority over the uploaded image.',
            'required' => 0,
          ),
          5 => 
          array (
            'key' => 'acf_testimonial_rating',
            'label' => 'Rating (1-5 stars)',
            'name' => 'acf_testimonial_rating',
            'type' => 'number',
            'instructions' => 'Enter a rating from 1 to 5 stars.',
            'required' => 0,
            'min' => 1,
            'max' => 5,
            'step' => 1,
          ),
          6 => 
          array (
            'key' => 'acf_testimonial_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_testimonial_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          7 => 
          array (
            'key' => 'acf_testimonial_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_testimonial_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/testimonial',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'acf' => 
        array (
          'blockVersion' => 3,
        ),
      ),
    ),
  ),
  'thread-builder' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/thread-builder',
      'title' => 'Thread Builder',
      'description' => 'Create Twitter X-style conversation threads.',
      'category' => 'acf-blocks',
      'icon' => 'format-chat',
      'keywords' => 
      array (
        0 => 'twitter',
        1 => 'thread',
        2 => 'conversation',
        3 => 'social',
        4 => 'tweet',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'thread-builder.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'anchor' => true,
      ),
      'style' => 'file:./thread-builder.css',
      'editorStyle' => 'file:./thread-builder.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_thread_builder',
        'title' => 'Thread Builder',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_thread_settings',
            'label' => 'Thread Settings',
            'name' => '',
            'aria-label' => '',
            'type' => 'tab',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => false,
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'placement' => 'top',
            'endpoint' => 0,
            'selected' => 0,
            'no_preference' => 0,
          ),
          1 => 
          array (
            'key' => 'field_thread_theme',
            'label' => 'Thread Theme',
            'name' => 'thread_theme',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => false,
            'conditional_logic' => false,
            'wrapper' => 
            array (
              'width' => '50',
              'class' => '',
              'id' => '',
            ),
            'choices' => 
            array (
              'light' => 'Light Mode',
              'dark' => 'Dark Mode',
            ),
            'default_value' => 'light',
            'multiple' => 0,
            'allow_null' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'return_format' => 'value',
            'allow_custom' => 0,
            'search_placeholder' => '',
            'prepend' => '',
            'append' => '',
            'min' => '',
            'max' => '',
          ),
          2 => 
          array (
            'key' => 'field_thread_width',
            'label' => 'Thread Width',
            'name' => 'thread_width',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => false,
            'conditional_logic' => false,
            'wrapper' => 
            array (
              'width' => '50',
              'class' => '',
              'id' => '',
            ),
            'choices' => 
            array (
              'narrow' => 'Narrow (400px)',
              'medium' => 'Medium (500px)',
              'wide' => 'Wide (600px)',
              'full' => 'Full Width',
            ),
            'default_value' => 'medium',
            'multiple' => 0,
            'allow_null' => 0,
            'ui' => 0,
            'ajax' => 0,
            'placeholder' => '',
            'return_format' => 'value',
            'allow_custom' => 0,
            'search_placeholder' => '',
            'prepend' => '',
            'append' => '',
            'min' => '',
            'max' => '',
          ),
          3 => 
          array (
            'key' => 'field_thread_show_connector',
            'label' => 'Show Thread Connector',
            'name' => 'thread_show_connector',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => '',
            'required' => false,
            'conditional_logic' => false,
            'wrapper' => 
            array (
              'width' => '50',
              'class' => '',
              'id' => '',
            ),
            'default_value' => 1,
            'ui' => 1,
            'message' => '',
            'ui_on_text' => '',
            'ui_off_text' => '',
            'style' => '',
          ),
          4 => 
          array (
            'key' => 'field_thread_connector_color',
            'label' => 'Connector Color',
            'name' => 'thread_connector_color',
            'aria-label' => '',
            'type' => 'color_picker',
            'instructions' => '',
            'required' => false,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_thread_show_connector',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
            'wrapper' => 
            array (
              'width' => '50',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'enable_opacity' => false,
            'return_format' => 'string',
            'display' => 'default',
            'button_label' => 'Select Color',
            'color_picker' => true,
            'absolute' => false,
            'input' => true,
            'allow_null' => true,
            'theme_colors' => false,
            'colors' => 
            array (
            ),
          ),
          5 => 
          array (
            'key' => 'field_thread_show_engagement',
            'label' => 'Show Engagement Stats',
            'name' => 'thread_show_engagement',
            'aria-label' => '',
            'type' => 'true_false',
            'instructions' => '',
            'required' => false,
            'conditional_logic' => false,
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'default_value' => 1,
            'ui' => 1,
            'message' => '',
            'ui_on_text' => '',
            'ui_off_text' => '',
            'style' => '',
          ),
          6 => 
          array (
            'key' => 'field_thread_posts',
            'label' => 'Thread Posts',
            'name' => '',
            'aria-label' => '',
            'type' => 'tab',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => false,
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'placement' => 'top',
            'endpoint' => 0,
            'selected' => 0,
            'no_preference' => 0,
          ),
          7 => 
          array (
            'key' => 'field_thread_posts_repeater',
            'label' => 'Posts',
            'name' => 'thread_posts',
            'aria-label' => '',
            'type' => 'repeater',
            'instructions' => '',
            'required' => false,
            'conditional_logic' => false,
            'wrapper' => 
            array (
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'layout' => 'block',
            'min' => 0,
            'button_label' => 'Add Post',
            'max' => 0,
            'rows_per_page' => 20,
            'collapsed' => '',
            'acfe_repeater_stylised_button' => 0,
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_thread_post_profile_tab',
                'label' => 'Profile',
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '',
                  'class' => '',
                  'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
                'selected' => 0,
                'no_preference' => 0,
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              1 => 
              array (
                'key' => 'field_thread_post_author_name',
                'label' => 'Author Name',
                'name' => 'author_name',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '50',
                  'class' => '',
                  'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              2 => 
              array (
                'key' => 'field_thread_post_author_handle',
                'label' => 'Author Handle',
                'name' => 'author_handle',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '50',
                  'class' => '',
                  'id' => '',
                ),
                'placeholder' => '@username',
                'default_value' => '',
                'maxlength' => '',
                'prepend' => '',
                'append' => '',
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              3 => 
              array (
                'key' => 'field_thread_post_author_avatar',
                'label' => 'Author Avatar',
                'name' => 'author_avatar',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '',
                  'class' => '',
                  'id' => '',
                ),
                'return_format' => 'url',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'min_width' => 0,
                'min_height' => 0,
                'min_size' => 0,
                'max_width' => 0,
                'max_height' => 0,
                'max_size' => 0,
                'mime_types' => '',
                'uploader' => '',
                'acfe_thumbnail' => 0,
                'upload_folder' => '',
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              4 => 
              array (
                'key' => 'field_thread_post_verified',
                'label' => 'Verified Account',
                'name' => 'verified',
                'aria-label' => '',
                'type' => 'true_false',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '',
                  'class' => '',
                  'id' => '',
                ),
                'ui' => 1,
                'default_value' => 0,
                'message' => '',
                'ui_on_text' => '',
                'ui_off_text' => '',
                'style' => '',
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              5 => 
              array (
                'key' => 'field_thread_post_content_tab',
                'label' => 'Content',
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '',
                  'class' => '',
                  'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
                'selected' => 0,
                'no_preference' => 0,
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              6 => 
              array (
                'key' => 'field_thread_post_content',
                'label' => 'Post Content',
                'name' => 'content',
                'aria-label' => '',
                'type' => 'textarea',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '',
                  'class' => '',
                  'id' => '',
                ),
                'rows' => 5,
                'default_value' => '',
                'new_lines' => '',
                'maxlength' => '',
                'placeholder' => '',
                'acfe_textarea_code' => 0,
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              7 => 
              array (
                'key' => 'field_thread_post_media',
                'label' => 'Attach Media',
                'name' => 'media',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '',
                  'class' => '',
                  'id' => '',
                ),
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'min_width' => 0,
                'min_height' => 0,
                'min_size' => 0,
                'max_width' => 0,
                'max_height' => 0,
                'max_size' => 0,
                'mime_types' => '',
                'uploader' => '',
                'acfe_thumbnail' => 0,
                'upload_folder' => '',
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              8 => 
              array (
                'key' => 'field_thread_post_timestamp',
                'label' => 'Timestamp',
                'name' => 'timestamp',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '100',
                  'class' => '',
                  'id' => '',
                ),
                'placeholder' => '10:30 AM · May 15, 2023',
                'default_value' => '',
                'maxlength' => '',
                'prepend' => '',
                'append' => '',
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              9 => 
              array (
                'key' => 'field_thread_post_engagement_tab',
                'label' => 'Engagement',
                'name' => '',
                'aria-label' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 
                array (
                  0 => 
                  array (
                    0 => 
                    array (
                      'field' => 'field_thread_show_engagement',
                      'operator' => '==',
                      'value' => '1',
                    ),
                  ),
                ),
                'wrapper' => 
                array (
                  'width' => '',
                  'class' => '',
                  'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
                'selected' => 0,
                'no_preference' => 0,
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              10 => 
              array (
                'key' => 'field_thread_post_replies',
                'label' => 'Replies',
                'name' => 'replies',
                'aria-label' => '',
                'type' => 'number',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '33',
                  'class' => '',
                  'id' => '',
                ),
                'default_value' => 0,
                'min' => '',
                'max' => '',
                'step' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              11 => 
              array (
                'key' => 'field_thread_post_reposts',
                'label' => 'Reposts',
                'name' => 'reposts',
                'aria-label' => '',
                'type' => 'number',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '33',
                  'class' => '',
                  'id' => '',
                ),
                'default_value' => 0,
                'min' => '',
                'max' => '',
                'step' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
              12 => 
              array (
                'key' => 'field_thread_post_likes',
                'label' => 'Likes',
                'name' => 'likes',
                'aria-label' => '',
                'type' => 'number',
                'instructions' => '',
                'required' => false,
                'conditional_logic' => false,
                'wrapper' => 
                array (
                  'width' => '33',
                  'class' => '',
                  'id' => '',
                ),
                'default_value' => 0,
                'min' => '',
                'max' => '',
                'step' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'parent_repeater' => 'field_thread_posts_repeater',
              ),
            ),
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/thread-builder',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
        'acfe_autosync' => '',
        'acfe_form' => 0,
        'acfe_display_title' => '',
        'acfe_meta' => '',
        'acfe_note' => '',
      ),
    ),
  ),
  'toc-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/toc',
      'title' => 'Table of Contents',
      'description' => 'Display an SEO-optimized table of contents with schema markup, multiple list styles, and optional sticky behavior.',
      'category' => 'acf-blocks',
      'icon' => 'list-view',
      'keywords' => 
      array (
        0 => 'toc',
        1 => 'table of contents',
        2 => 'navigation',
        3 => 'headings',
        4 => 'index',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'toc-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => 
        array (
          0 => 'wide',
          1 => 'full',
        ),
        'mode' => true,
        'anchor' => true,
        'jsx' => false,
      ),
      'styles' => 
      array (
        0 => 
        array (
          'name' => 'default',
          'label' => 'Default',
          'isDefault' => true,
        ),
      ),
      'style' => 
      array (
        0 => 'file:./toc-block.css',
        1 => 'file:./toc-runtime.css',
      ),
      'editorStyle' => 
      array (
        0 => 'file:./toc-block.css',
        1 => 'file:./toc-runtime.css',
      ),
      'viewScript' => 'file:./toc.js',
      'example' => 
      array (
        'attributes' => 
        array (
          'mode' => 'preview',
          'data' => 
          array (
            'toc_title' => 'Table of Contents',
            'toc_list_type' => 'ul',
            'toc_heading_levels' => 
            array (
              0 => 'h2',
              1 => 'h3',
            ),
          ),
        ),
      ),
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_toc_block',
        'title' => 'Table of Contents Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_toc_tab_content',
            'label' => 'Content',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          1 => 
          array (
            'key' => 'field_toc_title',
            'label' => 'Title',
            'name' => 'toc_title',
            'type' => 'text',
            'default_value' => 'Table of Contents',
            'placeholder' => 'Table of Contents',
          ),
          2 => 
          array (
            'key' => 'field_toc_title_tag',
            'label' => 'Title Tag',
            'name' => 'toc_title_tag',
            'type' => 'select',
            'choices' => 
            array (
              'p' => 'Paragraph',
              'h2' => 'H2',
              'h3' => 'H3',
              'h4' => 'H4',
              'h5' => 'H5',
              'h6' => 'H6',
              'div' => 'Div',
              'span' => 'Span',
            ),
            'default_value' => 'p',
            'instructions' => 'HTML tag for the TOC title. Use paragraph or div to avoid including the title in the TOC itself.',
          ),
          3 => 
          array (
            'key' => 'field_toc_heading_levels',
            'label' => 'Heading Levels to Include',
            'name' => 'toc_heading_levels',
            'type' => 'checkbox',
            'choices' => 
            array (
              'h1' => 'H1',
              'h2' => 'H2',
              'h3' => 'H3',
              'h4' => 'H4',
              'h5' => 'H5',
              'h6' => 'H6',
            ),
            'default_value' => 
            array (
              0 => 'h2',
            ),
            'layout' => 'horizontal',
            'instructions' => 'Select which heading levels to include in the table of contents.',
          ),
          4 => 
          array (
            'key' => 'field_toc_include_acf_headings',
            'label' => 'Include ACF Block Headings',
            'name' => 'toc_include_acf_block_headings',
            'type' => 'true_false',
            'default_value' => 0,
            'ui' => 1,
            'instructions' => 'When enabled, headings inside ACF blocks (product boxes, pros/cons, etc.) will be included in the table of contents. Off by default.',
          ),
          5 => 
          array (
            'key' => 'field_toc_list_type',
            'label' => 'List Type',
            'name' => 'toc_list_type',
            'type' => 'select',
            'choices' => 
            array (
              'ol' => 'Ordered List (with hierarchy)',
              'ul' => 'Unordered List (with hierarchy)',
              'plain' => 'Plain List (no hierarchy)',
            ),
            'default_value' => 'ul',
            'instructions' => 'Ordered/unordered lists preserve heading hierarchy. Plain list shows all headings at the same level.',
          ),
          6 => 
          array (
            'key' => 'field_toc_collapsible',
            'label' => 'Collapsible',
            'name' => 'toc_collapsible',
            'type' => 'true_false',
            'default_value' => 0,
            'ui' => 1,
            'instructions' => 'Allow users to collapse/expand the table of contents.',
          ),
          7 => 
          array (
            'key' => 'field_toc_collapsed_default',
            'label' => 'Collapsed by Default',
            'name' => 'toc_collapsed_default',
            'type' => 'true_false',
            'default_value' => 0,
            'ui' => 1,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_toc_collapsible',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
            'instructions' => 'Start with the TOC collapsed.',
          ),
          8 => 
          array (
            'key' => 'field_toc_tab_display',
            'label' => 'Display',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          9 => 
          array (
            'key' => 'field_toc_sticky',
            'label' => 'Sticky on Large Screens',
            'name' => 'toc_sticky',
            'type' => 'true_false',
            'default_value' => 0,
            'ui' => 1,
            'instructions' => 'Make the TOC sticky (fixed position) on screens 1400px and wider. Uses CSS position: sticky.',
          ),
          10 => 
          array (
            'key' => 'field_toc_sticky_offset',
            'label' => 'Sticky Top Offset',
            'name' => 'toc_sticky_offset',
            'type' => 'number',
            'default_value' => 20,
            'prepend' => '',
            'append' => 'px',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_toc_sticky',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
            'instructions' => 'Distance from the top of the viewport when sticky.',
          ),
          11 => 
          array (
            'key' => 'field_toc_smooth_scroll',
            'label' => 'Smooth Scroll',
            'name' => 'toc_smooth_scroll',
            'type' => 'true_false',
            'default_value' => 1,
            'ui' => 1,
            'instructions' => 'Enable smooth scrolling when clicking TOC links. Uses native CSS scroll-behavior.',
          ),
          12 => 
          array (
            'key' => 'field_toc_highlight_active',
            'label' => 'Highlight Active Section',
            'name' => 'toc_highlight_active',
            'type' => 'true_false',
            'default_value' => 0,
            'ui' => 1,
            'instructions' => 'Highlight the currently visible section in the TOC using Intersection Observer.',
          ),
          13 => 
          array (
            'key' => 'field_toc_tab_styling',
            'label' => 'Styling',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          14 => 
          array (
            'key' => 'field_toc_custom_class',
            'label' => 'Custom CSS Classes',
            'name' => 'toc_custom_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS classes to the TOC container. Separate multiple classes with spaces.',
          ),
          15 => 
          array (
            'key' => 'field_toc_title_class',
            'label' => 'Title CSS Classes',
            'name' => 'toc_title_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS classes to the TOC title element.',
          ),
          16 => 
          array (
            'key' => 'field_toc_list_class',
            'label' => 'List CSS Classes',
            'name' => 'toc_list_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS classes to the TOC list element.',
          ),
          17 => 
          array (
            'key' => 'field_toc_link_class',
            'label' => 'Link CSS Classes',
            'name' => 'toc_link_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS classes to TOC link elements.',
          ),
          18 => 
          array (
            'key' => 'field_toc_tab_seo',
            'label' => 'SEO',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          19 => 
          array (
            'key' => 'field_toc_schema',
            'label' => 'Include Schema Markup',
            'name' => 'toc_schema',
            'type' => 'true_false',
            'default_value' => 1,
            'ui' => 1,
            'instructions' => 'Add JSON-LD schema markup for better SEO. Includes SiteNavigationElement schema.',
          ),
          20 => 
          array (
            'key' => 'field_toc_aria_label',
            'label' => 'ARIA Label',
            'name' => 'toc_aria_label',
            'type' => 'text',
            'default_value' => 'Table of Contents',
            'instructions' => 'Accessible label for the navigation landmark. Important for screen readers.',
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/toc',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
      ),
    ),
  ),
  'url-preview' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/url-preview',
      'title' => 'URL Preview Card',
      'description' => 'Fetches Open Graph data from a URL and displays it as a product-like card with optional custom fields.',
      'category' => 'acf-blocks',
      'icon' => 'admin-links',
      'keywords' => 
      array (
        0 => 'url',
        1 => 'preview',
        2 => 'card',
        3 => 'product',
        4 => 'link',
        5 => 'opengraph',
        6 => 'og',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'url-preview.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'jsx' => true,
      ),
      'style' => 'file:./url-preview.css',
      'editorStyle' => 'file:./url-preview.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_url_preview_block',
        'title' => 'URL Preview Card',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'field_url_preview_tab_content',
            'label' => 'Content',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          1 => 
          array (
            'key' => 'field_url_preview_source_url',
            'label' => 'Source URL',
            'name' => 'source_url',
            'type' => 'url',
            'instructions' => 'Enter the URL to fetch Open Graph data from. Click \'Fetch Data\' to auto-populate title, description, and image.',
            'required' => 0,
            'placeholder' => 'https://example.com/page',
          ),
          2 => 
          array (
            'key' => 'field_url_preview_fetch_message',
            'label' => '',
            'name' => '',
            'type' => 'message',
            'message' => '<button type="button" class="button button-primary acf-url-preview-fetch-btn" style="margin-top: 5px;">Fetch Data from URL</button><span class="acf-url-preview-fetch-status" style="margin-left: 10px;"></span>',
            'new_lines' => '',
            'esc_html' => 0,
          ),
          3 => 
          array (
            'key' => 'field_url_preview_title',
            'label' => 'Title',
            'name' => 'preview_title',
            'type' => 'text',
            'instructions' => 'The title of the preview card. Auto-populated from Open Graph or page title.',
            'required' => 0,
            'placeholder' => 'Page Title',
          ),
          4 => 
          array (
            'key' => 'field_url_preview_description',
            'label' => 'Description',
            'name' => 'preview_description',
            'type' => 'textarea',
            'instructions' => 'The description for the preview card. Auto-populated from meta description or Open Graph.',
            'required' => 0,
            'rows' => 3,
            'placeholder' => 'Page description...',
          ),
          5 => 
          array (
            'key' => 'field_url_preview_tab_image',
            'label' => 'Image',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          6 => 
          array (
            'key' => 'field_url_preview_image_source',
            'label' => 'Image Source',
            'name' => 'image_source',
            'type' => 'button_group',
            'instructions' => 'Choose whether to use the external image URL or import to media library.',
            'required' => 0,
            'choices' => 
            array (
              'external' => 'External URL',
              'local' => 'Media Library',
            ),
            'default_value' => 'external',
            'layout' => 'horizontal',
          ),
          7 => 
          array (
            'key' => 'field_url_preview_external_image',
            'label' => 'External Image URL',
            'name' => 'external_image_url',
            'type' => 'url',
            'instructions' => 'The URL of the image to display. Auto-populated from Open Graph image or first suitable image.',
            'required' => 0,
            'placeholder' => 'https://example.com/image.jpg',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_image_source',
                  'operator' => '==',
                  'value' => 'external',
                ),
              ),
            ),
          ),
          8 => 
          array (
            'key' => 'field_url_preview_local_image',
            'label' => 'Media Library Image',
            'name' => 'local_image',
            'type' => 'image',
            'instructions' => 'Select an image from the media library or import from external URL.',
            'required' => 0,
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_image_source',
                  'operator' => '==',
                  'value' => 'local',
                ),
              ),
            ),
          ),
          9 => 
          array (
            'key' => 'field_url_preview_local_image_size',
            'label' => 'Image Size',
            'name' => 'local_image_size',
            'type' => 'select',
            'instructions' => 'Choose the thumbnail size for local images. Horizontal layout uses \'thumbnail\' by default.',
            'required' => 0,
            'choices' => 
            array (
              'thumbnail' => 'Thumbnail (150×150)',
              'medium' => 'Medium (300×300)',
              'medium_large' => 'Medium Large (768×auto)',
              'large' => 'Large (1024×1024)',
              'full' => 'Full Size',
            ),
            'default_value' => 'medium_large',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_image_source',
                  'operator' => '==',
                  'value' => 'local',
                ),
              ),
            ),
          ),
          10 => 
          array (
            'key' => 'field_url_preview_import_message',
            'label' => '',
            'name' => '',
            'type' => 'message',
            'message' => '<button type="button" class="button acf-url-preview-import-btn">Import External Image to Media Library</button><span class="acf-url-preview-import-status" style="margin-left: 10px;"></span>',
            'new_lines' => '',
            'esc_html' => 0,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_image_source',
                  'operator' => '==',
                  'value' => 'local',
                ),
              ),
            ),
          ),
          11 => 
          array (
            'key' => 'field_url_preview_image_alt',
            'label' => 'Image Alt Text',
            'name' => 'image_alt',
            'type' => 'text',
            'instructions' => 'Alternative text for the image (for accessibility).',
            'required' => 0,
            'placeholder' => 'Descriptive text for the image',
          ),
          12 => 
          array (
            'key' => 'field_url_preview_tab_data',
            'label' => 'Custom Data',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          13 => 
          array (
            'key' => 'field_url_preview_custom_fields',
            'label' => 'Custom Data Fields',
            'name' => 'custom_fields',
            'type' => 'repeater',
            'instructions' => 'Add custom data fields like Price, Expiry Date, Rating, etc.',
            'required' => 0,
            'min' => 0,
            'max' => 10,
            'layout' => 'table',
            'button_label' => 'Add Field',
            'sub_fields' => 
            array (
              0 => 
              array (
                'key' => 'field_url_preview_field_label',
                'label' => 'Label',
                'name' => 'field_label',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'placeholder' => 'e.g., Price',
                'wrapper' => 
                array (
                  'width' => '40',
                ),
              ),
              1 => 
              array (
                'key' => 'field_url_preview_field_value',
                'label' => 'Value',
                'name' => 'field_value',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'placeholder' => 'e.g., $9.99',
                'wrapper' => 
                array (
                  'width' => '40',
                ),
              ),
              2 => 
              array (
                'key' => 'field_url_preview_field_icon',
                'label' => 'Icon',
                'name' => 'field_icon',
                'type' => 'select',
                'instructions' => '',
                'required' => 0,
                'choices' => 
                array (
                  'none' => 'None',
                  'price' => 'Price Tag',
                  'calendar' => 'Calendar',
                  'star' => 'Star',
                  'check' => 'Checkmark',
                  'info' => 'Info',
                  'clock' => 'Clock',
                  'percent' => 'Percent',
                  'gift' => 'Gift',
                  'truck' => 'Shipping',
                ),
                'default_value' => 'none',
                'wrapper' => 
                array (
                  'width' => '20',
                ),
              ),
            ),
          ),
          14 => 
          array (
            'key' => 'field_url_preview_tab_button',
            'label' => 'Button',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          15 => 
          array (
            'key' => 'field_url_preview_show_button',
            'label' => 'Show Button',
            'name' => 'show_button',
            'type' => 'true_false',
            'instructions' => 'Display a call-to-action button on the card.',
            'required' => 0,
            'default_value' => 1,
            'ui' => 1,
          ),
          16 => 
          array (
            'key' => 'field_url_preview_button_text',
            'label' => 'Button Text',
            'name' => 'button_text',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'default_value' => 'View Details',
            'placeholder' => 'View Details',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_show_button',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
          ),
          17 => 
          array (
            'key' => 'field_url_preview_button_url',
            'label' => 'Button URL',
            'name' => 'button_url',
            'type' => 'url',
            'instructions' => 'Leave empty to use the source URL.',
            'required' => 0,
            'placeholder' => 'https://example.com',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_show_button',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
          ),
          18 => 
          array (
            'key' => 'field_url_preview_button_new_tab',
            'label' => 'Open in New Tab',
            'name' => 'button_new_tab',
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'default_value' => 1,
            'ui' => 1,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_show_button',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
          ),
          19 => 
          array (
            'key' => 'field_url_preview_button_rel',
            'label' => 'Add nofollow',
            'name' => 'button_nofollow',
            'type' => 'true_false',
            'instructions' => 'Add rel="nofollow" to the button link.',
            'required' => 0,
            'default_value' => 0,
            'ui' => 1,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_show_button',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
          ),
          20 => 
          array (
            'key' => 'field_url_preview_show_secondary_button',
            'label' => 'Show Secondary Button',
            'name' => 'show_secondary_button',
            'type' => 'true_false',
            'instructions' => 'Display a secondary button with outline style.',
            'required' => 0,
            'default_value' => 0,
            'ui' => 1,
          ),
          21 => 
          array (
            'key' => 'field_url_preview_secondary_button_text',
            'label' => 'Secondary Button Text',
            'name' => 'secondary_button_text',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'placeholder' => 'Learn More',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_show_secondary_button',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
          ),
          22 => 
          array (
            'key' => 'field_url_preview_secondary_button_url',
            'label' => 'Secondary Button URL',
            'name' => 'secondary_button_url',
            'type' => 'url',
            'instructions' => '',
            'required' => 0,
            'placeholder' => 'https://example.com',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_show_secondary_button',
                  'operator' => '==',
                  'value' => '1',
                ),
              ),
            ),
          ),
          23 => 
          array (
            'key' => 'field_url_preview_tab_style',
            'label' => 'Style',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
          ),
          24 => 
          array (
            'key' => 'field_url_preview_layout',
            'label' => 'Card Layout',
            'name' => 'card_layout',
            'type' => 'button_group',
            'instructions' => 'Choose the card layout style.',
            'required' => 0,
            'choices' => 
            array (
              'vertical' => 'Vertical',
              'horizontal' => 'Horizontal',
            ),
            'default_value' => 'vertical',
            'layout' => 'horizontal',
          ),
          25 => 
          array (
            'key' => 'field_url_preview_card_style',
            'label' => 'Card Style',
            'name' => 'card_style',
            'type' => 'select',
            'instructions' => 'Choose a visual style for the card.',
            'required' => 0,
            'choices' => 
            array (
              'default' => 'Default',
              'compact' => 'Compact',
              'minimal' => 'Minimal',
              'featured' => 'Featured',
              'dark' => 'Dark',
            ),
            'default_value' => 'default',
          ),
          26 => 
          array (
            'key' => 'field_url_preview_image_position',
            'label' => 'Image Position',
            'name' => 'image_position',
            'type' => 'button_group',
            'instructions' => '',
            'required' => 0,
            'choices' => 
            array (
              'left' => 'Left',
              'right' => 'Right',
            ),
            'default_value' => 'left',
            'layout' => 'horizontal',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'field_url_preview_layout',
                  'operator' => '==',
                  'value' => 'horizontal',
                ),
              ),
            ),
          ),
          27 => 
          array (
            'key' => 'field_url_preview_custom_class',
            'label' => 'Custom CSS Class',
            'name' => 'custom_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS classes (space-separated).',
            'required' => 0,
            'placeholder' => 'my-custom-class',
          ),
          28 => 
          array (
            'key' => 'field_url_preview_custom_inline',
            'label' => 'Custom Inline Styles',
            'name' => 'custom_inline',
            'type' => 'text',
            'instructions' => 'Add custom inline CSS styles.',
            'required' => 0,
            'placeholder' => 'margin-bottom: 20px;',
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/url-preview',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'show_in_rest' => 0,
      ),
    ),
  ),
  'video-block' => 
  array (
    'metadata' => 
    array (
      'apiVersion' => 3,
      'name' => 'acf/video',
      'title' => 'Video',
      'description' => 'A responsive video block supporting YouTube, Vimeo, and self-hosted videos.',
      'category' => 'acf-blocks',
      'icon' => 'video-alt3',
      'keywords' => 
      array (
        0 => 'video',
        1 => 'youtube',
        2 => 'vimeo',
        3 => 'media',
        4 => 'embed',
      ),
      'acf' => 
      array (
        'renderTemplate' => 'video-block.php',
        'blockVersion' => 3,
      ),
      'supports' => 
      array (
        'align' => true,
        'mode' => true,
        'jsx' => true,
      ),
      'style' => 'file:./video.css',
      'editorStyle' => 'file:./video.css',
    ),
    'field_groups' => 
    array (
      0 => 
      array (
        'key' => 'group_acf_video_block',
        'title' => 'Video Block',
        'fields' => 
        array (
          0 => 
          array (
            'key' => 'acf_video_type',
            'label' => 'Video Type',
            'name' => 'acf_video_type',
            'type' => 'select',
            'instructions' => 'Select the type of video.',
            'required' => 0,
            'choices' => 
            array (
              'youtube' => 'YouTube',
              'vimeo' => 'Vimeo',
              'self-hosted' => 'Self-Hosted',
            ),
            'default_value' => 'youtube',
          ),
          1 => 
          array (
            'key' => 'acf_video_url',
            'label' => 'Video URL',
            'name' => 'acf_video_url',
            'type' => 'url',
            'instructions' => 'Enter the video URL. For self-hosted, a direct URL (e.g. CDN) overrides the uploaded file.',
            'required' => 0,
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'acf_video_type',
                  'operator' => '==',
                  'value' => 'youtube',
                ),
              ),
              1 => 
              array (
                0 => 
                array (
                  'field' => 'acf_video_type',
                  'operator' => '==',
                  'value' => 'vimeo',
                ),
              ),
              2 => 
              array (
                0 => 
                array (
                  'field' => 'acf_video_type',
                  'operator' => '==',
                  'value' => 'self-hosted',
                ),
              ),
            ),
          ),
          2 => 
          array (
            'key' => 'acf_video_file',
            'label' => 'Video File',
            'name' => 'acf_video_file',
            'type' => 'file',
            'instructions' => 'Upload a video file (MP4, WebM, etc.). Ignored if a Video URL is provided.',
            'required' => 0,
            'return_format' => 'array',
            'mime_types' => 'mp4,webm,ogg',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'acf_video_type',
                  'operator' => '==',
                  'value' => 'self-hosted',
                ),
              ),
            ),
          ),
          3 => 
          array (
            'key' => 'acf_video_poster',
            'label' => 'Poster Image',
            'name' => 'acf_video_poster',
            'type' => 'image',
            'instructions' => 'Upload a poster/thumbnail image for the video.',
            'required' => 0,
            'return_format' => 'array',
            'preview_size' => 'medium',
            'conditional_logic' => 
            array (
              0 => 
              array (
                0 => 
                array (
                  'field' => 'acf_video_type',
                  'operator' => '==',
                  'value' => 'self-hosted',
                ),
              ),
            ),
          ),
          4 => 
          array (
            'key' => 'acf_video_title',
            'label' => 'Video Title',
            'name' => 'acf_video_title',
            'type' => 'text',
            'instructions' => 'Enter a title for the video (optional).',
            'required' => 0,
          ),
          5 => 
          array (
            'key' => 'acf_video_caption',
            'label' => 'Video Caption',
            'name' => 'acf_video_caption',
            'type' => 'text',
            'instructions' => 'Enter a caption for the video (optional).',
            'required' => 0,
          ),
          6 => 
          array (
            'key' => 'acf_video_aspect_ratio',
            'label' => 'Aspect Ratio',
            'name' => 'acf_video_aspect_ratio',
            'type' => 'select',
            'instructions' => 'Select the aspect ratio for the video.',
            'required' => 0,
            'choices' => 
            array (
              '16-9' => '16:9 (Widescreen)',
              '4-3' => '4:3 (Standard)',
              '21-9' => '21:9 (Ultrawide)',
              '1-1' => '1:1 (Square)',
            ),
            'default_value' => '16-9',
          ),
          7 => 
          array (
            'key' => 'acf_video_autoplay',
            'label' => 'Autoplay',
            'name' => 'acf_video_autoplay',
            'type' => 'true_false',
            'instructions' => 'Enable autoplay for the video.',
            'required' => 0,
            'default_value' => 0,
          ),
          8 => 
          array (
            'key' => 'acf_video_loop',
            'label' => 'Loop',
            'name' => 'acf_video_loop',
            'type' => 'true_false',
            'instructions' => 'Enable looping for the video.',
            'required' => 0,
            'default_value' => 0,
          ),
          9 => 
          array (
            'key' => 'acf_video_muted',
            'label' => 'Muted',
            'name' => 'acf_video_muted',
            'type' => 'true_false',
            'instructions' => 'Mute the video by default.',
            'required' => 0,
            'default_value' => 0,
          ),
          10 => 
          array (
            'key' => 'acf_video_controls',
            'label' => 'Show Controls',
            'name' => 'acf_video_controls',
            'type' => 'true_false',
            'instructions' => 'Show video player controls.',
            'required' => 0,
            'default_value' => 1,
          ),
          11 => 
          array (
            'key' => 'acf_video_class',
            'label' => 'Custom CSS Class',
            'name' => 'acf_video_class',
            'type' => 'text',
            'instructions' => 'Add custom CSS class(es) for styling.',
            'required' => 0,
          ),
          12 => 
          array (
            'key' => 'acf_video_inline',
            'label' => 'Inline Styles',
            'name' => 'acf_video_inline',
            'type' => 'text',
            'instructions' => 'Add inline CSS styles if needed.',
            'required' => 0,
          ),
        ),
        'location' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'param' => 'block',
              'operator' => '==',
              'value' => 'acf/video',
            ),
          ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'acf' => 
        array (
          'blockVersion' => 3,
        ),
      ),
    ),
  ),
);
