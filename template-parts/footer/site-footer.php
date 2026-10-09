<?php
/**
 * Footer: brand + head office, two link menus, contact details, social links,
 * copyright, partner badges and the "Other locations" block.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;

$brandstory_social = array(
	'linkedin'  => 'Linkedin',
	'youtube'   => 'Youtube',
	'facebook'  => 'Facebook',
	'instagram' => 'Instagram',
	'twitter'   => 'Twitter',
);
$brandstory_phone  = brandstory_setting( 'phone' );
$brandstory_email  = brandstory_setting( 'email' );
$brandstory_addr   = brandstory_setting( 'address' );
?>
<footer class="site-footer" id="contact">
	<div class="site-footer__main container container--wide">
		<div class="footer-col footer-col--brand">
			<a class="logo logo--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>">BRANDSTORY</a>
			<p><?php echo esc_html( brandstory_setting( 'footer_about' ) ); ?></p>
			<a class="btn btn--chip" href="#quote"><?php esc_html_e( 'Contact Sales', 'brandstory' ); ?></a>
			<address><strong><?php esc_html_e( 'Head Office:', 'brandstory' ); ?></strong><br><?php echo esc_html( brandstory_setting( 'head_office' ) ); ?></address>
		</div>

		<nav class="footer-col" aria-labelledby="footer-links-title">
			<h2 class="footer-col__title" id="footer-links-title"><?php esc_html_e( 'Our Links', 'brandstory' ); ?></h2>
			<ul class="footer-links"><?php brandstory_menu_items( 'footer_links' ); ?></ul>
		</nav>

		<nav class="footer-col" aria-labelledby="footer-services-title">
			<h2 class="footer-col__title" id="footer-services-title"><?php esc_html_e( 'Services', 'brandstory' ); ?></h2>
			<ul class="footer-links"><?php brandstory_menu_items( 'footer_services' ); ?></ul>
		</nav>

		<div class="footer-col">
			<h2 class="footer-col__title"><?php esc_html_e( 'Contact', 'brandstory' ); ?></h2>
			<ul class="footer-contact">
				<li><?php esc_html_e( 'Phone:', 'brandstory' ); ?><br><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $brandstory_phone ) ); ?>"><?php echo esc_html( $brandstory_phone ); ?></a></li>
				<li><?php esc_html_e( 'Email:', 'brandstory' ); ?><br><a href="mailto:<?php echo esc_attr( antispambot( $brandstory_email ) ); ?>"><?php echo esc_html( antispambot( $brandstory_email ) ); ?></a></li>
				<li><?php esc_html_e( 'Address:', 'brandstory' ); ?><br><a href="<?php echo esc_url( 'https://maps.google.com/?q=' . rawurlencode( $brandstory_addr ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $brandstory_addr ); ?></a></li>
			</ul>
		</div>

		<div class="footer-col">
			<h2 class="footer-col__title"><?php esc_html_e( 'Follow us', 'brandstory' ); ?></h2>
			<ul class="footer-social">
				<?php foreach ( $brandstory_social as $brandstory_key => $brandstory_label ) : ?>
					<?php $brandstory_url = brandstory_setting( $brandstory_key ); ?>
					<?php if ( $brandstory_url ) : ?>
						<li><a href="<?php echo esc_url( $brandstory_url ); ?>" target="_blank" rel="noopener"><?php brandstory_icon( $brandstory_key ); ?><?php echo esc_html( $brandstory_label ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="site-footer__bottom">
		<p>
			<?php
			/* translators: %s: current year. */
			printf( esc_html__( 'Copyright © Brandstory, All Rights Reserved %s', 'brandstory' ), esc_html( wp_date( 'Y' ) ) );
			?>
			| <a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'brandstory' ); ?></a>
			| <a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'brandstory' ); ?></a>
		</p>
	</div>

	<div class="partners-strip">
		<ul class="partners-strip__list">
			<li><img src="<?php echo esc_url( brandstory_asset( 'images/partners/partner-google.webp' ) ); ?>" alt="Google Partner" width="130" height="73" loading="lazy"></li>
			<li><img src="<?php echo esc_url( brandstory_asset( 'images/partners/partner-bing.webp' ) ); ?>" alt="Bing Partner" width="130" height="69" loading="lazy"></li>
			<li><img src="<?php echo esc_url( brandstory_asset( 'images/partners/partner-meta.webp' ) ); ?>" alt="Meta Marketing Partner" width="130" height="69" loading="lazy"></li>
			<li><img src="<?php echo esc_url( brandstory_asset( 'images/partners/partner-hubspot.webp' ) ); ?>" alt="HubSpot Certified Partner" width="130" height="69" loading="lazy"></li>
		</ul>
	</div>

	<?php get_template_part( 'template-parts/footer/locations' ); ?>
</footer>
