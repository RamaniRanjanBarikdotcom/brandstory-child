<?php
/**
 * [bs_case_studies] – case-study card carousel.
 *
 * @package BrandStory
 * @var array $args { cases: array[], tags: string[] }
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="bs-cases">
	<div class="slider-nav">
		<button class="slider-btn slider-btn--prev" type="button" data-cases-prev aria-label="<?php esc_attr_e( 'Previous case study', 'brandstory' ); ?>"><?php brandstory_icon( 'arrow-left' ); ?></button>
		<button class="slider-btn slider-btn--next" type="button" data-cases-next aria-label="<?php esc_attr_e( 'Next case study', 'brandstory' ); ?>"><?php brandstory_icon( 'arrow-right' ); ?></button>
	</div>
	<div class="swiper cases__slider" data-cases-slider>
		<div class="swiper-wrapper">
			<?php foreach ( $args['cases'] as $index => $case ) : ?>
				<article class="swiper-slide case-card">
					<img src="<?php echo esc_url( $case['src'] ); ?>"<?php echo $case['srcset'] ? ' srcset="' . esc_attr( $case['srcset'] ) . '" sizes="(max-width: 639px) 85vw, (max-width: 991px) 50vw, 420px"' : ''; ?> alt="<?php echo esc_attr( $case['alt'] ); ?>" width="<?php echo (int) $case['width']; ?>" height="<?php echo (int) $case['height']; ?>" decoding="async">
					<div class="case-card__overlay">
						<?php if ( ! empty( $args['tags'][ $index ] ) ) : ?>
							<span class="case-card__tag"><?php echo esc_html( $args['tags'][ $index ] ); ?></span>
						<?php endif; ?>
						<a class="case-card__link" href="#quote" data-subject="<?php esc_attr_e( 'Case study enquiry', 'brandstory' ); ?>"><?php esc_html_e( 'View Case Study', 'brandstory' ); ?> <?php brandstory_icon( 'arrow-right' ); ?></a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</div>
