<?php
/**
 * Site header (overrides Hello Elementor's header.php).
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/global/icon-sprite' ); ?>

<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'brandstory' ); ?></a>

<?php get_template_part( 'template-parts/header/site-header' ); ?>

<main id="content" class="site-main">
