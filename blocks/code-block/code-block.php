<?php
/**
 * Code Block Template
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during backend preview render.
 * @param int $post_id The post ID the block is rendering content against.
 * @param array $context The context provided to the block by the post or its parent block.
 */

defined( 'ABSPATH' ) || exit;

// Get block values
$code_content    = get_field( 'code_content' ) ?: '';
$code_language   = get_field( 'code_language' ) ?: 'plaintext';
$code_filename   = get_field( 'code_filename' ) ?: '';
$show_copy_button = get_field( 'show_copy_button' );
$highlight_lines = get_field( 'highlight_lines' ) ?: '';
$max_height      = get_field( 'max_height' ) ?: '';
$code_theme      = get_field( 'code_theme' ) ?: 'dark';
$font_size       = get_field( 'font_size' ) ?: 'normal';
$custom_class    = get_field( 'custom_class' ) ?: '';

// Build classes
$wrapper_classes = array( 'acf-code-block' );
$wrapper_classes[] = 'acf-code-block--' . $code_theme;
$wrapper_classes[] = 'acf-code-block--font-' . $font_size;

if ( ! empty( $custom_class ) ) {
    $wrapper_classes[] = esc_attr( $custom_class );
}

// Build inline styles
$inline_styles = '';
if ( ! empty( $max_height ) ) {
    $inline_styles = 'max-height: ' . intval( $max_height ) . 'px;';
}

// Parse highlight lines
$highlight_array = array();
if ( ! empty( $highlight_lines ) ) {
    $parts = explode( ',', $highlight_lines );
    foreach ( $parts as $part ) {
        $part = trim( $part );
        if ( strpos( $part, '-' ) !== false ) {
            list( $start, $end ) = explode( '-', $part );
            for ( $i = intval( $start ); $i <= intval( $end ); $i++ ) {
                $highlight_array[] = $i;
            }
        } else {
            $highlight_array[] = intval( $part );
        }
    }
}

// Generate unique ID for copy functionality
$block_id = 'code-block-' . uniqid();

// Language display names
$language_names = array(
    'plaintext'  => 'Plain Text',
    'html'       => 'HTML',
    'css'        => 'CSS',
    'javascript' => 'JavaScript',
    'typescript' => 'TypeScript',
    'php'        => 'PHP',
    'python'     => 'Python',
    'ruby'       => 'Ruby',
    'java'       => 'Java',
    'csharp'     => 'C#',
    'cpp'        => 'C++',
    'c'          => 'C',
    'go'         => 'Go',
    'rust'       => 'Rust',
    'swift'      => 'Swift',
    'kotlin'     => 'Kotlin',
    'sql'        => 'SQL',
    'bash'       => 'Bash',
    'powershell' => 'PowerShell',
    'json'       => 'JSON',
    'xml'        => 'XML',
    'yaml'       => 'YAML',
    'markdown'   => 'Markdown',
    'jsx'        => 'JSX',
    'tsx'        => 'TSX',
    'scss'       => 'SCSS',
    'sass'       => 'Sass',
    'less'       => 'Less',
);

$language_display = isset( $language_names[ $code_language ] ) ? $language_names[ $code_language ] : $code_language;
?>

<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
    <?php if ( ! empty( $code_filename ) || $code_language !== 'plaintext' || $show_copy_button ) : ?>
        <div class="acf-code-block__header">
            <div class="acf-code-block__header-left">
                <span class="acf-code-block__dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <?php if ( ! empty( $code_filename ) ) : ?>
                    <span class="acf-code-block__filename"><?php echo esc_html( $code_filename ); ?></span>
                <?php elseif ( $code_language !== 'plaintext' ) : ?>
                    <span class="acf-code-block__language"><?php echo esc_html( $language_display ); ?></span>
                <?php endif; ?>
            </div>
            <?php if ( $show_copy_button ) : ?>
                <button type="button" class="acf-code-block__copy" data-target="<?php echo esc_attr( $block_id ); ?>" aria-label="<?php esc_attr_e( 'Copy code', 'acf-blocks' ); ?>">
                    <svg class="acf-code-block__copy-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <svg class="acf-code-block__check-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span class="acf-code-block__copy-text"><?php esc_html_e( 'Copy', 'acf-blocks' ); ?></span>
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="acf-code-block__content"<?php echo ! empty( $inline_styles ) ? ' style="' . esc_attr( $inline_styles ) . '"' : ''; ?>>
        <?php if ( ! empty( $code_content ) ) : ?>
            <?php
            $lines = explode( "\n", $code_content );
            $line_num = 1;
            ?>
            <pre class="acf-code-block__pre"><code class="acf-code-block__code language-<?php echo esc_attr( $code_language ); ?>" data-code="<?php echo esc_attr( $code_content ); ?>"><?php
                foreach ( $lines as $index => $line ) :
                    $is_highlighted = in_array( $line_num, $highlight_array );
                    $line_class = $is_highlighted ? 'acf-code-block__line acf-code-block__line--highlighted' : 'acf-code-block__line';
                    ?><span class="<?php echo esc_attr( $line_class ); ?>"><span class="acf-code-block__line-content"><?php echo esc_html( $line ); ?></span>
</span><?php
                    $line_num++;
                endforeach;
            ?></code></pre>
        <?php else : ?>
            <pre class="acf-code-block__pre"><code class="acf-code-block__code"><?php esc_html_e( 'No code provided.', 'acf-blocks' ); ?></code></pre>
        <?php endif; ?>
    </div>
</div>

<?php if ( $show_copy_button ) : ?>
<script>
(function() {
    var block = document.getElementById('<?php echo esc_js( $block_id ); ?>');
    if (!block) return;

    var copyBtn = block.querySelector('.acf-code-block__copy');
    if (!copyBtn) return;

    copyBtn.addEventListener('click', function() {
        var codeEl = block.querySelector('.acf-code-block__code');
        var code = codeEl ? codeEl.getAttribute('data-code') : '';

        if (!code) {
            var lines = block.querySelectorAll('.acf-code-block__line-content');
            code = Array.prototype.map.call(lines, function(line) {
                return line.textContent;
            }).join('\n');
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(function() {
                showCopied();
            }).catch(function() {
                fallbackCopy(code);
            });
        } else {
            fallbackCopy(code);
        }
    });

    function fallbackCopy(text) {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showCopied();
        } catch (err) {
            console.error('Copy failed:', err);
        }
        document.body.removeChild(textarea);
    }

    function showCopied() {
        var copyIcon = copyBtn.querySelector('.acf-code-block__copy-icon');
        var checkIcon = copyBtn.querySelector('.acf-code-block__check-icon');
        var copyText = copyBtn.querySelector('.acf-code-block__copy-text');

        if (copyIcon) copyIcon.style.display = 'none';
        if (checkIcon) checkIcon.style.display = 'inline';
        if (copyText) copyText.textContent = '<?php echo esc_js( __( 'Copied!', 'acf-blocks' ) ); ?>';

        setTimeout(function() {
            if (copyIcon) copyIcon.style.display = 'inline';
            if (checkIcon) checkIcon.style.display = 'none';
            if (copyText) copyText.textContent = '<?php echo esc_js( __( 'Copy', 'acf-blocks' ) ); ?>';
        }, 2000);
    }
})();
</script>
<?php endif; ?>
