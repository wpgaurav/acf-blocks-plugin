<?php
/**
 * Email Form Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Retrieve ACF field values.
$form_type            = acf_blocks_get_field( 'form_type', $block );
$form_action_url      = acf_blocks_get_field( 'form_action_url', $block );
$webhook_url          = acf_blocks_get_field( 'webhook_url', $block );
$webhook_auth_headers = acf_blocks_get_field( 'webhook_auth_headers', $block );

$display_name         = acf_blocks_get_field( 'display_name_field', $block );
$name_required        = acf_blocks_get_field( 'name_field_required', $block );
$name_field_attrs     = acf_blocks_get_field( 'name_field_attributes', $block );
$email_field_attrs    = acf_blocks_get_field( 'email_field_attributes', $block );

$hidden_fields        = acf_blocks_get_field( 'hidden_fields', $block );

$button_text          = acf_blocks_get_field( 'button_text', $block ) ?: __( 'Subscribe', 'acf-blocks' );
$button_attrs         = acf_blocks_get_field( 'button_attributes', $block );

$success_message      = acf_blocks_get_field( 'success_message', $block ) ?: __( 'Thanks for subscribing!', 'acf-blocks' );
$form_attrs           = acf_blocks_get_field( 'form_attributes', $block );

// Generate unique ID for this block instance.
$block_uid   = 'ef-' . substr( md5( $block['id'] ?? uniqid() ), 0, 8 );
$name_id     = $block_uid . '-name';
$email_id    = $block_uid . '-email';

// Set default classes.
$default_form_class   = 'acf-email-form acf-email-form-full';
$default_name_class   = 'acf-input acf-input-name';
$default_email_class  = 'acf-input acf-input-email';
$default_button_class = 'acf-submit';

// Prepare form attributes.
$form_id    = ! empty( $form_attrs['id'] ) ? $form_attrs['id'] : $block_uid;
$form_class = ! empty( $form_attrs['class'] ) ? $default_form_class . ' ' . $form_attrs['class'] : $default_form_class;
$form_css   = ! empty( $form_attrs['inline_css'] ) ? $form_attrs['inline_css'] : '';

// Output webhook config via a safe inline script (not in HTML attributes).
if ( 'webhook' === $form_type && $webhook_url && ! $is_preview ) :
	$config_data = array(
		'webhookUrl'  => $webhook_url,
		'authHeaders' => $webhook_auth_headers ?: '',
		'proxyUrl'    => rest_url( 'email-form-proxy/v1/submit' ),
	);
	?>
	<script>
	window.acfEmailFormConfigs = window.acfEmailFormConfigs || {};
	window.acfEmailFormConfigs[<?php echo wp_json_encode( $form_id ); ?>] = <?php echo wp_json_encode( $config_data ); ?>;
	</script>
<?php endif; ?>

<div class="acf-email-form-wrapper">
	<form
		id="<?php echo esc_attr( $form_id ); ?>"
		class="<?php echo esc_attr( $form_class ); ?>"
		<?php echo $form_css ? 'style="' . esc_attr( $form_css ) . '"' : ''; ?>
		<?php if ( 'form_action' === $form_type && $form_action_url ) : ?>
			action="<?php echo esc_url( $form_action_url ); ?>"
		<?php endif; ?>
		method="post"
		data-form-type="<?php echo esc_attr( $form_type ); ?>"
		data-config-id="<?php echo esc_attr( $form_id ); ?>"
		data-error-message="<?php echo esc_attr__( 'Something went wrong. Please try again.', 'acf-blocks' ); ?>"
	>
		<?php if ( $display_name ) : ?>
			<div class="acf-form-group">
				<label for="<?php echo esc_attr( $name_id ); ?>"><?php esc_html_e( 'Name', 'acf-blocks' ); ?></label>
				<input
					type="text"
					name="email_form_name"
					id="<?php echo esc_attr( ! empty( $name_field_attrs['id'] ) ? $name_field_attrs['id'] : $name_id ); ?>"
					class="<?php echo esc_attr( $default_name_class . ( ! empty( $name_field_attrs['class'] ) ? ' ' . $name_field_attrs['class'] : '' ) ); ?>"
					<?php echo $name_required ? 'required' : ''; ?>
					placeholder="<?php esc_attr_e( 'Your Name', 'acf-blocks' ); ?>"
					<?php echo ! empty( $name_field_attrs['inline_css'] ) ? 'style="' . esc_attr( $name_field_attrs['inline_css'] ) . '"' : ''; ?>
				/>
			</div>
		<?php endif; ?>

		<div class="acf-form-group">
			<label for="<?php echo esc_attr( $email_id ); ?>"><?php esc_html_e( 'Email', 'acf-blocks' ); ?></label>
			<input
				type="email"
				name="email_form_email"
				id="<?php echo esc_attr( ! empty( $email_field_attrs['id'] ) ? $email_field_attrs['id'] : $email_id ); ?>"
				class="<?php echo esc_attr( $default_email_class . ( ! empty( $email_field_attrs['class'] ) ? ' ' . $email_field_attrs['class'] : '' ) ); ?>"
				required
				placeholder="<?php esc_attr_e( 'Your Email', 'acf-blocks' ); ?>"
				<?php echo ! empty( $email_field_attrs['inline_css'] ) ? 'style="' . esc_attr( $email_field_attrs['inline_css'] ) . '"' : ''; ?>
			/>
		</div>

		<?php if ( $hidden_fields ) : ?>
			<?php foreach ( $hidden_fields as $hidden ) : ?>
				<input
					type="hidden"
					name="<?php echo esc_attr( $hidden['field_name'] ); ?>"
					value="<?php echo esc_attr( $hidden['field_value'] ); ?>"
					<?php
					if ( isset( $hidden['attributes'] ) && is_array( $hidden['attributes'] ) ) {
						echo ! empty( $hidden['attributes']['id'] ) ? ' id="' . esc_attr( $hidden['attributes']['id'] ) . '"' : '';
						echo ! empty( $hidden['attributes']['class'] ) ? ' class="' . esc_attr( $hidden['attributes']['class'] ) . '"' : '';
						echo ! empty( $hidden['attributes']['inline_css'] ) ? ' style="' . esc_attr( $hidden['attributes']['inline_css'] ) . '"' : '';
					}
					?>
				/>
			<?php endforeach; ?>
		<?php endif; ?>

		<div class="acf-form-group">
			<button
				type="submit"
				class="<?php echo esc_attr( $default_button_class . ( ! empty( $button_attrs['class'] ) ? ' ' . $button_attrs['class'] : '' ) ); ?>"
				<?php echo ! empty( $button_attrs['id'] ) ? 'id="' . esc_attr( $button_attrs['id'] ) . '"' : ''; ?>
				<?php echo ! empty( $button_attrs['inline_css'] ) ? 'style="' . esc_attr( $button_attrs['inline_css'] ) . '"' : ''; ?>
			>
				<?php echo esc_html( $button_text ); ?>
			</button>
		</div>

		<div class="acf-email-form-success" hidden role="status">
			<?php echo wp_kses_post( $success_message ); ?>
		</div>
		<div class="acf-email-form-error" hidden role="alert"></div>
	</form>
</div>
