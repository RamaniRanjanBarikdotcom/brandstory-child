<?php
/**
 * [bs_clients] – two-row logo marquee. JS duplicates each row for a seamless loop.
 *
 * @package BrandStory
 * @var array $args { rows: array[] }
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="bs-clients">
	<?php foreach ( $args['rows'] as $row_index => $row ) : ?>
		<div class="marquee<?php echo 1 === $row_index ? ' marquee--reverse' : ''; ?>" data-marquee>
			<ul class="marquee__track">
				<?php foreach ( $row as $logo ) : ?>
					<li>
						<img src="<?php echo esc_url( $logo['src'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" width="<?php echo (int) $logo['width']; ?>" height="<?php echo (int) $logo['height']; ?>" decoding="async">
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>
