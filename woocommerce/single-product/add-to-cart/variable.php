<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$attribute_keys = array_keys( $attributes );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->id ); ?>" data-product_variations="<?php echo htmlspecialchars( json_encode( $available_variations ) ) ?>">
	
	<?php do_action( 'woocommerce_before_variations_form' ); ?>
	
	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
	
		<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
		
	<?php else : ?>
	
		<div class="variations row">
			
			<?php foreach ( $attributes as $attribute_name => $options ) :
				
				$classes = "";
				$display = (count($options) != 1);
				
				if(!$display){
					$classes = 'hidden-xs-up';
				}
				
				?>
				
				<div class="variation col-xs-12 col-md-6 col-lg-4 <?php echo $classes;?>">
					
					<div class="form-label"><label for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label></div>
					
					<div class="value">
						
						<?php
							
							$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
							
							if(!$display){
								$selected = $options[0];	
							}
							
							echo "<div class='styled-select margin-bottom'>";
				
								wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
								
							echo "</div>";
							
						?>
						
					</div>
					
				</div>
				
	        <?php endforeach;?>
	        
	        <?php if ( ! $product->is_sold_individually() ) : ?>
	        
		        <div class="quantity col-xs-12 col-md-6 col-lg-4">
			        
					<?php echo "<div class='form-label'><label>" . __("Quantity", 'woocommerce') . "</label></div>"; ?>
					
					
						<?php
							
							$min_value = apply_filters('woocommerce_quantity_input_min', 1, $product );
							$max_value = apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product);
							
							$max_val = empty($max_value) ? 100 : $max_value;
							
							/*echo "<div class='hidden-md-up styled-select'>";
								echo "<select name='quantity-mobile'>";
									for($i=$min_value;$i<=$max_val;$i++){
										echo "<option value='" . $i . "'>" . $i . "</option>";
									}
								echo "</select>";
							echo "</div>";*/
							
							echo "<div class=''>";//hidden-xs-down
							
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
			
			<div class="reset col-xs-12 col-md-6 col-lg-4">
				
				<?php echo "<div class='form-title'>" . __("Reset", 'woocommerce') . "</div>"; ?>
						
				<?php echo apply_filters( 'woocommerce_reset_variations_link', '<button class="reset_variations_custom btn btn-secondary">' . __( 'Clear', 'woocommerce' ) . '</button>' ); ?>
				
			</div>
			
		</div><!-- .row -->
	
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
							
							/*echo '<tr class="tagged_as">';
								echo '<td>' . _n( 'Tag:', 'tags:', $tag_count, 'woocommerce' ) . '</td>';
								echo $product->get_tags( ', ', '<td>', '</td>' );
							echo '</tr>';*/
							
							
							echo '<tr><td>'.__("Product", 'woocommerce'). ':</td><td>'.get_the_title().'</td></tr>';
							
							foreach($product->product_attributes as $key => $attribute){
								if($attribute['is_variation'] == true){
									if($attribute['is_taxonomy'] == true){
										//we need to get the name from the db
										$tax = get_taxonomy($attribute['name']);
										
										echo '<tr><td>'.$tax->labels->name.':</td><td data-update-name="attribute_'.$key.'"></td></tr>';
									}else{
										echo '<tr><td>'.$attribute['name'].':</td><td data-update-name="attribute_'.$key.'"></td></tr>';
									}
								}else{
									if($attribute['is_taxonomy'] == true){
										//we need to get the name from the db
										$tax = get_taxonomy($attribute['name']);
										echo '<tr><td>'.$tax->labels->name.':</td><td>'.$product->get_attribute( $attribute['name'] ).'</td></tr>';
									}else{
										echo '<tr><td>'.$attribute['name'].':</td><td>'.$product->get_attribute( $attribute['name'] ).'</td></tr>';
									}
								}
							}
						?>
					</tbody>
				</table>
			</div>
			
			<?php
				
				ob_start();
				
				do_action( 'woocommerce_before_add_to_cart_button' );
				
				$output = trim(ob_get_clean());
				ob_end_flush();
				
				if(!empty($output) && strpos($output, "<input") !== false){ //check for an html input tag
					echo '<div class="summary product_meta col-xs-12 col-md-6 col-lg-4">';
						echo $output;
					echo '</div>';
				}else{
					echo $output;
				}
				
				feuerschutz_bulk_discount_echo_table($product);
				
			?>
			
			<div class="woocommerce-variation-add-to-cart variations_button col-xs-12 col-md-6 col-lg-4">
				<?php
					
					/**
					 * woocommerce_before_single_variation Hook.
					 */
					do_action( 'woocommerce_before_single_variation' );
					
					/**
					 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
					 * @since 2.4.0
					 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
					 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
					 */
					do_action( 'woocommerce_single_variation' );
		
					/**
					 * woocommerce_after_single_variation Hook.
					 */
					do_action( 'woocommerce_after_single_variation' );
				?>
			</div>    
			
			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
			
		</div>


	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
	
</form>
<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>