<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<table class="shop_table shop_table_responsive cart full-collapse" cellspacing="0">
	<thead>
		<tr>
			<th class="product-thumbnail">&nbsp;</th>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-sku"><?php _e( 'SKU', 'woocommerce' ); ?></th>
			<th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
			<th class="product-remove">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			
			$notices = array();

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<td class="product-thumbnail mobile-collapse">
						<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $_product->is_visible() ) {
								echo $thumbnail;
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $thumbnail );
							}
						?>
					</td>

					<td class="product-name" data-title="<?php _e( 'Product', 'woocommerce' ); ?>">
						<?php
							echo "<div class='mobile-collapse-toggle'>";
								if ( ! $_product->is_visible() ) {
									echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
								} else {
									echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $_product->get_title() ), $cart_item, $cart_item_key );
								}
								
							echo "</div>";
							
							$uid = "itemMeta".bin2hex(random_bytes(10));

							// Meta data
							if(!empty(WC()->cart->get_item_data( $cart_item ))){
								echo "<a class='mobile-collapse' data-toggle='collapse' href='#".$uid."' aria-expanded='false' aria-controls='".$uid."'><i class='fa fa-info-circle'></i> ".__("Show details", 'b4st')."</a>";
								echo "<div class='item-meta collapse' id='".$uid."'>";
									echo WC()->cart->get_item_data( $cart_item );
								echo "</div>";
							}

							// Backorder notification
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
							}
						?>
					</td>
					
					<td class="product-sku mobile-collapse">
						<?php
							echo "<span class='hidden-sm-up'>".__("SKU", 'woocommerce').": </span>" . $_product->get_sku();	
						?>
					</td>

					<td class="product-price mobile-collapse" data-title="<?php _e( 'Price', 'woocommerce' ); ?>">
						<span class="hidden-sm-up"><?php echo __("Price", 'woocommerce') . ": ";?></span>
						<?php
							
							if(feuerschutz_reseller_discount_enabled($_product) || feuerschutz_bulk_discount_get_price($_product)){
								//get orig price
								
								$_pr = "";
								
								if(is_a($_product, "WC_Product_Variation")){
									
									$_p			= new WC_Product_Variation($_product->variation_id);
									$_pr		= $_p->get_price();
									
								}else{
									
									$_p			= wc_get_product($_product->id );
									$_pr		= $_p->get_price();
									
								}
								
								if(!empty($_pr) && $_product->get_price() != $_pr){
									echo "<del>" . wc_price($_pr) . "</del><br>";
								}
							}
							
							echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
					</td>

					<td class="product-quantity mobile-collapse" data-title="<?php _e( 'Quantity', 'woocommerce' ); ?>">
						<span class="hidden-sm-up"><?php echo __("Quantity", 'woocommerce') . ": ";?></span>
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								
								$min_value = apply_filters( 'woocommerce_quantity_input_min', 0, isset($_product->parent) ? $_product->parent : $_product );
								
								if($cart_item['quantity'] < $min_value){
									$notices[] =
									'<div class="alert alert-warning" role="alert">'.
										'<button type="button" class="close" data-dismiss="alert" aria-label="'.__("Close", 'b4st').'">'.
											'<span aria-hidden="true">&times;</span>'.
										'</button>'.
										sprintf(__("<strong>Warning!</strong> The quantity of the product above has been updated to %d as this is the minimum purchase quantity for this product! (You tried to order %d).", 'b4st'), $min_value, $cart_item['quantity']).
									'</div>';
									
									WC()->cart->set_quantity($cart_item_key, $min_value, true);
									$cart_item['quantity'] = $min_value;
								}
								
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => $min_value
								), $_product, false );
								
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
						?>
					</td>

					<td class="product-subtotal mobile-collapse" data-title="<?php _e( 'Total', 'woocommerce' ); ?>">
						<span class="hidden-sm-up"><?php echo __("Subtotal", 'woocommerce') . ": ";?></span>
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</td>
					
					<td class="product-remove mobile-collapse">
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
								'<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s"><span class="hint--top hint--warning hint--negative" aria-label="' . __('Remove this item', 'woocommerce') . '"><i class="fa fa-trash-o"></i></span></a>',
								esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
								/*__( 'Remove this item', 'woocommerce' )*/'',
								esc_attr( $product_id ),
								esc_attr( $_product->get_sku() )
							), $cart_item_key );
						?>
						<span class="hidden-sm-up"><?php echo "(" . __("Remove product from cart", 'b4st') . ")";?></span>
					</td>
					
				</tr>
				<?php
					foreach($notices as $notice){
						echo "<tr><td colspan='7'>" . $notice . "</td></tr>";
					}
			}
		}

		do_action( 'woocommerce_cart_contents' );
		?>
		<tr class="actions">
			<td colspan="7" class="actions">

				<?php if ( wc_coupons_enabled() ) { ?>
					<div class="coupon">

						<label for="coupon_code"><?php _e( 'Coupon', 'woocommerce' ); ?>:</label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply Coupon', 'woocommerce' ); ?>" />

						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php } ?>

				<input type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>" />

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
			</td>
		</tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</tbody>
</table>

<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>


<div class="cart-collaterals">

	<?php do_action( 'woocommerce_cart_collaterals' ); ?>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
