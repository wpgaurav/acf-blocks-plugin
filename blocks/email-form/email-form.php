<?php
/**
 * Email Form Block Template.
 *
 * This template renders the Email Form block.
 * Use functions.php to register the block and include this template as the render callback.
 *
 * @package YourThemeName
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Retrieve ACF field values.
$form_type            = get_field( 'form_type' );
$form_action_url      = get_field( 'form_action_url' );
$webhook_url          = get_field( 'webhook_url' );
$webhook_auth_headers = get_field( 'webhook_auth_headers' );

$display_name         = get_field( 'display_name_field' );
$name_required        = get_field( 'name_field_required' );
$name_field_attrs     = get_field( 'name_field_attributes' );
$email_field_attrs    = get_field( 'email_field_attributes' );

$hidden_fields        = get_field( 'hidden_fields' );

$button_text          = get_field( 'button_text' );
$button_attrs         = get_field( 'button_attributes' );

$success_message      = get_field( 'success_message' );
$form_attrs           = get_field( 'form_attributes' );

// Set default classes.
$default_form_class   = 'md-email-form md-email-full';
$default_name_class   = 'md-input md-input-name';
$default_email_class  = 'md-input md-input-email';
$default_button_class = 'md-submit mb-half';

// Prepare form attributes.
$form_id    = isset( $form_attrs['id'] ) ? $form_attrs['id'] : '';
$form_class = isset( $form_attrs['class'] ) ? $default_form_class . ' ' . $form_attrs['class'] : $default_form_class;
$form_css   = isset( $form_attrs['inline_css'] ) ? $form_attrs['inline_css'] : '';
?>
<div class="md-email md-clear">
	<div class="md-email-content md-clear">
		<form 
			<?php echo $form_id ? 'id="' . esc_attr( $form_id ) . '"' : ''; ?>
			class="<?php echo esc_attr( $form_class ); ?>"
			<?php echo $form_css ? 'style="' . esc_attr( $form_css ) . '"' : ''; ?>
			<?php if ( 'form_action' === $form_type && $form_action_url ) : ?>
				action="<?php echo esc_url( $form_action_url ); ?>"
			<?php endif; ?>
			method="post"
			onsubmit="return handleEmailFormSubmit(event, '<?php echo esc_js( $form_type ); ?>', '<?php echo esc_js( $webhook_url ); ?>', '<?php echo esc_js( $webhook_auth_headers ); ?>');"
		>
			<?php if ( $display_name ) : ?>
				<div class="form-group">
					<label for="email-form-name"><?php esc_html_e( 'Name', 'gauravtiwari' ); ?></label>
					<input 
						type="text" 
						name="email_form_name" 
						id="email-form-name"
						class="<?php echo $default_name_class . ( ! empty( $name_field_attrs['class'] ) ? ' ' . esc_attr( $name_field_attrs['class'] ) : '' ); ?>"
						<?php echo ( $name_required ? 'required' : '' ); ?>
						placeholder="<?php esc_attr_e( 'Your Name', 'gauravtiwari' ); ?>"
						<?php 
						if ( $name_field_attrs ) {
							echo ! empty( $name_field_attrs['id'] ) ? ' id="' . esc_attr( $name_field_attrs['id'] ) . '"' : '';
							echo ! empty( $name_field_attrs['inline_css'] ) ? ' style="' . esc_attr( $name_field_attrs['inline_css'] ) . '"' : '';
						}
						?>
					/>
				</div>
			<?php endif; ?>
			
			<div class="form-group">
				<label for="email-form-email"><?php esc_html_e( 'Email', 'gauravtiwari' ); ?></label>
				<input 
					type="email" 
					name="email_form_email" 
					id="email-form-email"
					class="<?php echo $default_email_class . ( ! empty( $email_field_attrs['class'] ) ? ' ' . esc_attr( $email_field_attrs['class'] ) : '' ); ?>"
					required
					placeholder="<?php esc_attr_e( 'Your Email', 'gauravtiwari' ); ?>"
					<?php 
					if ( $email_field_attrs ) {
						echo ! empty( $email_field_attrs['id'] ) ? ' id="' . esc_attr( $email_field_attrs['id'] ) . '"' : '';
						echo ! empty( $email_field_attrs['inline_css'] ) ? ' style="' . esc_attr( $email_field_attrs['inline_css'] ) . '"' : '';
					}
					?>
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
			
			<div class="form-group">
				<button 
					type="submit"
					class="<?php echo $default_button_class . ( ! empty( $button_attrs['class'] ) ? ' ' . esc_attr( $button_attrs['class'] ) : '' ); ?>"
					<?php 
					if ( $button_attrs ) {
						echo ! empty( $button_attrs['id'] ) ? ' id="' . esc_attr( $button_attrs['id'] ) . '"' : '';
						echo ! empty( $button_attrs['inline_css'] ) ? ' style="' . esc_attr( $button_attrs['inline_css'] ) . '"' : '';
					}
					?>
				>
					<?php echo esc_html( $button_text ); ?>
				</button>
			</div>
			
			<div class="email-form-success" style="display:none; color: green; margin-top: 10px;">
				<?php echo wp_kses_post( $success_message ); ?>
			</div>
		</form>
	</div>
</div>

<script>
function handleEmailFormSubmit(event, formType, webhookUrl, webhookAuthHeaders) {
	event.preventDefault();
	var form = event.target;
	
	// Serialize form data.
	var formData = new FormData(form);
	var data = {};
	formData.forEach(function(value, key) {
		data[key] = value;
	});
	
	if (formType === 'webhook' && webhookUrl) {
		// Instead of calling the external API directly, use your local proxy.
		fetch('/wp-json/email-form-proxy/v1/submit', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				webhookUrl: webhookUrl,
				webhookAuthHeaders: webhookAuthHeaders,
				data: data
			})
		})
		.then(function(response) {
			if (response.ok) {
				form.querySelector('.email-form-success').style.display = 'block';
				form.reset();
			} else {
				console.error('Proxy response error:', response.statusText);
			}
		})
		.catch(function(error) {
			console.error('Error:', error);
		});
	} else {
		// For form_action, let the form submit normally.
		form.submit();
	}
	return false;
}
</script>