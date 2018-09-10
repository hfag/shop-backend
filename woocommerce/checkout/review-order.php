<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table class="shop_table woocommerce-checkout-review-order-table not-default bill">
	<thead class="hidden-sm-down">
		<tr>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-sku"><?php _e( 'SKU', 'woocommerce' ); ?></th>
			<th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			do_action( 'woocommerce_review_order_before_cart_contents' );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product		= apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$_p				= false;
					
				if(empty($cart_item['variation_id'])){
					$_p = new WC_Product($cart_item['product_id']);
				}else{
					$_p = new WC_Product_Variation($cart_item['variation_id']);
				}

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					?>
					<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
						<td class="product-name hidden-sm-down">
							<?php
								
								echo "<div class='mobile-collapse-toggle'>";
									echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
								echo "</div>";
								
								$uid = "itemMeta".bin2hex(random_bytes(10));
								
								// Meta data
								if(!empty(wc_get_formatted_cart_item_data( $cart_item ))){
									echo "<a class='mobile-collapse' data-toggle='collapse' href='#".$uid."' aria-expanded='false' aria-controls='".$uid."'><i class='fa fa-info-circle'></i> ".__("Show details", 'b4st')."</a>";
									echo "<div class='item-meta collapse' id='".$uid."'>";
										echo wc_get_formatted_cart_item_data( $cart_item );
									echo "</div>";
								}
							?>
						</td>
						<td class="product-sku hidden-sm-down">
							<?php
								echo $_p->get_sku();
							?>
						</td>
						<td class="product-price hidden-sm-down">
							<?php
								echo wc_price($_product->get_price());
							?>
						</td>
						<td class="product-quantity hidden-sm-down">
							<?php echo $cart_item['quantity']; ?>
						</td>
						<td class="product-total hidden-sm-down">
							<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
						</td>
						
						<td colspan="5" class="hidden-md-up">
							<?php
								
								echo "<div class='mobile-collapse-toggle'>";
									echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title() . " (" . $_p->get_sku() . ")", $cart_item, $cart_item_key ) . '&nbsp;';
								echo "</div>";
								
								$uid = "itemMeta".bin2hex(random_bytes(10));
								
								// Meta data
								if(!empty(wc_get_formatted_cart_item_data( $cart_item ))){
									echo "<a class='mobile-collapse' data-toggle='collapse' href='#".$uid."' aria-expanded='false' aria-controls='".$uid."'><i class='fa fa-info-circle'></i> ".__("Show details", 'b4st')."</a>";
									echo "<div class='item-meta collapse' id='".$uid."'>";
										echo wc_get_formatted_cart_item_data( $cart_item );
									echo "</div>";
								}
								
								echo "<p>" . wc_price($_product->get_price()) . " × " . $cart_item['quantity'] . " = " . apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) . "</p>";
							?>
						</td>
					</tr>
					<?php
				}
			}

			do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal border-top-dashed">
			<th><?php _e( 'Subtotal', 'woocommerce' ); ?></th>
			<td colspan="4"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td colspan="4"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php
			if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ){
			
				do_action( 'woocommerce_review_order_before_shipping' );
				
				wc_cart_totals_shipping_html();
				
				do_action( 'woocommerce_review_order_after_shipping' );

			}
		?>
		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td colspan="4"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>
		
		<tr class="cart-subtotal-shipping border-top">
			<th><?php _e( 'Subtotal with shipping', 'b4st' ); ?></th>
			<td colspan="4"><?php echo wc_price(WC()->cart->cart_contents_total + WC()->cart->shipping_total); ?></td>
		</tr>

		<?php if ( wc_tax_enabled() && 'excl' === WC()->cart->tax_display_cart ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td colspan="4"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td colspan="4"><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php _e( 'Total', 'woocommerce' ); ?></th>
			<td colspan="4"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>
