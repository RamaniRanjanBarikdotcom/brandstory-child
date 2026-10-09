<?php
/**
 * Customizer: footer contact details and social links.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function brandstory_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'brandstory_contact',
		array(
			'title'    => __( 'BrandStory: Contact & Social', 'brandstory' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'footer_about' => array( __( 'Footer intro text', 'brandstory' ), 'textarea', 'sanitize_textarea_field' ),
		'head_office'  => array( __( 'Head office address', 'brandstory' ), 'textarea', 'sanitize_textarea_field' ),
		'phone'        => array( __( 'Phone', 'brandstory' ), 'text', 'sanitize_text_field' ),
		'email'        => array( __( 'Email', 'brandstory' ), 'email', 'sanitize_email' ),
		'address'      => array( __( 'Office address', 'brandstory' ), 'textarea', 'sanitize_textarea_field' ),
		'linkedin'     => array( 'LinkedIn URL', 'url', 'esc_url_raw' ),
		'youtube'      => array( 'YouTube URL', 'url', 'esc_url_raw' ),
		'facebook'     => array( 'Facebook URL', 'url', 'esc_url_raw' ),
		'instagram'    => array( 'Instagram URL', 'url', 'esc_url_raw' ),
		'twitter'      => array( 'Twitter / X URL', 'url', 'esc_url_raw' ),
	);

	foreach ( $fields as $key => $field ) {
		list( $label, $type, $sanitize ) = $field;

		$wp_customize->add_setting(
			'brandstory_' . $key,
			array(
				'default'           => brandstory_setting( $key ),
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'brandstory_' . $key,
			array(
				'label'   => $label,
				'section' => 'brandstory_contact',
				'type'    => $type,
			)
		);
	}
}
add_action( 'customize_register', 'brandstory_customize_register' );
