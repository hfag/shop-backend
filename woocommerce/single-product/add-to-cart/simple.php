<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

$bulk_discount		= feuerschutz_get_discount_coeffs($product);
$reseller			= feuerschutz_get_reseller_discount_by_user();
$reseller_discount	= isset($reseller[$product->id]) ? $reseller[$product->id] : 0;

?>

<?php
	// Availability
	$availability      = $product->get_availability();
	$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

	echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
?>

<?php if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" method="post" enctype='multipart/form-data'>
		
		<div class="variations row">
			<?php if ( ! $product->is_sold_individually() ) : ?>
	        
		        <div class="quantity col-xs-12 col-md-6 col-lg-4">
			        
					<?php
						echo "<div class='form-label'><label>" . __("Quantity", 'woocommerce') . "</label></div>";
						
						$min_value = apply_filters('woocommerce_quantity_input_min', 1, $product );
						$max_value = apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product);
						
						$max_val = empty($max_value) ? 999 : $max_value;
						
						echo "<select class='hide-md-up' name='quantity-mobile'>";
							for($i=$min_value;$i<=$max_val;$i++){
								echo "<option>" . $i . "</option>";
							}
						echo "</select>";
						
						echo "<div class='hide-xs-down'>";
						
							do_action( 'woocommerce_before_add_to_cart_quantity' );
							
							woocommerce_quantity_input( array(
								'min_value'   => $min_value,
								'max_value'   => $max_value,
								'input_value' => ( isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 )
							));
							
							do_action( 'woocommerce_after_add_to_cart_quantity' );
							
						echo "</div>";
			 			
						if($min_value > 1){
							echo '<div class="alert alert-info" role="alert">';
								echo '<button type="button" class="close" data-dismiss="alert" aria-label="'.__("Close", 'b4st').'">';
									echo '<span aria-hidden="true">&times;</span>';
								echo '</button>';
								
								printf(__("<strong>Heads up!</strong> This product has a minimum purchase quantity of %d.", 'b4st'), $min_value);
							echo '</div>';
						}
						
					?>
					
				</div>
			
			<?php endif; ?>
		</div>
		
		<?php feuerschutz_horizontal_line(); ?>
		
		<div class="row">
		
			<?php
				
				$message = get_field('message'); //Optional messages to display
				if(!empty($message)){
					echo '<div class="message col-xs-12 col-md-6 col-lg-4">';
						echo "<div class='form-title'><i class='fa fa-warning'></i> " . $message->post_title . "</div>";
						echo $message->post_content;
					echo '</div>';
				}
				
				
			?>
			
			<div class="summary product_meta col-xs-12 col-md-6 col-lg-4">
				
				<?php echo "<div class='form-title'>" . __("Summary", 'b4st') . "</div>"; ?>
				
				<table class="summary not-default">
					<tbody>
						<?php
							
							$cat_count = sizeof( get_the_terms( $product->ID, 'product_cat' ) );
							$tag_count = sizeof( get_the_terms( $product->ID, 'product_tag' ) );
							
							if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ){
								
								echo '<tr class="sku_wrapper">';
									echo '<td>' . __( 'SKU:', 'woocommerce' ) . '</td>';
									
									echo '<td class="sku" itemprop="sku">';
										echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' );
									echo '</td>';
								echo '</tr>';
								
							}
							
							echo '<tr class="posted_in">';
								echo '<td>' . _n( 'Category:', 'Categories:', $cat_count, 'woocommerce' ) . '</td>';
								echo $product->get_categories( ', ', '<td>', '</td>' );
							echo '</tr>';
							
							echo '<tr class="tagged_as">';
								echo '<td>' . _n( 'Tag:', 'tags:', $tag_count, 'woocommerce' ) . '</td>';
								echo $product->get_tags( ', ', '<td>', '</td>' );
							echo '</tr>';
							
							
							echo '<tr><td>'.__("Product", 'woocommerce'). ':</td><td>'.get_the_title().'</td></tr>';
						?>
					</tbody>
				</table>
			</div>
			
			<?php
				
				echo '<div class="summary product_meta col-xs-12 col-md-6 col-lg-4">';
					do_action( 'woocommerce_before_add_to_cart_button' );
				echo '</div>';
			
				feuerschutz_bulk_discount_echo_table($product);
				
			?>
			
			<div class="woocommerce-variation-add-to-cart variations_button col-xs-12 col-md-6 col-lg-4">
				
				<ul class="bill" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<li class="product">
						<span class="quantity" data-update-name="quantity"></span>
						<span class="price" data-update="price-per-piece"><?php echo $product->get_price_html(); ?></span>
					</li>
					<li class="sum"><span class="price" data-update="price-sum"></span></li>
					<li class="taxes"><span class="price"><?php echo __("taxes and shipping included", 'b4st');?></span></li>
					
					<meta itemprop="price" data-coeffs="<?php echo htmlspecialchars(json_encode($bulk_discount), ENT_QUOTES, 'UTF-8'); ?>" data-reseller="<?php echo $reseller_discount;?>" content="<?php echo esc_attr( $product->get_price() ); ?>">
					<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
					<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />
					
				</ul>
				
				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />
				
				<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
				
			</div>
			
			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
			
		</div>
		
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
