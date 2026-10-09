<?php
/**
 * "Other Digital Marketing Services Location" block (menu location: locations).
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="locations" id="locations" aria-labelledby="locations-title">
	<div class="container">
		<h2 class="section-title section-title--tight" id="locations-title"><?php esc_html_e( 'Other Digital Marketing Services Location', 'brandstory' ); ?></h2>
		<ul class="locations__list">
			<?php brandstory_menu_items( 'locations' ); ?>
		</ul>
	</div>
</section>
