<?php
/**
 * Quote modal. Opened by any link to "#quote" (see main.js).
 * Posts via AJAX; falls back to admin-post.php without JS.
 *
 * @package BrandStory
 */

defined( 'ABSPATH' ) || exit;
?>
<dialog class="modal" id="contact-modal" aria-labelledby="modal-title">
	<form class="modal__form" id="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="brandstory_contact">
		<?php wp_nonce_field( 'brandstory_contact', 'nonce', false ); ?>
		<p class="visually-hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>

		<button class="modal__close" type="button" data-close-modal aria-label="<?php esc_attr_e( 'Close', 'brandstory' ); ?>"><?php brandstory_icon( 'close' ); ?></button>
		<h2 class="modal__title" id="modal-title"><?php esc_html_e( 'Get a Free Quote', 'brandstory' ); ?></h2>
		<p class="modal__intro"><?php esc_html_e( 'Tell us about your goals and our Abu Dhabi team will get back to you within one business day.', 'brandstory' ); ?></p>

		<div class="form-row">
			<label for="f-name"><?php esc_html_e( 'Full name', 'brandstory' ); ?></label>
			<input id="f-name" name="name" type="text" autocomplete="name" required>
		</div>
		<div class="form-row form-row--half">
			<div>
				<label for="f-email"><?php esc_html_e( 'Email', 'brandstory' ); ?></label>
				<input id="f-email" name="email" type="email" autocomplete="email" required>
			</div>
			<div>
				<label for="f-phone"><?php esc_html_e( 'Phone', 'brandstory' ); ?></label>
				<input id="f-phone" name="phone" type="tel" autocomplete="tel" pattern="[0-9+()\s-]{7,}">
			</div>
		</div>
		<div class="form-row">
			<label for="f-subject"><?php esc_html_e( 'Interested in', 'brandstory' ); ?></label>
			<input id="f-subject" name="subject" type="text" placeholder="<?php esc_attr_e( 'e.g. SEO, PPC, Social Media', 'brandstory' ); ?>">
		</div>
		<div class="form-row">
			<label for="f-message"><?php esc_html_e( 'Message', 'brandstory' ); ?></label>
			<textarea id="f-message" name="message" rows="4" required></textarea>
		</div>
		<p class="form-status" role="status" aria-live="polite"></p>
		<button class="btn btn--primary btn--block" type="submit"><?php esc_html_e( 'Send Enquiry', 'brandstory' ); ?></button>
	</form>
</dialog>
