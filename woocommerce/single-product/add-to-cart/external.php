<?php
/**
 * External product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/external.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>

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
		
		<table class="summary unstyled">
			<tbody>
				<?php
					
					$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
					$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
					
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
	
	<div class="woocommerce-variation-add-to-cart variations_button col-xs-12 col-md-6 col-lg-4">
		
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
		
		<ul class="bill" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<li class="product">
				<span class="quantity" data-update-name="quantity">1</span>
				<span class="price" data-update="price-per-piece"><?php echo $product->get_price_html(); ?></span>
			</li>
		</ul>
		
		<a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow" class="single_add_to_cart_button button alt btn btn-secondary"><?php echo esc_html( $button_text ); ?></a>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		
	</div>
</div>
