<?php
/**
 * Pagination - Show numbered pagination for catalog pages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/pagination.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
	return;
}
?>
<div class="ajax-archive-spinner sk-folding-cube">
	<div class="sk-cube1 sk-cube"></div>
	<div class="sk-cube2 sk-cube"></div>
	<div class="sk-cube4 sk-cube"></div>
	<div class="sk-cube3 sk-cube"></div>
</div>
<nav class="woocommerce-pagination">
	<?php
		//b4st_pagination();
	?>
</nav>
