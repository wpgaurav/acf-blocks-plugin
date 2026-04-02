<?php
/**
 * Chat Block Template.
 *
 * @param array      $block      The block settings and attributes.
 * @param string     $content    The block inner HTML (empty).
 * @param bool       $is_preview True during AJAX preview.
 * @param int|string $post_id    The post ID.
 */

$align     = $block['align'] ?? '';
$className = $block['className'] ?? '';
$anchor    = $block['anchor'] ?? '';

$block_id = $anchor ?: 'chat-' . ( $block['id'] ?? uniqid() );

// Header fields
$header_title      = acf_blocks_get_field( 'chat_header_title', $block );
$show_indicator    = acf_blocks_get_field( 'chat_header_show_indicator', $block );
$indicator_color   = acf_blocks_get_field( 'chat_header_indicator_color', $block ) ?: '#22c55e';

// Messages repeater
$messages = acf_blocks_get_repeater( 'chat_messages', [
    [ 'chat_speaker_name', 'text' ],
    [ 'chat_speaker_color', 'text' ],
    [ 'chat_message_content', 'text' ],
], $block );

// Build classes
$classes = [ 'acf-chat' ];
if ( ! empty( $align ) ) {
    $classes[] = 'align' . $align;
}
if ( ! empty( $className ) ) {
    $classes[] = $className;
}
if ( empty( $header_title ) ) {
    $classes[] = 'no-header';
}

// Don't render if no messages
if ( empty( $messages ) ) {
    if ( $is_preview ) {
        echo '<p style="padding:20px;text-align:center;color:#999;">Add messages to preview the chat block.</p>';
    }
    return;
}
?>

<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" role="log" aria-label="<?php echo esc_attr( $header_title ?: __( 'Chat conversation', 'acf-blocks' ) ); ?>">
    <?php if ( ! empty( $header_title ) ) : ?>
        <div class="acf-chat-header">
            <?php if ( $show_indicator ) : ?>
                <span class="acf-chat-indicator" style="background-color: <?php echo esc_attr( $indicator_color ); ?>;" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="acf-chat-header-title"><?php echo esc_html( $header_title ); ?></span>
        </div>
    <?php endif; ?>

    <div class="acf-chat-body" role="list">
        <?php foreach ( $messages as $msg ) :
            $name    = $msg['chat_speaker_name'] ?? '';
            $color   = $msg['chat_speaker_color'] ?? '#6366f1';
            $content = $msg['chat_message_content'] ?? '';

            if ( empty( $name ) || empty( $content ) ) {
                continue;
            }
        ?>
            <div class="acf-chat-message" role="listitem">
                <div class="acf-chat-speaker" style="color: <?php echo esc_attr( $color ); ?>;">
                    <?php echo esc_html( strtoupper( $name ) ); ?>
                </div>
                <div class="acf-chat-content">
                    <?php echo wp_kses_post( $content ); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
