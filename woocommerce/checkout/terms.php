<?php
/**
 * Checkout terms and conditions checkbox
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.1.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$terms = get_post(wc_get_page_id('terms'));

echo '<div class="modal fade" id="termModal" tabindex="-1" role="dialog">';
	echo '<div class="modal-dialog" role="document">';
		echo '<div class="modal-content">';
			echo '<div class="modal-header">';
				echo '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>';
				echo '<h4 class="modal-title">' . $terms->post_title . '</h4>';
			echo '</div>';
			echo '<div class="modal-body">';
				echo apply_filters( 'the_content', $terms->post_content);
			echo '</div>';
			echo '<div class="modal-footer">';
				echo '<button type="button" class="btn btn-default" data-terms="false">' . __("Decline", 'b4st') . '</button>';
				echo '<button type="button" class="btn btn-primary" data-terms="true">' . __("Accept", 'b4st') . '</button>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) : ?>
    <p class="form-row terms wc-terms-and-conditions">
	    <span class="styled-input">
	    	<input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
	    	<label for="terms"></label>
	    </span>
        <label for="terms"><?php echo __( 'I\'ve read and accept the <a data-toggle="modal" data-target="#termModal">terms &amp; conditions</a>', 'b4st' ); ?> <span class="required">*</span></label>
        <input type="hidden" name="terms-field" value="1" />
    </p>
<?php endif; ?>
