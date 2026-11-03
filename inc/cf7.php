<?php
/**
 * Contact Form 7 integration and a styled fallback form.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wpcf7_autop_or_not', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );
add_filter( 'wpcf7_load_js', '__return_false' );

add_action( 'wp_enqueue_scripts', 'ast_enqueue_cf7_js' );

/**
 * Load Contact Form 7's script on the front page only.
 */
function ast_enqueue_cf7_js() {
	if ( is_front_page() && function_exists( 'wpcf7_enqueue_scripts' ) ) {
		wpcf7_enqueue_scripts();
	}
}

add_filter( 'wpcf7_form_elements', 'ast_cf7_privacy_url' );

/**
 * Replace the privacy token inside the form markup.
 *
 * @param string $content Form HTML.
 * @return string
 */
function ast_cf7_privacy_url( $content ) {
	$url = get_privacy_policy_url();
	if ( ! $url ) {
		$url = home_url( '/' );
	}
	return str_replace( '%%privacy_url%%', esc_url( $url ), $content );
}

add_action( 'init', 'ast_ensure_cf7_form', 20 );

/**
 * Create the investment enquiry form once, when Contact Form 7 is active.
 */
function ast_ensure_cf7_form() {
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
		return;
	}

	$stored = get_option( 'ast_cf7_form_id' );
	if ( $stored && 'publish' === get_post_status( (int) $stored ) ) {
		ast_cf7_apply_flamingo_settings( (int) $stored );
		return;
	}

	$existing = get_posts(
		array(
			'post_type'              => 'wpcf7_contact_form',
			'title'                  => 'Investment enquiry',
			'post_status'            => 'publish',
			'numberposts'            => 1,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( $existing ) {
		update_option( 'ast_cf7_form_id', $existing[0]->ID, false );
		update_option( 'ast_cf7_shortcode', ast_cf7_shortcode_from_post( $existing[0]->ID ), false );
		ast_cf7_apply_flamingo_settings( (int) $existing[0]->ID );
		return;
	}

	$form = WPCF7_ContactForm::get_template(
		array(
			'title' => 'Investment enquiry',
		)
	);

	$properties = $form->get_properties();
	$properties['form'] = ast_cf7_markup();

	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	$properties['mail']['subject']            = 'Investment enquiry — [full-name]';
	$properties['mail']['sender']             = sprintf( 'Alpha Solar Technologies <wordpress@%s>', $host ? $host : 'localhost' );
	$properties['mail']['recipient']          = get_option( 'admin_email' );
	$properties['mail']['body']               = "Name: [full-name]\nEmail: [email-address]\nCompany: [company]\n\n[enquiry]";
	$properties['mail']['additional_headers'] = 'Reply-To: [email-address]';
	$properties['mail']['use_html']           = 0;

	$properties['messages']['mail_sent_ok']     = 'Thank you. Your enquiry has been sent and our team will be in touch.';
	$properties['messages']['mail_sent_ng']     = 'Sorry, your enquiry could not be sent. Please try again or email us directly.';
	$properties['messages']['validation_error'] = 'Please check the highlighted fields and try again.';
	$properties['messages']['invalid_required'] = 'Please fill in this field.';
	$properties['messages']['invalid_email']    = 'Please enter a valid email address.';
	$properties['messages']['spam']             = 'Sorry, your enquiry could not be sent. Please try again or email us directly.';
	$properties['additional_settings']          = ast_cf7_flamingo_additional_settings();

	$form->set_properties( $properties );
	$post_id = $form->save();

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_option( 'ast_cf7_form_id', (int) $post_id, false );
		update_option( 'ast_cf7_shortcode', ast_cf7_shortcode_from_post( (int) $post_id ), false );
	}
}

/**
 * @param int $post_id Form post ID.
 * @return string
 */
function ast_cf7_shortcode_from_post( $post_id ) {
	$hash = (string) get_post_meta( $post_id, '_hash', true );
	if ( '' === $hash && class_exists( 'WPCF7_ContactForm' ) ) {
		$form = WPCF7_ContactForm::get_instance( $post_id );
		if ( $form ) {
			return $form->shortcode();
		}
	}
	return sprintf( '[contact-form-7 id="%s" title="Investment enquiry"]', $hash );
}

/**
 * Additional settings Flamingo reads for the inbound Subject and From columns.
 *
 * From is built as "name <email>", so it needs both flamingo_name and flamingo_email.
 *
 * @return string
 */
function ast_cf7_flamingo_additional_settings() {
	return implode(
		"\n",
		array(
			'flamingo_subject: "Investment enquiry — [full-name]"',
			'flamingo_name: "[full-name]"',
			'flamingo_email: "[email-address]"',
		)
	);
}

/**
 * Add the Flamingo mappings when the form does not already define them.
 *
 * @param int $post_id Contact form ID.
 */
function ast_cf7_apply_flamingo_settings( $post_id ) {
	$form = WPCF7_ContactForm::get_instance( $post_id );
	if ( ! $form ) {
		return;
	}

	$properties = $form->get_properties();
	$current    = isset( $properties['additional_settings'] ) ? (string) $properties['additional_settings'] : '';
	$merged     = ast_cf7_merge_flamingo_settings( $current );

	if ( $merged === $current ) {
		return;
	}

	$properties['additional_settings'] = $merged;
	$form->set_properties( $properties );
	$form->save();
}

/**
 * Append any missing flamingo_* lines without replacing settings already saved.
 *
 * @param string $existing Additional settings text.
 * @return string
 */
function ast_cf7_merge_flamingo_settings( $existing ) {
	$lines   = preg_split( '/\r\n|\r|\n/', ast_cf7_flamingo_additional_settings() );
	$missing = array();

	foreach ( $lines as $line ) {
		if ( ! preg_match( '/^([a-zA-Z0-9_]+)[\t ]*:/', $line, $matches ) ) {
			continue;
		}
		if ( ! preg_match( '/^' . preg_quote( $matches[1], '/' ) . '[\t ]*:/m', $existing ) ) {
			$missing[] = $line;
		}
	}

	if ( ! $missing ) {
		return $existing;
	}

	$existing = rtrim( $existing );
	if ( '' === $existing ) {
		return implode( "\n", $missing );
	}

	return $existing . "\n" . implode( "\n", $missing );
}

/**
 * Form markup matching the R05 field layout.
 *
 * @return string
 */
function ast_cf7_markup() {
	return <<<'FORM'
<div class="ast-form__row ast-form__row--2">
<p class="ast-field"><label for="ast-name" class="screen-reader-text">Full name (required)</label>
[text* full-name id:ast-name autocomplete:name maxlength:100 placeholder "Full name"]</p>
<p class="ast-field"><label for="ast-email" class="screen-reader-text">Email address (required)</label>
[email* email-address id:ast-email autocomplete:email placeholder "Email address"]</p>
</div>
<p class="ast-field"><label for="ast-company" class="screen-reader-text">Company or organisation (optional)</label>
[text company id:ast-company autocomplete:organization maxlength:150 placeholder "Company or organisation (optional)"]</p>
<p class="ast-field ast-field--textarea"><label for="ast-enquiry" class="screen-reader-text">Your enquiry (required)</label>
[textarea* enquiry id:ast-enquiry maxlength:2000 placeholder "Your enquiry"]</p>
<div class="ast-form__actions">
<span class="ast-submit">[submit class:btn class:btn--primary "Enquire about investment"]<span class="ast-sending screen-reader-text">Sending…</span></span>
<a class="btn btn--outline" href="%%privacy_url%%">Privacy policy</a>
</div>
FORM;
}

/**
 * Shortcode to render. An ACF override wins.
 *
 * @return string
 */
function ast_contact_shortcode() {
	if ( function_exists( 'get_field' ) ) {
		$custom = get_field( 'contact_form_shortcode' );
		if ( is_string( $custom ) && '' !== trim( $custom ) ) {
			return trim( $custom );
		}
	}
	$stored = get_option( 'ast_cf7_shortcode' );
	return is_string( $stored ) ? $stored : '';
}

add_action( 'admin_post_nopriv_ast_enquiry', 'ast_handle_enquiry' );
add_action( 'admin_post_ast_enquiry', 'ast_handle_enquiry' );

/**
 * Fallback mail handler used when Contact Form 7 is not available.
 */
function ast_handle_enquiry() {
	$redirect = home_url( '/' );
	if ( ! isset( $_POST['ast_enquiry_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ast_enquiry_nonce'] ) ), 'ast_enquiry' ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'failed', $redirect ) . '#contact' );
		exit;
	}

	if ( ! empty( $_POST['ast_company_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'sent', $redirect ) . '#contact' );
		exit;
	}

	$name    = isset( $_POST['full-name'] ) ? sanitize_text_field( wp_unslash( $_POST['full-name'] ) ) : '';
	$email   = isset( $_POST['email-address'] ) ? sanitize_email( wp_unslash( $_POST['email-address'] ) ) : '';
	$company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
	$enquiry = isset( $_POST['enquiry'] ) ? sanitize_textarea_field( wp_unslash( $_POST['enquiry'] ) ) : '';

	$valid = ( '' !== $name && strlen( $name ) <= 100 && is_email( $email ) && '' !== $enquiry && strlen( $enquiry ) <= 2000 && strlen( $company ) <= 150 );

	if ( ! $valid ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'invalid', $redirect ) . '#contact' );
		exit;
	}

	$body = "Name: {$name}\nEmail: {$email}\nCompany: {$company}\n\n{$enquiry}";
	$sent = wp_mail(
		get_option( 'admin_email' ),
		'Investment enquiry — ' . $name,
		$body,
		array( 'Reply-To: ' . $email )
	);

	wp_safe_redirect( add_query_arg( 'enquiry', $sent ? 'sent' : 'failed', $redirect ) . '#contact' );
	exit;
}
