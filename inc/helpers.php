<?php
/**
 * Content helpers. Every public string falls back to the R05 copy.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verbatim defaults from the Figma frame.
 *
 * @return array<string, string>
 */
function ast_defaults() {
	return array(
		'hero_eyebrow'       => 'ALPHA SOLAR TECHNOLOGIES',
		'hero_heading'       => 'Built to be <em>seen</em>, designed to <em>perform</em>.',
		'hero_body'          => "ArchiSolar brings architectural design and solar energy together.\nDeveloped by Alpha Solar Technologies, our technology is designed to bring high-resolution colour, imagery and finishes to building-integrated photovoltaics (BIPV), while preserving as much solar energy output as possible. Our ambition is to give architects and building owners greater freedom to create the look they want, with solar performance at the heart of the design.",
		'announce_heading'   => 'Our <em>refreshed identity</em> and new website is coming: <em>1 February 2027</em>.',
		'business_heading'   => 'Technology with global potential. <em>A business model built around its use.</em>',
		'business_body'      => "Alpha Solar Technologies is developing ArchiSolar for commercial licensing, with the ambition to bring high-resolution architectural finishes to building-integrated solar worldwide. Our proposed commercial model combines licence fees, royalties and usage-based fees, linking potential revenue to the adoption and ongoing use of ArchiSolar technology.\n\nFor investors, the opportunity extends beyond an individual panel or print: it is participation in the development and commercialisation of technology intended for use across international BIPV markets. Reach out if you have any questions.",
		'licensing_heading'  => 'A licensing model with <em>revenue tied to every print</em>',
		'licensing_body'     => 'AST is preparing for a proposed NSX listing as we develop ArchiSolar for commercial licensing to glass manufacturers and printing technology companies.',
		'contact_heading'    => '<em>Discuss investment</em> with AST',
		'contact_intro'      => 'Contact our team to discuss investment in ArchiSolar’s development and commercialisation.',
		'contact_privacy_label' => 'Privacy policy',
		'footer_text'           => '© Alpha Solar Technologies Limited',
		'announce_label'        => 'Enquire about investment',
		'licensing_label'       => 'Request investment information',
		'header_cta_label'      => 'Contact Us',
		'header_cta_url'        => '#contact',
	);
}

/**
 * The page whose ACF fields feed the landing page.
 *
 * @return int
 */
function ast_context_id() {
	$front = (int) get_option( 'page_on_front' );
	if ( $front && ( is_front_page() || is_admin() ) ) {
		return $front;
	}

	$queried = (int) get_queried_object_id();
	if ( $queried ) {
		return $queried;
	}

	return $front;
}

/**
 * ACF value, or the Figma default when the field is empty.
 *
 * @param string $key Field name.
 * @return string
 */
function ast_field( $key ) {
	$defaults = ast_defaults();
	$value    = null;

	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, ast_context_id() );
	}

	if ( null === $value || false === $value || '' === $value ) {
		return $defaults[ $key ] ?? '';
	}

	return is_string( $value ) ? $value : '';
}

/**
 * Optional class and id saved beside a text or image field.
 *
 * Classes use hyphens. A single id uses underscores.
 *
 * @param string $name Content field name.
 * @return array{class: string, id: string}
 */
function ast_content_attrs( $name ) {
	$class = '';
	$id    = '';

	if ( function_exists( 'get_field' ) ) {
		$class = ast_sanitize_classes( get_field( $name . '_class', ast_context_id() ) );
		$id    = ast_sanitize_id( get_field( $name . '_id', ast_context_id() ) );
	}

	return array(
		'class' => $class,
		'id'    => $id,
	);
}

/**
 * @param mixed $value Raw class value.
 * @return string
 */
function ast_sanitize_classes( $value ) {
	$value  = strtolower( str_replace( '_', '-', (string) $value ) );
	$tokens = preg_split( '/\s+/', $value );
	$clean  = array();

	if ( ! is_array( $tokens ) ) {
		return '';
	}

	foreach ( $tokens as $token ) {
		$token = preg_replace( '/[^a-z0-9-]/', '', $token );
		$token = trim( (string) $token, '-' );
		if ( '' !== $token && ! preg_match( '/^[0-9]/', $token ) ) {
			$clean[] = $token;
		}
	}

	return implode( ' ', array_unique( $clean ) );
}

/**
 * @param mixed $value Raw id value.
 * @return string
 */
function ast_sanitize_id( $value ) {
	$value = strtolower( trim( str_replace( '-', '_', (string) $value ) ) );
	$value = preg_replace( '/[^a-z0-9_]/', '', $value );
	$value = trim( (string) $value, '_' );

	if ( '' === $value || preg_match( '/^[0-9]/', $value ) ) {
		return '';
	}

	return $value;
}

/**
 * Id that will be rendered, or the theme fallback when the field is empty.
 *
 * @param string $name     Content field name.
 * @param string $fallback Existing id.
 * @return string
 */
function ast_content_id( $name, $fallback = '' ) {
	$attrs = ast_content_attrs( $name );
	return '' !== $attrs['id'] ? $attrs['id'] : $fallback;
}

/**
 * Class and id attributes. Extra classes are added to the theme class.
 *
 * @param string $name       Content field name.
 * @param string $base_class Theme class names.
 * @param string $base_id    Theme id.
 * @return string
 */
function ast_attr_html( $name, $base_class = '', $base_id = '' ) {
	$attrs = ast_content_attrs( $name );
	$class = trim( $base_class . ' ' . $attrs['class'] );
	$id    = '' !== $attrs['id'] ? $attrs['id'] : $base_id;
	$html  = '';

	if ( '' !== $class ) {
		$html .= ' class="' . esc_attr( $class ) . '"';
	}
	if ( '' !== $id ) {
		$html .= ' id="' . esc_attr( $id ) . '"';
	}

	return $html;
}

/**
 * Heading, eyebrow, and footer copy. Block tags are flattened so the
 * result can sit inside an existing heading or paragraph.
 *
 * @param string $key Field name.
 * @return string
 */
function ast_inline_html( $key ) {
	$html = trim( ast_field( $key ) );
	$html = preg_replace( '#</p>\s*<p[^>]*>#i', '<br>', $html );
	$html = preg_replace( '#</?p[^>]*>#i', '', $html );

	return wp_kses(
		$html,
		array(
			'em'     => array(),
			'strong' => array(),
			'b'      => array(),
			'i'      => array(),
			'u'      => array(),
			'br'     => array(),
			'a'      => array(
				'href'   => true,
				'target' => true,
				'rel'    => true,
			),
		)
	);
}

/**
 * Heading copy. Editors mark highlights with italic.
 *
 * @param string $key Field name.
 * @return string
 */
function ast_highlight( $key ) {
	return ast_inline_html( $key );
}

/**
 * Body copy. Plain text still gets paragraphs; saved HTML is kept.
 *
 * @param string $key Field name.
 * @return string
 */
function ast_rich_text( $key ) {
	$html = ast_field( $key );
	if ( ! preg_match( '/<(?:p|br|ul|ol|div|h[1-6]|blockquote)\b/i', $html ) ) {
		$html = wpautop( $html );
	}

	return wp_kses_post( $html );
}

/**
 * Plain text with hard line breaks preserved.
 *
 * @param string $key Field name.
 * @return string
 */
function ast_text_br( $key ) {
	return ast_rich_text( $key );
}

/**
 * Plain text turned into paragraphs.
 *
 * @param string $key Field name.
 * @return string
 */
function ast_paragraphs( $key ) {
	return ast_rich_text( $key );
}

/**
 * Link field with a hardcoded fallback.
 *
 * @param string $key      ACF link field.
 * @param string $title    Fallback label.
 * @param string $url      Fallback URL.
 * @return array{title: string, url: string, target: string}
 */
function ast_link( $key, $title, $url ) {
	$link = array(
		'title'  => $title,
		'url'    => $url,
		'target' => '',
	);

	if ( ! function_exists( 'get_field' ) ) {
		return $link;
	}

	$value = get_field( $key, ast_context_id() );
	if ( is_array( $value ) && ! empty( $value['url'] ) ) {
		$link['url']    = $value['url'];
		$link['title']  = ! empty( $value['title'] ) ? $value['title'] : $title;
		$link['target'] = isset( $value['target'] ) ? (string) $value['target'] : '';
	}

	return $link;
}

/**
 * Northeast arrow used inside pill buttons.
 *
 * @return string
 */
function ast_arrow_svg() {
	return '<svg viewBox="0 0 12 12" width="12" height="12" aria-hidden="true" focusable="false"><path d="M2.2 9.8 L9.8 2.2 M9.8 2.2 H4.3 M9.8 2.2 V7.7" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/**
 * Chevron used by the scroll button and carousel.
 *
 * @return string
 */
function ast_chevron_svg() {
	/* === AST-FIXES-01 G1: render the full 60x60 box so the chevron path draws at 27x14 (Figma 72:573). === */
	return '<svg viewBox="0 0 60 60" width="60" height="60" aria-hidden="true" focusable="false"><path d="M16.43 25.71 L30 39.29 L43.57 25.71" fill="none" stroke="currentColor" stroke-width="4.29" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/**
 * Pill or outline button.
 *
 * @param array{title: string, url: string, target: string} $link    Link parts.
 * @param string                                            $variant primary|dark|outline.
 * @param string                                            $extra   Extra class names.
 * @param string                                            $id      Optional element id.
 * @return string
 */
function ast_button( $link, $variant, $extra = '', $id = '' ) {
	$class  = trim( 'btn btn--' . $variant . ' ' . $extra );
	$target = '';
	$id     = ast_sanitize_id( $id );
	if ( '_blank' === $link['target'] ) {
		$target = ' target="_blank" rel="noopener noreferrer"';
	}
	if ( '' !== $id ) {
		$target = ' id="' . esc_attr( $id ) . '"' . $target;
	}

	$icon = '';
	if ( 'outline' !== $variant ) {
		$icon = '<span class="btn-icon" aria-hidden="true">' . ast_arrow_svg() . '</span>';
	}

	return sprintf(
		'<a class="%1$s" href="%2$s"%3$s><span class="btn-label">%4$s</span>%5$s</a>',
		esc_attr( $class ),
		esc_url( $link['url'] ),
		$target,
		esc_html( $link['title'] ),
		$icon
	);
}

/**
 * Attachment image, or a theme file when the media field is empty.
 *
 * @param array<string, mixed> $args Image arguments.
 * @return string
 */
function ast_picture( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'field'          => '',
			'size'           => 'full',
			'file'           => '',
			'width'          => 0,
			'height'         => 0,
			'alt'            => '',
			'class'          => '',
			'id'             => '',
			'attr_field'     => '',
			'loading'        => 'lazy',
			'fetchpriority'  => '',
			'decorative'     => false,
		)
	);

	$id = 0;
	if ( $args['field'] && function_exists( 'get_field' ) ) {
		$id = ast_attachment_id( get_field( $args['field'], ast_context_id() ) );
	}

	if ( $args['attr_field'] ) {
		$extra          = ast_content_attrs( $args['attr_field'] );
		$args['class']  = trim( $args['class'] . ' ' . $extra['class'] );
		if ( '' !== $extra['id'] ) {
			$args['id'] = $extra['id'];
		}
	}

	if ( $id && ast_attachment_file_exists( $id ) ) {
		$attr = array(
			'class'    => $args['class'],
			'loading'  => $args['loading'],
			'decoding' => 'async',
		);
		if ( $args['id'] ) {
			$attr['id'] = $args['id'];
		}
		if ( $args['fetchpriority'] ) {
			$attr['fetchpriority'] = $args['fetchpriority'];
		}
		if ( $args['decorative'] ) {
			$attr['alt'] = '';
		}
		$html = wp_get_attachment_image( $id, $args['size'], false, $attr );
		if ( $html ) {
			return $html;
		}
	}

	$src  = AST_URI . '/assets/img/' . ltrim( (string) $args['file'], '/' );
	$alt  = $args['decorative'] ? '' : (string) $args['alt'];
	$html = sprintf(
		'<img src="%1$s" alt="%2$s" width="%3$d" height="%4$d" class="%5$s" loading="%6$s" decoding="async"%7$s%8$s%9$s>',
		esc_url( $src ),
		esc_attr( $alt ),
		(int) $args['width'],
		(int) $args['height'],
		esc_attr( $args['class'] ),
		esc_attr( $args['loading'] ),
		$args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : '',
		$args['fetchpriority'] ? ' fetchpriority="' . esc_attr( $args['fetchpriority'] ) . '"' : '',
		$args['decorative'] ? ' aria-hidden="true"' : ''
	);

	return $html;
}

/**
 * Licensing slide attachment IDs. Free ACF uses three fixed fields.
 *
 * @return int[]
 */
function ast_slide_ids() {
	$ids = array();
	foreach ( ast_licensing_slides() as $slide ) {
		$ids[] = (int) $slide['id'];
	}
	return $ids;
}

/**
 * Licensing slides, with the image field that can supply a class and id.
 *
 * @return array<int, array{id: int, field: string}>
 */
function ast_licensing_slides() {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$field_ids = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		$field = 'licensing_slide_' . $i;
		$id    = ast_attachment_id( get_field( $field, ast_context_id() ) );
		if ( $id && ast_attachment_file_exists( $id ) ) {
			$field_ids[ $id ] = $field;
		}
	}

	$slides = array();
	$seen   = array();
	$add    = function ( $id, $field ) use ( &$slides, &$seen ) {
		$id = (int) $id;
		if ( ! $id || isset( $seen[ $id ] ) || ! ast_attachment_file_exists( $id ) ) {
			return;
		}
		$slides[]   = array(
			'id'    => $id,
			'field' => $field,
		);
		$seen[ $id ] = true;
	};

	$gallery = get_field( 'licensing_slides', ast_context_id() );
	if ( is_array( $gallery ) ) {
		foreach ( $gallery as $item ) {
			$id = ast_attachment_id( $item );
			$add( $id, $field_ids[ $id ] ?? '' );
		}
	}

	foreach ( $field_ids as $id => $field ) {
		$add( $id, $field );
	}

	return $slides;
}

/**
 * Image field value as an attachment ID.
 *
 * @param mixed $value ACF image value.
 * @return int
 */
function ast_attachment_id( $value ) {
	if ( is_array( $value ) ) {
		return (int) ( $value['ID'] ?? $value['id'] ?? 0 );
	}
	return (int) $value;
}

/**
 * True when the attachment file is actually on this server.
 *
 * @param int $id Attachment ID.
 * @return bool
 */
function ast_attachment_file_exists( $id ) {
	$id = (int) $id;
	if ( ! $id ) {
		return false;
	}
	$file = get_attached_file( $id );
	return is_string( $file ) && '' !== $file && file_exists( $file );
}
