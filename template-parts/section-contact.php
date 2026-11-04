<?php
/**
 * Investment enquiry.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shortcode = ast_contact_shortcode();
$privacy   = get_privacy_policy_url();
if ( ! $privacy ) {
	$privacy = home_url( '/' );
}
$label         = ast_field( 'contact_privacy_label' );
$privacy_attrs = ast_content_attrs( 'contact_privacy_label' );
$status = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : '';
?>
<section class="contact ast-contact" id="contact" aria-labelledby="<?php echo esc_attr( ast_content_id( 'contact_heading', 'contact_heading' ) ); ?>">
	<div class="contact-shade" aria-hidden="true">
		<div class="hex hex--contact-a"></div>
		<div class="hex hex--contact-b"></div>
	</div>

	<div class="contact-intro">
		<h2<?php echo ast_attr_html( 'contact_heading', 'h2', 'contact_heading' ); ?> data-reveal style="--i:0"><?php echo ast_highlight( 'contact_heading' ); ?></h2>
		<div<?php echo ast_attr_html( 'contact_intro', 'contact-lead' ); ?> data-reveal style="--i:1"><?php echo ast_rich_text( 'contact_intro' ); ?></div>
	</div>

	<div class="contact-form" data-reveal style="--i:2">
		<?php if ( $shortcode && shortcode_exists( 'contact-form-7' ) ) : ?>
			<?php echo do_shortcode( $shortcode ); ?>
		<?php else : ?>
			<?php if ( current_user_can( 'edit_pages' ) ) : ?>
				<p class="ast-admin-note"><?php esc_html_e( 'Contact Form 7 is not available yet, so this is the theme fallback form. It sends to the site admin email.', 'alpha-solar' ); ?></p>
			<?php endif; ?>
			<form class="ast-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
				<input type="hidden" name="action" value="ast_enquiry">
				<?php wp_nonce_field( 'ast_enquiry', 'ast_enquiry_nonce' ); ?>
				<p class="ast-hp" aria-hidden="true">
					<label>Website <input type="text" name="ast_company_website" tabindex="-1" autocomplete="off"></label>
				</p>
				<div class="ast-form-row ast-form-row--2">
					<p class="ast-field">
						<label for="ast_name" class="screen-reader-text"><?php esc_html_e( 'Full name (required)', 'alpha-solar' ); ?></label>
						<input id="ast_name" name="full-name" type="text" autocomplete="name" maxlength="100" placeholder="<?php esc_attr_e( 'Full name', 'alpha-solar' ); ?>" required>
					</p>
					<p class="ast-field">
						<label for="ast_email" class="screen-reader-text"><?php esc_html_e( 'Email address (required)', 'alpha-solar' ); ?></label>
						<input id="ast_email" name="email-address" type="email" autocomplete="email" placeholder="<?php esc_attr_e( 'Email address', 'alpha-solar' ); ?>" required>
					</p>
				</div>
				<p class="ast-field">
					<label for="ast_company" class="screen-reader-text"><?php esc_html_e( 'Company or organisation (optional)', 'alpha-solar' ); ?></label>
					<input id="ast_company" name="company" type="text" autocomplete="organization" maxlength="150" placeholder="<?php esc_attr_e( 'Company or organisation (optional)', 'alpha-solar' ); ?>">
				</p>
				<p class="ast-field ast-field--textarea">
					<label for="ast_enquiry" class="screen-reader-text"><?php esc_html_e( 'Your enquiry (required)', 'alpha-solar' ); ?></label>
					<textarea id="ast_enquiry" name="enquiry" maxlength="2000" placeholder="<?php esc_attr_e( 'Your enquiry', 'alpha-solar' ); ?>" required></textarea>
				</p>
				<div class="ast-form-actions">
					<button class="btn btn--primary" type="submit">
						<span class="btn-label"><?php esc_html_e( 'Enquire about investment', 'alpha-solar' ); ?></span>
						<span class="btn-icon" aria-hidden="true"><?php echo ast_arrow_svg(); ?></span>
					</button>
					<a class="<?php echo esc_attr( trim( 'btn btn--outline ' . $privacy_attrs['class'] ) ); ?>"<?php echo '' !== $privacy_attrs['id'] ? ' id="' . esc_attr( $privacy_attrs['id'] ) . '"' : ''; ?> href="<?php echo esc_url( $privacy ); ?>"><?php echo esc_html( $label ); ?></a>
				</div>
			</form>
		<?php endif; ?>

		<?php if ( $status ) : ?>
			<?php
			$messages = array(
				'sent'    => __( 'Thank you. Your enquiry has been sent and our team will be in touch.', 'alpha-solar' ),
				'invalid' => __( 'Please check the highlighted fields and try again.', 'alpha-solar' ),
				'failed'  => __( 'Sorry, your enquiry could not be sent. Please try again or email us directly.', 'alpha-solar' ),
			);
			$tone = 'sent' === $status ? 'sent' : 'invalid';
			$text = $messages[ $status ] ?? '';
			?>
			<?php if ( $text ) : ?>
				<p class="ast-form-status ast-form-status--<?php echo esc_attr( $tone ); ?>" tabindex="-1"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
