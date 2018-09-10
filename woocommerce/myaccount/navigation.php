<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );


global $wp;

$current_endpoint = WC()->query->get_current_endpoint();
$possible_endpoints = array_merge(array($current_endpoint), array_keys($wp->query_vars));

?>

<nav class="woocommerce-MyAccount-navigation navbar navbar-dark bg-primary">
	<ul class="nav navbar-nav">
		<li class="nav-item">
			<a class="nav-link" href="/"><?php echo __("Back to the shop", "b4st");?></a>
		</li>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="nav-item <?php echo wc_get_account_menu_item_classes( $endpoint ); ?>" data-endpoint="<?php echo $endpoint;?>">
				<a class="nav-link <?php echo ($current_endpoint == $endpoint || in_array($endpoint, $possible_endpoints) || ($endpoint === 'dashboard' && $current_endpoint == '' && in_array("page", $possible_endpoints))) ? 'active' : '';?>" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
