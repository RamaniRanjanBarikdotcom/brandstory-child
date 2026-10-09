<?php
/**
 * Quote form: AJAX handler + "Enquiries" post type.
 *
 * Every submission is stored as a private bs_enquiry post (so nothing is lost if mail fails)
 * and emailed to the site admin. Works without JS too via admin-post.php.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Enquiries post type (admin only).
 */
function brandstory_register_enquiry_cpt() {
	register_post_type(
		'bs_enquiry',
		array(
			'labels'          => array(
				'name'          => __( 'Enquiries', 'brandstory' ),
				'singular_name' => __( 'Enquiry', 'brandstory' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 26,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'brandstory_register_enquiry_cpt' );

/**
 * Validate + store + email a submission.
 *
 * @param array $raw Raw request data.
 * @return true|WP_Error
 */
function brandstory_process_enquiry( array $raw ) {
	// Honeypot: real users never fill the hidden "website" field.
	if ( ! empty( $raw['website'] ) ) {
		return true;
	}

	$data = array(
		'name'    => sanitize_text_field( wp_unslash( $raw['name'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $raw['email'] ?? '' ) ),
		'phone'   => sanitize_text_field( wp_unslash( $raw['phone'] ?? '' ) ),
		'subject' => sanitize_text_field( wp_unslash( $raw['subject'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $raw['message'] ?? '' ) ),
	);

	if ( '' === $data['name'] || ! is_email( $data['email'] ) || '' === $data['message'] ) {
		return new WP_Error( 'invalid', __( 'Please fill in the highlighted fields.', 'brandstory' ) );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'bs_enquiry',
			'post_status'  => 'private',
			/* translators: 1: name, 2: subject. */
			'post_title'   => sprintf( __( '%1$s – %2$s', 'brandstory' ), $data['name'], $data['subject'] ? $data['subject'] : __( 'General enquiry', 'brandstory' ) ),
			'post_content' => sprintf( "Email: %s\nPhone: %s\nInterested in: %s\n\n%s", $data['email'], $data['phone'], $data['subject'], $data['message'] ),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	wp_mail(
		get_option( 'admin_email' ),
		/* translators: %s: name. */
		sprintf( __( 'New quote request from %s', 'brandstory' ), $data['name'] ),
		get_post_field( 'post_content', $post_id ),
		array( 'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>' )
	);

	return true;
}

/**
 * AJAX endpoint.
 */
function brandstory_ajax_contact() {
	if ( ! check_ajax_referer( 'brandstory_contact', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Session expired. Please refresh and try again.', 'brandstory' ) ), 403 );
	}

	$result = brandstory_process_enquiry( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 422 );
	}
	wp_send_json_success( array( 'message' => __( 'Thank you! Our team will contact you shortly.', 'brandstory' ) ) );
}
add_action( 'wp_ajax_brandstory_contact', 'brandstory_ajax_contact' );
add_action( 'wp_ajax_nopriv_brandstory_contact', 'brandstory_ajax_contact' );

/**
 * No-JS fallback (form posts to admin-post.php).
 */
function brandstory_post_contact() {
	check_admin_referer( 'brandstory_contact', 'nonce' );
	$result = brandstory_process_enquiry( $_POST );
	$back   = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'enquiry', is_wp_error( $result ) ? 'error' : 'sent', $back ) );
	exit;
}
add_action( 'admin_post_brandstory_contact', 'brandstory_post_contact' );
add_action( 'admin_post_nopriv_brandstory_contact', 'brandstory_post_contact' );
