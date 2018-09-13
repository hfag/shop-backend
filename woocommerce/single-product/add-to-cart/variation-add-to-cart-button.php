<?php
/**
 * Single variation cart button
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$bulk_discount		= $product->is_on_sale() ? array() : feuerschutz_get_discount_coeffs($product);
$reseller			= feuerschutz_get_reseller_discount_by_user();
$reseller_discount	= isset($reseller[$product->id]) ? $reseller[$product->id] : 0;

?>

<div class='form-title'><?php echo __("Price", 'woocommerce');?></div>

<?php
	
	/*if($reseller_discount != 0 && feuerschutz_bulk_discount_enabled($product)){
		echo '<div class="alert alert-info" role="alert">';
			echo '<strong>' . __("Heads up!", 'b4st') . '</strong> ';
			echo __("Bulk discount doesn't apply for you because you've already reseller discount on this product!", 'b4st');
		echo '</div>';
	}*/
	
?>

<ul class="bill" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
	<li class="product">
		<span class="quantity" data-update-name="quantity">1</span>
		<span class="price" data-update="price-per-piece"><?php echo count($product->get_available_variations()) == 1 ? $product->get_price_html() : '–'; ?></span>
	</li>
	<li class="sum"><span class="price" data-update="price-sum"></span></li>
	<li class="taxes"><span class="price"><?php echo __("taxes and shipping included", 'b4st');?></span></li>
</ul>

<meta itemprop="price" data-coeffs="<?php echo htmlspecialchars(json_encode($bulk_discount), ENT_QUOTES, 'UTF-8'); ?>" data-reseller="<?php echo $reseller_discount;?>" content="<?php echo /*esc_attr( $product->get_price() )*/'0'; ?>">
<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>">
<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>">

<!-- BTW: yes cou can change many things here but it'll be calculated serverside again so don't even bother ;) -->

<button type="submit" class="single_add_to_cart_button button alt"><i class="fa fa-cart-plus"></i><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->id ); ?>" >
<input type="hidden" name="product_id" value="<?php echo absint( $product->id ); ?>" >
<input type="hidden" name="variation_id" class="variation_id" value="0" >