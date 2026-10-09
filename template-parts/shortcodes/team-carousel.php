<?php
/**
 * [bs_team_carousel] – centre-mode photo carousel.
 *
 * @package BrandStory
 * @var array $args { slides: array[] }
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="bs-team">
	<div class="container">
		<div class="slider-nav">
			<button class="slider-btn slider-btn--prev" type="button" data-team-prev aria-label="<?php esc_attr_e( 'Previous team photo', 'brandstory' ); ?>"><?php brandstory_icon( 'arrow-left' ); ?></button>
			<button class="slider-btn slider-btn--next" type="button" data-team-next aria-label="<?php esc_attr_e( 'Next team photo', 'brandstory' ); ?>"><?php brandstory_icon( 'arrow-right' ); ?></button>
		</div>
	</div>
	<div class="swiper team__slider" data-team-slider>
		<div class="swiper-wrapper">
			<?php foreach ( $args['slides'] as $photo ) : ?>
				<div class="swiper-slide team__slide">
					<img src="<?php echo esc_url( $photo['src'] ); ?>"<?php echo $photo['srcset'] ? ' srcset="' . esc_attr( $photo['srcset'] ) . '" sizes="(max-width: 575px) 80vw, 380px"' : ''; ?> alt="<?php echo esc_attr( $photo['alt'] ); ?>" width="<?php echo (int) $photo['width']; ?>" height="<?php echo (int) $photo['height']; ?>" decoding="async">
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
