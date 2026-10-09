<?php
/**
 * Header bar: logo, primary navigation (pill), quote button, mobile toggle.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="site-header" id="top">
	<div class="site-header__inner container container--wide">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' – ' . __( 'home', 'brandstory' ) ); ?>">
			BRANDSTORY<span class="logo__mark" aria-hidden="true">&reg;</span>
		</a>

		<nav class="main-nav" id="main-nav" aria-label="<?php esc_attr_e( 'Primary', 'brandstory' ); ?>">
			<ul class="main-nav__list">
				<?php brandstory_menu_items( 'primary' ); ?>
			</ul>
			<a class="btn btn--light main-nav__cta" href="#quote"><?php esc_html_e( 'Get Quote', 'brandstory' ); ?></a>
		</nav>

		<a class="btn btn--light site-header__cta" href="#quote"><?php esc_html_e( 'Get Quote', 'brandstory' ); ?></a>

		<button class="nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'brandstory' ); ?>" aria-expanded="false" aria-controls="main-nav">
			<span class="nav-toggle__bar"></span><span class="nav-toggle__bar"></span><span class="nav-toggle__bar"></span>
		</button>
	</div>
</header>
