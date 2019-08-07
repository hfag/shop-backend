<?php

/*add_action('wp_footer', function(){
	echo "<h1>∆TIME: " . (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) . "</h1>";
});*/


/*add_action( 'wp_footer', function(){
	if(current_user_can('manage_options')){
		global $wp_filter;
		var_dump( $wp_filter['wp_loaded'] );
	}
} );*/

require_once locate_template('/functions/setup.php');
require_once locate_template('/functions/react.php');
require_once locate_template('/functions/admin-menu.php');
require_once locate_template('/functions/search.php');

require_once locate_template('/functions/woocommerce-invoice-gateway.php');
require_once locate_template('/functions/woocommerce.php');
require_once locate_template('/functions/woocommerce-discount.php');

require_once locate_template('/functions/enqueues.php');
require_once locate_template('/functions/shortcodes.php');
require_once locate_template('/functions/email.php');
require_once locate_template('/functions/rest.php');
require_once locate_template('/functions/sitemap.php');

class Hfag {
	public function __construct(){
		add_action('after_switch_theme', array($this, 'activation'), 0);
		add_action('switch_theme', array($this, 'deactivation'), 0);
		
		//Change wpmu behaviour
		add_filter('wpmu_welcome_user_notification', '__return_false');
		
		//make the switch to the new url easier and hopefully seamless
		add_filter('post_type_link', function($permalink, $post){
			return str_replace("api.feuerschutz", "shop.feuerschutz", $permalink);
		}, 10, 2);
		
		//Change mail sender
		add_filter('wp_mail_from', function($from){
			return "info@feuerschutz.ch";
		}, 100);
		
		add_filter('wp_mail_from_name', function($from_name){
			return 'Hauser Feuerschutz AG';
		}, 100);		
	}
	
	public function activation(){
		flush_rewrite_rules();
	}
	
	public function deactivation(){
		flush_rewrite_rules();
	}
	
}

new Hfag();

?>