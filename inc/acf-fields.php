<?php
/**
 * Front-page field group. Saved into ACF so it appears under Custom Fields.
 * ACF Free is assumed: licensing slides are three fixed image fields.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'ast_register_fields' );

/**
 * Save the front-page field group into ACF once, so it is listed and editable
 * under Custom Fields → Field Groups.
 */
function ast_register_fields() {
	if ( ! function_exists( 'acf_import_field_group' ) || ! function_exists( 'acf_get_field_group' ) ) {
		return;
	}

	$existing = acf_get_field_group( 'group_ast_front_page' );
	if ( ! empty( $existing['ID'] ) ) {
		return;
	}

	acf_import_field_group( ast_front_page_field_group() );
}

/**
 * Field group definition imported into the ACF admin.
 *
 * @return array<string, mixed>
 */
function ast_front_page_field_group() {
	$defaults = ast_defaults();

	return array(
			'key'                   => 'group_ast_front_page',
			'title'                 => 'Front page',
			'description'           => 'Text and images for the landing page.',
			'fields'                => array(
				ast_tab( 'field_ast_tab_header', 'Header' ),
				ast_image( 'field_ast_header_logo', 'header_logo', 'Logo' ),
				ast_text( 'field_ast_header_cta_label', 'header_cta_label', 'Contact button label', $defaults['header_cta_label'] ),
				ast_text( 'field_ast_header_cta_url', 'header_cta_url', 'Contact button link', $defaults['header_cta_url'], 'Use #contact to scroll to the form.' ),

				ast_tab( 'field_ast_tab_hero', 'Hero' ),
				ast_text( 'field_ast_hero_eyebrow', 'hero_eyebrow', 'Eyebrow', $defaults['hero_eyebrow'] ),
				ast_text( 'field_ast_hero_heading', 'hero_heading', 'Heading', $defaults['hero_heading'], 'Wrap highlighted words in <em>.</em>' ),
				ast_textarea( 'field_ast_hero_body', 'hero_body', 'Body', $defaults['hero_body'], 'A new line becomes a line break.' ),
				ast_image( 'field_ast_hero_bg', 'hero_bg_image', 'Background photo' ),
				ast_image( 'field_ast_hero_fg', 'hero_fg_image', 'Building foreground' ),
				ast_image( 'field_ast_hero_band', 'hero_band_image', 'Image band' ),

				ast_tab( 'field_ast_tab_announce', 'Announcement' ),
				ast_text( 'field_ast_announce_heading', 'announce_heading', 'Heading', $defaults['announce_heading'], 'Wrap highlighted words in <em>.</em>' ),
				ast_link_field( 'field_ast_announce_button', 'announce_button', 'Button' ),

				ast_tab( 'field_ast_tab_business', 'Business model' ),
				ast_text( 'field_ast_business_heading', 'business_heading', 'Heading', $defaults['business_heading'], 'Wrap the second sentence in <em>.</em>' ),
				ast_textarea( 'field_ast_business_body', 'business_body', 'Body', $defaults['business_body'], 'A blank line starts a new paragraph.' ),
				ast_image( 'field_ast_business_image', 'business_image', 'Image' ),

				ast_tab( 'field_ast_tab_licensing', 'Licensing model' ),
				ast_text( 'field_ast_licensing_heading', 'licensing_heading', 'Heading', $defaults['licensing_heading'], 'Wrap the highlighted phrase in <em>.</em>' ),
				ast_image( 'field_ast_slide_1', 'licensing_slide_1', 'Slide 1' ),
				ast_image( 'field_ast_slide_2', 'licensing_slide_2', 'Slide 2' ),
				ast_image( 'field_ast_slide_3', 'licensing_slide_3', 'Slide 3' ),
				ast_textarea( 'field_ast_licensing_body', 'licensing_body', 'Body', $defaults['licensing_body'] ),
				ast_link_field( 'field_ast_licensing_button', 'licensing_button', 'Button' ),

				ast_tab( 'field_ast_tab_contact', 'Contact' ),
				ast_text( 'field_ast_contact_heading', 'contact_heading', 'Heading', $defaults['contact_heading'], 'Wrap the highlighted phrase in <em>.</em>' ),
				ast_textarea( 'field_ast_contact_intro', 'contact_intro', 'Intro', $defaults['contact_intro'] ),
				ast_text( 'field_ast_contact_shortcode', 'contact_form_shortcode', 'Contact Form 7 shortcode', '', 'Leave empty to use the form created by the theme.' ),
				ast_text( 'field_ast_contact_privacy', 'contact_privacy_label', 'Privacy button label', $defaults['contact_privacy_label'] ),

				ast_tab( 'field_ast_tab_footer', 'Footer' ),
				ast_text( 'field_ast_footer_text', 'footer_text', 'Footer text', $defaults['footer_text'] ),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'hide_on_screen'        => array( 'the_content', 'excerpt', 'discussion', 'comments', 'revisions', 'slug', 'author', 'format', 'featured_image', 'categories', 'tags', 'send-trackbacks' ),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
	);
}

/**
 * @param string $key   Field key.
 * @param string $label Tab label.
 * @return array<string, string>
 */
function ast_tab( $key, $label ) {
	return array(
		'key'   => $key,
		'label' => $label,
		'type'  => 'tab',
	);
}

/**
 * @param string $key         Field key.
 * @param string $name        Field name.
 * @param string $label       Label.
 * @param string $default     Default value.
 * @param string $instruction Instructions.
 * @return array<string, string>
 */
function ast_text( $key, $name, $label, $default = '', $instruction = '' ) {
	return array(
		'key'           => $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'text',
		'default_value' => $default,
		'instructions'  => $instruction,
	);
}

/**
 * @param string $key         Field key.
 * @param string $name        Field name.
 * @param string $label       Label.
 * @param string $default     Default value.
 * @param string $instruction Instructions.
 * @return array<string, string>
 */
function ast_textarea( $key, $name, $label, $default = '', $instruction = '' ) {
	return array(
		'key'           => $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'textarea',
		'rows'          => 6,
		'new_lines'     => '',
		'default_value' => $default,
		'instructions'  => $instruction,
	);
}

/**
 * @param string $key   Field key.
 * @param string $name  Field name.
 * @param string $label Label.
 * @return array<string, string>
 */
function ast_image( $key, $name, $label ) {
	return array(
		'key'           => $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'image',
		'return_format' => 'id',
		'preview_size'  => 'medium',
		'library'       => 'all',
	);
}

/**
 * @param string $key   Field key.
 * @param string $name  Field name.
 * @param string $label Label.
 * @return array<string, string>
 */
function ast_link_field( $key, $name, $label ) {
	return array(
		'key'           => $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'link',
		'return_format' => 'array',
	);
}
