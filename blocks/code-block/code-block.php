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
$code_content    = acf_blocks_get_field( 'code_content', $block ) ?: '';
$code_language   = acf_blocks_get_field( 'code_language', $block ) ?: 'plaintext';
$code_filename   = acf_blocks_get_field( 'code_filename', $block ) ?: '';
$highlight_lines = acf_blocks_get_field( 'highlight_lines', $block ) ?: '';
$code_theme      = acf_blocks_get_field( 'code_theme', $block ) ?: 'dark';
$font_size       = acf_blocks_get_field( 'font_size', $block ) ?: 'normal';
$custom_class    = acf_blocks_get_field( 'custom_class', $block ) ?: '';

// Build classes
$wrapper_classes = array( 'acf-code-block' );
$wrapper_classes[] = 'acf-code-block--' . $code_theme;
$wrapper_classes[] = 'acf-code-block--font-' . $font_size;

if ( ! empty( $custom_class ) ) {
    $wrapper_classes[] = esc_attr( $custom_class );
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

// Generate unique ID for expand functionality
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

<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" id="<?php echo esc_attr( $block_id ); ?>" data-expandable="true">
    <?php if ( ! empty( $code_filename ) || $code_language !== 'plaintext' ) : ?>
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
        </div>
    <?php endif; ?>

    <div class="acf-code-block__content">
        <?php if ( ! empty( $code_content ) ) : ?>
            <?php
            $lines = explode( "\n", $code_content );
            $line_num = 1;
            ?>
            <pre class="acf-code-block__pre"><code class="acf-code-block__code language-<?php echo esc_attr( $code_language ); ?>"><?php
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

    <button type="button" class="acf-code-block__expand" aria-expanded="false" style="display: none;">
        <span class="acf-code-block__expand-text"><?php esc_html_e( 'Show more', 'acf-blocks' ); ?></span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </button>
</div>

<script>
(function() {
    var block = document.getElementById('<?php echo esc_js( $block_id ); ?>');
    if (!block) return;

    var content = block.querySelector('.acf-code-block__content');
    var expandBtn = block.querySelector('.acf-code-block__expand');
    var expandText = block.querySelector('.acf-code-block__expand-text');
    var maxHeight = 900;

    function checkHeight() {
        if (!content || !expandBtn) return;

        // Temporarily remove max-height to measure full height
        content.style.maxHeight = 'none';
        var fullHeight = content.scrollHeight;

        if (fullHeight > maxHeight) {
            content.style.maxHeight = maxHeight + 'px';
            block.classList.add('acf-code-block--collapsed');
            expandBtn.style.display = 'flex';
        } else {
            content.style.maxHeight = 'none';
            block.classList.remove('acf-code-block--collapsed');
            expandBtn.style.display = 'none';
        }
    }

    if (expandBtn) {
        expandBtn.addEventListener('click', function() {
            var isExpanded = block.classList.contains('acf-code-block--expanded');

            if (isExpanded) {
                block.classList.remove('acf-code-block--expanded');
                block.classList.add('acf-code-block--collapsed');
                content.style.maxHeight = maxHeight + 'px';
                expandBtn.setAttribute('aria-expanded', 'false');
                expandText.textContent = '<?php echo esc_js( __( 'Show more', 'acf-blocks' ) ); ?>';
                expandBtn.querySelector('svg').style.transform = 'rotate(0deg)';
            } else {
                block.classList.remove('acf-code-block--collapsed');
                block.classList.add('acf-code-block--expanded');
                content.style.maxHeight = 'none';
                expandBtn.setAttribute('aria-expanded', 'true');
                expandText.textContent = '<?php echo esc_js( __( 'Show less', 'acf-blocks' ) ); ?>';
                expandBtn.querySelector('svg').style.transform = 'rotate(180deg)';
            }
        });
    }

    // Check height on load
    checkHeight();
})();
</script>
