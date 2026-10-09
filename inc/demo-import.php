<?php
/**
 * One-click page import: Appearance → BrandStory Setup (or `wp brandstory import`).
 *
 * 1. Uploads the images referenced by data/elementor-page.json to the Media Library.
 * 2. Creates (or updates) the "Digital Marketing Agency in Abu Dhabi" page with that Elementor data.
 * 3. Saves a copy to Elementor → Templates so it can be exported / re-used.
 * 4. Sets the page as the static front page.
 * 5. Creates the four menus (primary, footer x2, locations) if they are not assigned yet.
 *
 * Safe to run more than once: assets and the page are reused, not duplicated.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the admin page.
 */
function brandstory_setup_menu() {
	add_theme_page(
		__( 'BrandStory Setup', 'brandstory' ),
		__( 'BrandStory Setup', 'brandstory' ),
		'edit_theme_options',
		'brandstory-setup',
		'brandstory_setup_page'
	);
}
add_action( 'admin_menu', 'brandstory_setup_menu' );

/**
 * Admin page markup.
 */
function brandstory_setup_page() {
	$page_id = (int) get_option( 'brandstory_page_id' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'BrandStory Setup', 'brandstory' ); ?></h1>
		<?php if ( isset( $_GET['imported'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Landing page imported and set as the front page.', 'brandstory' ); ?></p></div>
		<?php elseif ( isset( $_GET['import_error'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-error"><p><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['import_error'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?></p></div>
		<?php endif; ?>

		<p><?php esc_html_e( 'Imports the Abu Dhabi landing page (built with Elementor widgets), uploads its images, creates the menus and sets the page as your homepage.', 'brandstory' ); ?></p>
		<?php if ( ! did_action( 'elementor/loaded' ) ) : ?>
			<p><strong><?php esc_html_e( 'Install and activate the Elementor plugin first.', 'brandstory' ); ?></strong></p>
		<?php else : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="brandstory_import">
				<?php wp_nonce_field( 'brandstory_import' ); ?>
				<?php submit_button( $page_id ? __( 'Re-import landing page', 'brandstory' ) : __( 'Import landing page', 'brandstory' ), 'primary', 'submit', false ); ?>
				<?php if ( $page_id && get_post( $page_id ) ) : ?>
					&nbsp;<a class="button" href="<?php echo esc_url( get_permalink( $page_id ) ); ?>"><?php esc_html_e( 'View page', 'brandstory' ); ?></a>
					&nbsp;<a class="button" href="<?php echo esc_url( admin_url( 'post.php?post=' . $page_id . '&action=elementor' ) ); ?>"><?php esc_html_e( 'Edit with Elementor', 'brandstory' ); ?></a>
				<?php endif; ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Handle the import form.
 */
function brandstory_handle_import() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'brandstory' ) );
	}
	check_admin_referer( 'brandstory_import' );

	$result = brandstory_run_import();
	$args   = is_wp_error( $result ) ? array( 'import_error' => rawurlencode( $result->get_error_message() ) ) : array( 'imported' => 1 );

	wp_safe_redirect( add_query_arg( $args, admin_url( 'themes.php?page=brandstory-setup' ) ) );
	exit;
}
add_action( 'admin_post_brandstory_import', 'brandstory_handle_import' );

/**
 * Run the import.
 *
 * @return int|WP_Error Page ID.
 */
function brandstory_run_import() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return new WP_Error( 'no_elementor', __( 'Elementor is not active.', 'brandstory' ) );
	}

	$file = BRANDSTORY_DIR . '/data/elementor-page.json';
	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( empty( $data['content'] ) ) {
		return new WP_Error( 'bad_json', __( 'Template data is missing or invalid.', 'brandstory' ) );
	}

	$content = brandstory_import_resolve_assets( $data['content'] );

	// Page.
	$page_id = (int) get_option( 'brandstory_page_id' );
	if ( ! $page_id || ! get_post( $page_id ) ) {
		$page_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => $data['title'],
				'post_name'   => 'digital-marketing-agency-abu-dhabi',
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			return $page_id;
		}
		update_option( 'brandstory_page_id', $page_id );
	}
	update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );

	$saved = brandstory_import_save_document( $page_id, $content, $data['page_settings'] );
	if ( is_wp_error( $saved ) ) {
		return $saved;
	}

	// Library copy (Elementor → Templates → Saved Templates).
	$template_id = (int) get_option( 'brandstory_template_id' );
	if ( ! $template_id || ! get_post( $template_id ) ) {
		$template_id = wp_insert_post(
			array(
				'post_type'   => 'elementor_library',
				'post_status' => 'publish',
				'post_title'  => 'BrandStory – Abu Dhabi Landing Page',
			)
		);
		if ( $template_id && ! is_wp_error( $template_id ) ) {
			update_option( 'brandstory_template_id', $template_id );
			update_post_meta( $template_id, '_elementor_template_type', 'page' );
			wp_set_object_terms( $template_id, 'page', 'elementor_library_type' );
		}
	}
	if ( $template_id && ! is_wp_error( $template_id ) ) {
		brandstory_import_save_document( $template_id, $content, $data['page_settings'] );
	}

	// Front page.
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_id );

	brandstory_import_menus();

	\Elementor\Plugin::$instance->files_manager->clear_cache();

	return $page_id;
}

/**
 * Save Elementor data through the Document API (handles meta, version and CSS cache).
 *
 * @param int   $post_id  Post ID.
 * @param array $elements Elements.
 * @param array $settings Page settings.
 * @return true|WP_Error
 */
function brandstory_import_save_document( $post_id, array $elements, array $settings ) {
	$document = \Elementor\Plugin::$instance->documents->get( $post_id, false );
	if ( ! $document ) {
		return new WP_Error( 'no_document', __( 'Could not load the Elementor document.', 'brandstory' ) );
	}
	$document->set_is_built_with_elementor( true );
	$ok = $document->save(
		array(
			'elements' => $elements,
			'settings' => $settings,
		)
	);
	return $ok ? true : new WP_Error( 'save_failed', __( 'Elementor could not save the page (check user permissions).', 'brandstory' ) );
}

/**
 * Replace {{asset:path}} tokens in image controls with uploaded attachments.
 *
 * @param array $elements Elements tree.
 * @return array
 */
function brandstory_import_resolve_assets( array $elements ) {
	foreach ( $elements as &$element ) {
		if ( ! empty( $element['settings'] ) && is_array( $element['settings'] ) ) {
			foreach ( $element['settings'] as &$value ) {
				if ( is_array( $value ) && isset( $value['url'] ) && preg_match( '/^\{\{asset:(.+)\}\}$/', $value['url'], $m ) ) {
					$attachment     = brandstory_import_asset( $m[1], isset( $value['alt'] ) ? $value['alt'] : '' );
					$value['url']   = $attachment['url'];
					$value['id']    = $attachment['id'];
				}
			}
			unset( $value );
		}
		if ( ! empty( $element['elements'] ) ) {
			$element['elements'] = brandstory_import_resolve_assets( $element['elements'] );
		}
	}
	unset( $element );
	return $elements;
}

/**
 * Upload a bundled asset once and remember it. SVGs are not uploadable in core WordPress,
 * so they are referenced from the theme folder directly.
 *
 * @param string $path Path inside /assets.
 * @param string $alt  Alt text.
 * @return array{id:int|string, url:string}
 */
function brandstory_import_asset( $path, $alt ) {
	$source = BRANDSTORY_DIR . '/assets/' . $path;
	$map    = (array) get_option( 'brandstory_imported_assets', array() );

	if ( ! empty( $map[ $path ] ) && wp_get_attachment_url( $map[ $path ] ) ) {
		return array( 'id' => (int) $map[ $path ], 'url' => wp_get_attachment_url( $map[ $path ] ) );
	}

	if ( 'svg' === strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) || ! is_readable( $source ) ) {
		return array( 'id' => '', 'url' => brandstory_asset( $path ) );
	}

	$upload = wp_upload_bits( 'brandstory-' . basename( $path ), null, (string) file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $upload['error'] ) ) {
		return array( 'id' => '', 'url' => brandstory_asset( $path ) );
	}

	$type          = wp_check_filetype( $upload['file'] );
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $type['type'],
			'post_title'     => sanitize_title( pathinfo( $path, PATHINFO_FILENAME ) ),
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return array( 'id' => '', 'url' => brandstory_asset( $path ) );
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	if ( $alt ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
	}

	$map[ $path ] = $attachment_id;
	update_option( 'brandstory_imported_assets', $map, false );

	return array( 'id' => $attachment_id, 'url' => wp_get_attachment_url( $attachment_id ) );
}

/**
 * Create the default menus for any location that has none.
 */
function brandstory_import_menus() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	foreach ( brandstory_default_menus() as $location => $menu ) {
		if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( $locations[ $location ] ) ) {
			continue;
		}

		$existing = wp_get_nav_menu_object( $menu['name'] );
		$menu_id  = $existing ? $existing->term_id : wp_create_nav_menu( $menu['name'] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}

		if ( ! $existing ) {
			foreach ( $menu['items'] as $entry ) {
				$parent_id = wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $entry['title'],
						'menu-item-url'    => $entry['url'],
						'menu-item-status' => 'publish',
						'menu-item-type'   => 'custom',
					)
				);
				foreach ( $entry['children'] as $child ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'     => $child['title'],
							'menu-item-url'       => $child['url'],
							'menu-item-status'    => 'publish',
							'menu-item-type'      => 'custom',
							'menu-item-parent-id' => $parent_id,
						)
					);
				}
			}
		}

		$locations[ $location ] = $menu_id;
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * WP-CLI: `wp brandstory import`.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'brandstory import',
		static function () {
			if ( ! get_current_user_id() ) {
				$admins = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
				if ( $admins ) {
					wp_set_current_user( $admins[0]->ID );
				}
			}
			$result = brandstory_run_import();
			if ( is_wp_error( $result ) ) {
				WP_CLI::error( $result->get_error_message() );
			}
			WP_CLI::success( 'Imported page #' . $result . ': ' . get_permalink( $result ) );
		}
	);
}
