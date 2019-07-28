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

require_once locate_template('/functions/enqueues.php');
require_once locate_template('/functions/rest.php');

add_action(		'admin_menu',								'feuerschutz_admin_menu',								20			);
add_action(		'after_setup_theme',						'feuerschutz_after_setup_theme',						20			);
add_action(		'init',										'feuerschutz_init',										0			);
add_action(		'init',										'feuerschutz_late_init',								100			);//late, wait for woocommerce to init it's taxonomies
add_action(		'admin_head',								'feuerschutz_fix_svg_thumb_display',					10			);

add_action(		'after_switch_theme',						'feuerschutz_activation',								0			);
add_action(		'switch_theme',								'feuerschutz_deactivation',								0			);

add_filter(		'query_vars',								'feuerschutz_query_vars',								0, 		1	);

/* Change Woocommerce layout */
add_filter(		'woocommerce_enqueue_styles',				'__return_empty_array',									20			);

/* Change Woocommerce backend layout */
add_filter(		'manage_edit-product_cat_columns',			'feuerschutz_fix_woocommerce_description_columns',		100,	1	);


/* Change woocommerce behaviour */
add_filter(		'woocommerce_cart_shipping_method_full_label','feuerschutz_cart_shipping_method_full_label',		100			);

add_filter(		'woocommerce_order_formatted_billing_address', 'feuerschutz_order_formatted_billing_address',		100,	2	);
add_filter(		'woocommerce_order_formatted_shipping_address', 'feuerschutz_order_formatted_shipping_address',		100,	2	);

add_filter(		'woocommerce_localisation_address_formats', 'feuerschutz_localisation_address_formats',				100,	1	);

add_filter(		'woocommerce_formatted_address_force_country_display', '__return_true'											);
add_filter(		'woocommerce_formatted_address_replacements','feuerschutz_formatted_address_replacements',			100,	2	);

add_filter(		'woocommerce_order_shipping_to_display_tax_label', 'feuerschutz_order_shipping_to_display_tax_label',100,	0	);
add_filter(		'woocommerce_order_shipping_to_display_shipped_via', 'feuerschutz_order_shipping_to_display_shipped_via', 100, 0);
add_filter(		'woocommerce_get_order_item_totals',		'feuerschutz_get_order_item_totals',					100,	2	);


add_filter(		'woocommerce_billing_fields' ,				'feuerschutz_billing_fields',							100,	1	);
add_filter(		'woocommerce_shipping_fields' ,				'feuerschutz_shipping_fields',							100,	1	);

add_action(		'woocommerce_checkout_process',				'feuerschutz_checkout_process',							100,	1	);

add_filter(		'woocommerce_payment_gateways',				'feuerschutz_payment_gateways',							20			);

add_filter(		'woocommerce_thankyou_order_received_text',	'feuerschutz_woocommerce_thankyou_order_received_text',	100,	2	);

add_action(		'woocommerce_email',						'feuerschutz_wc_mails',									100,	1	);

add_action(		'woocommerce_thankyou',						'feuerschutz_thankyou',									100,	1	);

add_action(		'woocommerce_quantity_input_min',			'feuerschutz_quantity_input_min',						10,		2	);


add_action(		'woocommerce_checkout_update_order_meta',	'feuerschutz_checkout_update_order_meta',				10,		1	);

add_filter(		'woocommerce_email_headers',				'feuerschutz_woocommerce_email_headers',				1000,	3	);
add_filter(		'woocommerce_email_subject_new_order',		'feuerschutz_woocommerce_email_subject_new_order',		1,		2	);

/* Adjust price by applying discount */
add_action(		'woocommerce_before_calculate_totals',		'feuerschutz_before_calculate_totals',					10,		1	);

/* Add custom fields */
add_action(		'woocommerce_product_options_general_product_data', 'feuerschutz_product_options_general_product_data', 10		);
add_action(		'woocommerce_process_product_meta',			'feuerschutz_process_product_meta',						10			);

add_action(		'woocommerce_product_after_variable_attributes', 'feuerschutz_product_after_variable_attributes',	10,		3	);
add_action(		'woocommerce_save_product_variation',		'feuerschutz_save_product_variation',					10,		1	);

/* Shortcodes */
remove_shortcode(	'gallery'																									);
add_shortcode(		'gallery',								'feuerschutz_photoswipe_gallery_shortcode_func'						);

//Reduce min password strength because clients are ... well "smart"
//add_filter( 'woocommerce_min_password_strength', function(){return 3;/*range: 0-3*/});

add_action('after_password_reset', function(){
	wp_redirect("https://shop.feuerschutz.ch"); 
    exit;
});

add_filter( 'lostpassword_redirect', function ($lostpassword_redirect){
	return "https://shop.feuerschutz.ch";
});

add_filter('post_type_link',function($permalink, $post){
	return str_replace("api.feuerschutz", "shop.feuerschutz", $permalink);
}, 10, 2);

//Ajax

	//Import products
	add_action(	'wp_ajax_import_product',					'feuerschutz_ajax_import_product' );
	add_action(	'wp_ajax_import_reseller',					'feuerschutz_ajax_import_reseller' );
	
	//Refresh taxonomy counts
	add_action(	'wp_ajax_refresh_taxonomy_count',			'feuerschutz_ajax_refresh_taxonomy_count' );
	
	//Refresh variable min/max
	add_action(	'wp_ajax_refresh_variable_product_min_max', 'feuerschutz_ajax_refresh_variable_product_min_max' );



//Always apply filters
add_filter(		'woocommerce_is_layered_nav_active',		'__return_true',										100			);
add_filter(		'woocommerce_is_price_filter_active',		'__return_true',										100			);

//Change mail sender
add_filter(		'wp_mail_from',								'feuerschutz_mail_from',								100			);
add_filter(		'wp_mail_from_name',						'feuerschutz_mail_from_name',							100			);

function hfag_index_relevanssi($content, $post){
	/*if ($post->post_type == "product"){
		$args = array('post_parent' => $post->ID, 'post_type' => 'product_variation', 'posts_per_page' => -1);
		$variations = get_posts($args);
		if (!empty($variations)) {
			foreach ($variations as $variation) {
				$sku = get_post_meta($variation->ID, '_sku', true);
				$content .= " $sku";
			}
		}
	}else */if($post->post_type == "product_variation"){
		$parent = get_post($post->post_parent);
		$content .= " {$parent->post_content}";
	}
 
	return $content;
}
add_filter('relevanssi_content_to_index', 'hfag_index_relevanssi', 10, 2);

//Change wp default behaviour
add_filter(		'wpmu_welcome_user_notification',			'__return_false'													);

function feuerschutz_activation(){	
	flush_rewrite_rules();
}


function feuerschutz_deactivation(){
	flush_rewrite_rules();
}

function feuerschutz_query_vars($vars){
	$vars[] = 'discount';
    return $vars;
}

/**
 * feuerschutz_init function.
 *
 * @access public
 * @return void
 */
function feuerschutz_init() {

	global $pagenow;

	if('wp-login.php' == $pagenow && !isset($_GET["action"]) && $_GET["action"] !== "rp"){
		wp_redirect('https://shop.feuerschutz.ch/login');
		exit();
	}

	load_theme_textdomain('b4st', get_template_directory() . '/lang/');
	
	add_theme_support('post-thumbnails');
	
	remove_image_size("search-thumbnail");
	remove_image_size("woocommerce_thumbnail");
	remove_image_size("woocommerce_single");
	remove_image_size("woocommerce_gallery_thumbnail");
	remove_image_size("shop_catalog");
	remove_image_size("shop_single");
	remove_image_size("shop_thumbnail");
	
	
	$labels = array(
		'name'                       => _x( 'Discount Groups', 'Taxonomy General Name', 'b4st' ),
		'singular_name'              => _x( 'Discount Group', 'Taxonomy Singular Name', 'b4st' ),
		'menu_name'                  => __( 'Discount Groups', 'b4st' ),
		'all_items'                  => __( 'All Groups', 'b4st' ),
		'parent_item'                => __( 'Parent Group', 'b4st' ),
		'parent_item_colon'          => __( 'Parent Group:', 'b4st' ),
		'new_item_name'              => __( 'New Group Name', 'b4st' ),
		'add_new_item'               => __( 'Add New Group', 'b4st' ),
		'edit_item'                  => __( 'Edit Group', 'b4st' ),
		'update_item'                => __( 'Update Group', 'b4st' ),
		'view_item'                  => __( 'View Group', 'b4st' ),
		'separate_items_with_commas' => __( 'Separate groups with commas', 'b4st' ),
		'add_or_remove_items'        => __( 'Add or remove groups', 'b4st' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'b4st' ),
		'popular_items'              => __( 'Popular Groups', 'b4st' ),
		'search_items'               => __( 'Search Discount Groups', 'b4st' ),
		'not_found'                  => __( 'Not Found', 'b4st' ),
		'no_terms'                   => __( 'No Groups', 'b4st' ),
		'items_list'                 => __( 'Group list', 'b4st' ),
		'items_list_navigation'      => __( 'Groups list navigation', 'b4st' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
		'single_value'				 => false,
	);
	register_taxonomy( 'product_discount', array( 'product', 'product_variation' ), $args );
	register_taxonomy( 'user_discount', 'user', $args );
	
	// filter to remove TinyMCE emojis
	add_filter('tiny_mce_plugins', function( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	});

}

/**
 * feuerschutz_fix_svg_thumb_display function.
 *
 * @access public
 * @return void
 */
function feuerschutz_fix_svg_thumb_display(){
	$css =		'.media-icon img[src$=".svg"] { width: 100% !important; height: auto !important; }';
	$css .=		'img.thumbnail,.thumbnail img{max-width: 100% !important;max-height: 100% !important;}';
	echo '<style type="text/css">'.$css.'</style>';
}

function feuerschutz_fix_woocommerce_description_columns($columns){
	unset($columns['description']); //prevent wysiwyg content from displaying
	return $columns;
}

/**
 * feuerschutz_admin_menu function.
 *
 * @access public
 * @return void
 */
function feuerschutz_admin_menu() {
	//remove_menu_page('edit.php');
	remove_menu_page('edit-comments.php');

	add_submenu_page(
		'tools.php',
		__('Import Products from Excel','b4st'),
		__('Import Products from Excel','b4st'),
		'manage_options',
		'import-wc-products-from-excel',
		'feuerschutz_options_page_import_products'
	);
	add_submenu_page(
		'tools.php',
		__('Import Resellers from Excel','b4st'),
		__('Import Resellers from Excel','b4st'),
		'manage_options',
		'import-resellers-from-excel',
		'feuerschutz_options_page_import_resellers'
	);
	add_submenu_page(
		'tools.php',
		__('Refresh Taxonomy Count','b4st'),
		__('Refresh Taxonomy Count','b4st'),
		'manage_options',
		'refresh-taxonomy-count',
		'feuerschutz_options_page_refresh_taxonomy_count'
	);
	
	if( function_exists('acf_add_options_page') ) {

		acf_add_options_sub_page(array(
			'page_title' 	=> __("Global Attribute Settings", 'b4st'),
			'menu_title' 	=> __("Global Attribute Settings", 'b4st'),
			'parent_slug' 	=> 'options-general.php',
		));
		
		acf_add_options_sub_page(array(
			'page_title' 	=> __("Footer Settings", 'b4st'),
			'menu_title' 	=> __("Footer Settings", 'b4st'),
			'parent_slug' 	=> 'themes.php',
		));
		
		acf_add_options_sub_page(array(
			'page_title' 	=> __("Discount Settings", 'b4st'),
			'menu_title' 	=> __("Discount Settings", 'b4st'),
			'parent_slug' 	=> 'users.php',
		));
	
	}
	
	//Add custom urls
	global $submenu;
	
	//$submenu['edit.php?post_type=product'][] = array(__('Discount groups for variable products', 'b4st'), 'manage_options', 'edit-tags.php?taxonomy=product_discount&post_type=product_variation');
	
}

/* Woocommerce */

/**
 * feuerschutz_after_setup_theme function.
 *
 * @access public
 * @return void
 */
function feuerschutz_after_setup_theme(){
	add_theme_support( 'woocommerce' );
	add_image_size( 'search-thumbnail', 100, 100, true );
	
	
	class WC_Gateway_Feuerschutz_Invoice extends WC_Payment_Gateway{
		
		function __construct(){
			$this->id						= 'feuerschutz_invoice';
			//$this->icon						= get_template_directory_uri() . '/img/bill.svg';
			$this->has_fields				= false; //fields on the checkout screen
			$this->method_title				= __("Invoice", 'b4st');
			$this->method_description		= __("Just an ordinary invoice. That's it! Simple as that.", 'b4st');
			
			
			$this->init_form_fields();
			$this->init_settings();
			
			$this->title					= $this->get_option( 'title' );
			$this->enabled					= $this->get_option( 'enabled' );
			$this->message					= $this->get_option( 'message' );
			
			
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
		}
		
		public function payment_fields(){
			/*echo "<div class='feuerschutz invoice fields'>";
				echo "<label for='feuerschutz_invoice_message'>" . __("Message", 'b4st') . "</label>";
				echo "<textarea id='feuerschutz_invoice_message' name='feuerschutz_invoice_message'></textarea>";
			echo "</div>";*/
		}
		
		public function validate_fields(){
			
		}
		
		public function admin_options() {
			
			echo "<h3>"		. $this->method_title			. "</h3>";
			echo "<p>"		. $this->method_description		. "</p>";
			
			echo "<table class='form-table'>";
				$this->generate_settings_html();
			echo "</table>";
		}
		
		function init_form_fields(){
			$this->form_fields = array(
				'enabled'	=> array(
					'title'			=> __( 'Enable/Disable', 'b4st' ),
					'type'			=> 'checkbox',
					'label'			=> __( 'Enable Invoice Payment', 'b4st' ),
					'default'		=> 'no'
				),
				'title'		=> array(
					'title'			=> __( 'Title', 'b4st' ),
					'type'			=> 'text',
					'description'	=> __( 'This controls the title which the user sees during checkout.', 'b4st' ),
					'default'		=> __( 'Invoice', 'b4st' ),
					'desc_tip'		=> true
				),
				'message'	=> array(
					'title'			=> __( 'Message', 'b4st' ),
					'type'			=> 'textarea',
					'description'	=> __( 'A message show to users while checking out.', 'b4st' ),
					'default'		=> '',
					'desc_tip'		=> true
				)
			);
		}
		
		function process_payment( $order_id ){
			
			global $woocommerce;
			$order = new WC_Order( $order_id );
			
			// Reduce stock levels
			$order->reduce_order_stock();
		
			// Remove cart
			$woocommerce->cart->empty_cart();
			
			//Mark as complete
			$order->payment_complete();
		
			// Return thankyou redirect
			return array(
				'result'	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);
		}
	}

}
function feuerschutz_set_content_type_html($content_type){
	return 'text/html';
}

function feuerschutz_mail_from($from){
	$sitename = strtolower( $_SERVER['SERVER_NAME'] );
    if ( substr( $sitename, 0, 4 ) == 'www.' ) {
        $sitename = substr( $sitename, 4 );
    }
	return "NOREPLY@" . $sitename;
}

function feuerschutz_mail_from_name($from_name){
	return get_bloginfo('name');
}

/**
 * feuerschutz_payment_gateways function.
 * 
 * @access public
 * @param array $methods
 * @return void
 */
function feuerschutz_payment_gateways($methods){
	$methods[] = 'WC_Gateway_Feuerschutz_Invoice'; 
	return $methods;
}

function feuerschutz_woocommerce_thankyou_order_received_text($text, $order){
	return "<p>" . __("The order was successfully sent.", "b4st") . "</p>" . 
	"<p>" . __("Thank you! Your order is being processed. You will receive a confirmation mail within the next few minutes. You could now leave this site.", "b4st") . "</p>" . 
	"<p>" . sprintf(__("If you would like to return to the shop, click <a href='%s'>here</a>.", "b4st"), home_url('/')) . "</p>";
}

/**
 * feuerschutz_wc_mails function.
 * 
 * @access public
 * @return void
 */
function feuerschutz_wc_mails($email_class){
	
	/**
		* Hooks for sending emails during store events
	**/
	//remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
	//remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
	//remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );
	
	// New order emails
	//remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	//remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	//remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	//remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	//remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	//remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	
	remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
	
	// Processing order emails
	//remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
	//remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
	
	// Completed order emails
	remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
		
	// Note emails
	remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
}

function feuerschutz_thankyou($order_id){
	if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
}

function feuerschutz_cart_shipping_method_full_label($label){
	return explode(": ", $label)[1]; //strip everything but the price;
}

function feuerschutz_order_formatted_billing_address($address, $order){
	
	$address['additional_line_above']		= get_post_meta($order->id, '_billing_additional_line_above', true);
	$address['description']					= get_post_meta($order->id, '_billing_description', true);
	
	$box = get_post_meta($order->id, '_billing_post_office_box', true);
	$address['post_office_box']				= $box ? (__("Post office box", 'b4st') . ": " . $box) : '';
	
	return $address;
}

function feuerschutz_order_formatted_shipping_address($address, $order){
	
	$address['additional_line_above']		= get_post_meta($order->id, '_shipping_additional_line_above', true);
	$address['description']					= get_post_meta($order->id, '_shipping_description', true);
	
	$box = get_post_meta($order->id, '_shipping_post_office_box', true);
	$address['post_office_box']				= $box ? (__("Post office box", 'b4st') . ": " . $box) : '';
	
	return $address;
	
}


function feuerschutz_localisation_address_formats($formats){
	
	$formats[ 'CH' ]  = "{additional_line_above}\n{company}\n{name}\n{description}\n{address_1}\n{address_2}\n{post_office_box}\n{country}-{postcode}, {city}, {state}\n";
	
	return $formats;
}

function feuerschutz_formatted_address_replacements($replacements, $args){
	
	$replacements['{country}']						= $args['country']; /* country code instead of name */
	
	$replacements['{additional_line_above}']		= !empty($args['additional_line_above']) ? $args['additional_line_above'] : '';
	$replacements['{description}']					= !empty($args['description']) ? $args['description'] : '';
	$replacements['{post_office_box}']				= !empty($args['post_office_box']) ? $args['post_office_box'] : '';
	
	return $replacements;
}


/* Helpers */

function feuerschutz_get_discount_coeffs($product){
	//return array();
	if($product->is_type('simple')){
		$discount = removeBOM(get_post_meta($product->get_id(), '_feuerschutz_bulk_discount', true));
		
		if(empty($discount) || !isJson($discount)){
			return array();
		}
		
		$array = array();
		$array[$product->get_id()] = json_decode($discount);
		
		return $array;
		
	}else if($product->is_type('variable')){
		
		$discount = array();
		
		$variations = $product->get_available_variations();
		foreach($variations as $variation){
			$discount_tmp = removeBOM(get_post_meta($variation['variation_id'], '_feuerschutz_bulk_discount', true));
			
			if(empty($discount_tmp) || !isJson($discount_tmp)){
				$discount_tmp = "[]";
			}
				
			$discount[$variation['variation_id']] = (empty($discount_tmp) ? array() : json_decode($discount_tmp));
		}
		
		return $discount;
		
	}

}

function removeBOM($data) {
	if (0 === strpos(bin2hex($data), 'efbbbf')) {
		return substr($data, 3);
	}
	
	return $data;
}

function feuerschutz_starts_with($haystack, $needle){
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

function feuerschutz_ends_with($haystack, $needle){
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}
	
	return (substr($haystack, -$length) === $needle);
}

/**
 * feuerschutz_get_terms_args function.
 *
 * @access public
 * @param bool $hierarchical (default: true)
 * @param string $parent_id (default: '')
 * @return void
 */
function feuerschutz_get_terms_args($hierarchical = true, $parent_id = ''){
	return array(
		'orderby'		=> 'name',
		'order'			=> 'ASC',
		'hide_empty'	=> true,
		'parent'		=> $parent_id,
		'hierarchical'	=> $hierarchical,
		'childless'		=> false
	);
}

function feuerschutz_order_shipping_to_display_tax_label(){
	return '';
}
function feuerschutz_order_shipping_to_display_shipped_via(){
	return '';
}

function feuerschutz_get_order_item_totals($rows, $order){
	
	if(($subtotal = $order->get_subtotal_to_display(false)) && ($order->get_shipping_method())){
		
		$offset = array_search('shipping', array_keys($rows)) + 1;
		
		$matches = array();
		
		preg_match('/([0-9]+\.[0-9]+)/', $subtotal, $matches);
		
		$subtotal = floatval($matches[0]);
		
		$rows = array_merge(
			array_slice($rows, 0, $offset),
			array('cart_subtotal_taxes' => array(
				'label'		=> __( 'Subtotal with shipping:', 'b4st' ),
				'value'		=> wc_price(($subtotal + $order->order_shipping), array('currency' => $order->get_order_currency())),
				'classes'	=> 'border-top'
			)),
			array_slice($rows, $offset, null)
	    );
		
	}
	
	if(isset($rows['cart_subtotal'])){
		$rows['cart_subtotal']['classes'] = "border-top-dashed";
	}
	
	if(isset($rows['shipping'])){
		$rows['shipping']['classes'] = "spacer";
	}
	
	return $rows;
}


/*function feuerschutz_close_tax_desc_background(){
	echo "</div>";
}*/


function feuerschutz_photoswipe_gallery_shortcode_func($attr){
	$attributes = shortcode_atts( array(
		/*'link' => '',*/
		'ids' => '',
		/*'orderby' => '',*/
	), $attr );
	
	return feuerschutz_photoswipe_gallery(explode(",", $attributes['ids']));
	
}

function feuerschutz_photoswipe_gallery($attachment_ids){
	
	$return = "";
	
	if (count($attachment_ids) > 0 ) {
		
		$return .= "<div class='images-pswp'>";
			
			include(get_template_directory().'/html/photoswipe-template.php');
			
			$return .= "<div class='thumbnails row same-width'>";
			
				foreach($attachment_ids as $attachment_id){
					
					$link		= wp_get_attachment_url( $attachment_id );
					$caption	= get_the_title( $attachment_id );
					$meta		= wp_get_attachment_metadata($attachment_id);
					
					if(!isset($meta['height']) || !isset($meta['width'])){
						//probably an svg
						$svgfile = simplexml_load_file(get_attached_file($attachment_id));
						$xmlattributes = $svgfile->attributes();
						$viewbox = explode(" ", (string) $xmlattributes->viewBox);
						if(count($viewbox) == 4){
							$meta['width'] = 2000; //big number so it'll be all right on all displays
							$meta['height'] = ($viewbox[3] / $viewbox[2] * $meta['width']); //calc based on ratio given by the viewbox
						}else{
							//Welp don't know what to do sooooo
							$meta['height'] = $meta['width'] = 0;
						}
					}
					
					$return .= "<div class='".feuerschutz_get_small_cols()." image-preview'".
					
						" data-full-src='".$link."' data-title='".$caption."'".
						" data-width='".$meta['width']."' data-height='".$meta['height']."'".
						
					">";
					
						$return .= wp_get_attachment_image( $attachment_id, 'shop_single', false, array() );
						
					$return .= "</div>";
				}
			$return .= "</div>";
		$return .= "</div>";
		
	}
	
	return $return;
}

/* Import products page */

/**
 * feuerschutz_options_page_import_products function.
 *
 * @access public
 * @return void
 */
function feuerschutz_options_page_import_products(){
	echo "<div class='wrap'>";
		echo "<h1>" . __('Import Products from Excel','b4st') . "</h1>";

		//Sorry not sorry
		echo "<script src='" . get_template_directory_uri() . '/bower_components/js-xlsx/dist/xlsx.full.min.js' . "' type='text/javascript'></script>";

		echo "<div id='drop-excel' style='width:100%;border:2px #aaa dashed;margin-top:20px;padding:135px 0;text-align:center;font-size:25px;'>";
			echo __("Drop your excel file here", "b4st");
		echo "</div>";
		
		$options = array("max_execution_time", "max_input_time", "max_input_vars", "post_max_size", "upload_max_filesize");
		
		foreach($options as $option){
			echo "<p>".$option.": " . ini_get($option) . "</p>";
		}
		
		echo "<form action='/wp-admin/admin-ajax.php' method='POST' target='_blank'>";
			echo "<input type='hidden' name='action' value='refresh_variable_product_min_max'>";
			echo "<input type='submit' value='Refresh min/max'>";
		echo "</form>";

		echo "<div id='log-filename' style='padding-top:20px;'></div>";

		echo "<div id='log-ajax' style='padding-top:20px;'></div>";

		echo "<script src='" . get_template_directory_uri() . '/js/min/excel-json-product.min.js' . "' type='text/javascript'></script>";

	echo "</div>";
}

function feuerschutz_options_page_import_resellers(){
	echo "<div class='wrap'>";
		echo "<h1>" . __('Import Resellers from Excel','b4st') . "</h1>";

		//The dirty way because I don't want to create a big overhad
		echo "<script src='" . get_template_directory_uri() . '/bower_components/js-xlsx/dist/xlsx.full.min.js' . "' type='text/javascript'></script>";

		echo "<div id='drop-excel' style='width:100%;border:2px #aaa dashed;margin-top:20px;padding:135px 0;text-align:center;font-size:25px;'>";
			echo __("Drop your excel file here", "b4st");
		echo "</div>";
		
		$options = array("max_execution_time", "max_input_time", "max_input_vars", "post_max_size", "upload_max_filesize");
		
		foreach($options as $option){
			echo "<p>".$option.": " . ini_get($option) . "</p>";
		}

		echo "<div id='log-filename' style='padding-top:20px;'></div>";
		
		echo "<progress id='import-progress' min='0' max='100' value='0' style='width:100%;'></progress>";

		echo "<div id='log-ajax' style='padding-top:20px;'></div>";
		
		feuerschutz_import_reseller_discount(-1, array());

		echo "<script src='" . get_template_directory_uri() . '/js/min/excel-json-reseller.min.js' . "' type='text/javascript'></script>";

	echo "</div>";
}

function feuerschutz_options_page_refresh_taxonomy_count(){
	echo "<div class='wrap'>";
		echo "<h1>" . __('Refresh Taxonomy Count','b4st') . "</h1>";
		
		?>
		
		<script>
			function refresh_taxonomy_count(){
				jQuery("#refresh-button").hide();
				jQuery("#refresh-progress").show();
				jQuery.post(ajaxurl, {action: 'refresh_taxonomy_count', data: {}}, function(response){
					jQuery("#response").text(response.message);
					jQuery("#refresh-progress").attr("min", "0").attr("max", "1").attr("value", "1");
				});
			}
		</script>
		
		<?php
		
		echo "<button id='refresh-button' class='button button-secondary' onclick='refresh_taxonomy_count()'>" . __("Do It!", 'b4st') . "</button>";
		echo "<h4 id='response'></h4>";
		echo "<progress id='refresh-progress' style='width:100%;display:none;'></progress>";

	echo "</div>";
}

function feuerschutz_ajax_refresh_taxonomy_count(){
	
	header('Content-Type: application/json');
	
	set_time_limit(120); //Two minutes should be plenty of time
	
	$taxonomy_names = get_taxonomies();
	foreach($taxonomy_names as $taxonomy_name){
		$term_ids = get_terms( array(
		    'taxonomy'		=> $taxonomy_name,
		    'hide_empty'	=> false,
		    'fields'		=> 'ids'
		) );
		wp_update_term_count_now($term_ids, $taxonomy_name);
	}
	
	die(json_encode(array("message"=>__("Refreshed successfully!", "b4st"))));
}

function feuerschutz_ajax_refresh_variable_product_min_max(){
	$query = new WP_Query(array(
		"post_type" => "product",
		"posts_per_page" => -1
	));
	
	foreach($query->posts as $post){
		WC_Product_Variable::sync($post->ID);
	}
	
	die("Refreshed " . count($query->posts) . " products.");
}

function feuerschutz_bulk_discount_cpm_array($a, $b){
	return ($a['qty'] < $b['qty']) ? -1 : 1;
}

function feuerschutz_bulk_discount_cpm_obj($a, $b){
	return ($a->qty < $b->qty) ? -1 : 1;
}

/* Ajax Import Product */

/**
 * feuerschutz_ajax_import_product function.
 *
 * @access public
 * @return void
 */
function feuerschutz_ajax_import_product(){
	
	header('Content-Type: application/json');
	
	if(empty($_POST['product_data'])){die("invalid input");}
	
	$_POST = json_decode(stripslashes($_POST['product_data']), true);
	
	/*if(!empty($_POST['product_type']) && $_POST['product_type'] == 'variable'){
		die(json_encode(array("message"=>__("$" . "_POST: " . print_r($_POST, true), "b4st"), "errors"=>array())));
	}*/

	//but first check if 1) user is admin and 2) all needed params are set

	if ( current_user_can( 'manage_options' ) ) {
		
		$new = true;

		/**
			KEY						|	Required?	|	Possible values

			post_title					true			(string) $title
			post_content				true			(string) $content


		**/

		$post_id = $_POST['post_id'];

		if($post_id == false || $post_id == "false"){
			/* Product is new */
			
			/* WP_POST */
			$post = array(
				'post_author'						=> get_current_user_id(),
				'post_status'						=> "publish",
				'post_title'						=> strip_tags($_POST['post_title']),
				'post_parent'						=> "",
				'post_type'							=> "product"
			);

			$post_id = wp_insert_post( $post, true );
			
			if(is_wp_error($post_id)){
				//error occured, print it
				die(json_encode(array("message" => "ERROR: ".$post_id->get_error_message())));
			}
			
		}else{
			/* Product is being updated */
			
			/* WP_POST */
			
			//Don't override existing stuff
			
			/*$post = array(
				'ID'								=> $post_id,
				'post_author'						=> get_current_user_id(),
				'post_status'						=> "publish",
				'post_title'						=> htmlentities(strip_tags($_POST['post_title'])),
				'post_parent'						=> "",
				'post_type'							=> "product"
			);
			
			wp_update_post($post);*/
			
			$new = false;
		}
		
		//Update thumbnail id
		if(!empty($_POST['thumbnail_id']) && !(has_post_thumbnail($post_id) && get_post_status(get_post_thumbnail_id($post_id)) !== false )){
			set_post_thumbnail($post_id, $_POST['thumbnail_id']);
		}
		
		//sanitize category names
		
		$keys = array_keys($_POST);
		foreach($keys as $key){
			if(strpos($key, "attribute_") !== false){
				$sanitized_key = sanitize_title($key);
				if($sanitized_key != $key){
					$_POST[$sanitized_key] = $_POST[$key];
					unset($_POST[$key]);				
				}
				
				if(strpos($key, "attribute_pa_") !== false){
					foreach($_POST[$key] as $index => $attribute_name){
						$_POST[$key][$index] = sanitize_title($attribute_name);
					}
				}
			}
		}
		
		if(!empty($_POST['categories'])){
			
			$terms = array();
			
			foreach($_POST['categories'] as $category){
				
				$term = get_term_by('name', $category, 'product_cat');
				
				if($term === false){
					//Create one
					
					$term = wp_insert_term( trim( $category ), 'product_cat');
					if(!is_wp_error($term)){
						$terms[] = (int) $term->term_id;
					}
					
				}else{
					$terms[] = (int) $term->term_id;
				}
				
				$thumb = get_woocommerce_term_meta($term->term_id, 'thumbnail_id', true );
				if($thumb == null){
					//use current product image as thumbnail
					update_woocommerce_term_meta($term->term_id, 'thumbnail_id', $_POST['thumbnail_id']);
				}
			}
			
			wp_set_object_terms($post_id, $terms, 'product_cat', false /* Override existing ones */);
			wp_update_term_count_now($terms, 'product_cat');
		}
		
		if(!empty($_POST['_visibility'])){
			update_post_meta($post_id, '_visibility', $_POST['_visibility']); //sadly not done by wc functions called below
		}
		
		if(!empty($_POST['discounts'])){
			
			$terms = array();
			
			foreach($_POST['discounts'] as $discount_group){
				
				$term = get_term_by('name', $discount_group, 'product_discount');
				
				if($term === false){
					$term = wp_insert_term(trim($discount_group), 'product_discount');
					
					if(!is_wp_error($term)){
						$terms[] = (int) $term->term_id;
					}
				}else{
					$terms[] = (int) $term->term_id;
				}
			}
			
			wp_set_object_terms($post_id, $terms, 'product_discount', false /* Override */);
			wp_update_term_count_now($terms, 'product_discount');
		}
		
		if(!empty($_POST['min_purchase_qty'])){
			if($_POST['min_purchase_qty'] > 1){
				update_post_meta($post_id, '_feuerschutz_min_purchase_qty', intval($_POST['min_purchase_qty']));
			}
		}
		
		//bulk discount is a little harder :/
		$import_fields = array(
			'bulk_discount' => function($input, $post_id, $parent_id = 0){
				
				if(is_array($input)){
					foreach($input as $key => $discount){
						if(
							( empty($discount['qty']) || !is_numeric($discount['qty']) || $discount['qty'] < 2 ) ||
							( empty($discount['ppu']) || !is_numeric($discount['ppu']) )
						){
							return false;
						}else{
							$input[$key]['qty'] = intval($input[$key]['qty']);
							$input[$key]['ppu'] = floatval($input[$key]['ppu']);
						}
					}
					
					usort($input, "feuerschutz_bulk_discount_cpm_array");
					
					update_post_meta($post_id, '_feuerschutz_bulk_discount', json_encode($input));
					
					if($parent_id !== 0){
						update_post_meta($parent_id, '_feuerschutz_variable_bulk_discount_enabled', '1');
					}
					
					return true;
					
				}
				
				return false;
			}
		);
		
		foreach($import_fields as $import_field => $validate_and_store){
			
			if( !empty($_POST[$import_field]) ){
				$validate_and_store($_POST[$import_field], $post_id);
			}
			
			if(!empty($_POST['variable_' . $import_field])){
				
				foreach($_POST['variable_' . $import_field] as $variation_id => $import_field_value){
					
					$validate_and_store($import_field_value, $_POST['variable_post_id'][$variation_id], $post_id);
					
				}
			}
			
		}
		

		/**
			KEY						|	Required?	|	Possible values

			product-type				true			{'simple', 'grouped', 'variable', 'external'}

			_downloadable				false			-
			_virtual					false			-

			_tax_status					false			{'taxable'}
			_tax_class					false			{'reduced-rate', 'zero-rate', [custom]}

			_purchase_note				false			(string) $note

			_featured					false			-

			_weight						false			{ (int) $weight,	(string) '' }
			_length						false			{ (int) $length,	(string) '' }
			_width						false			{ (int) $width,		(string) '' }
			_height						false			{ (int) $height,	(string) '' }

			product_shipping_class		false			{ (int) $product_shipping_class_id, (int) -1 }

			_sku						true			(string) *unique* $sku

			attribute_names				false			array(
															0 => ATTR1_NAME,
															1 => ATTR1_NAME,
															2 => ATTR1_NAME,
															3 => ATTR1_NAME
															...
															X => $taxonomy_name_Y
														) (x Elements)

			attribute_values			false			array(

															4 => (string) "ATTR1_TERM | ATTR1_TERM | ATTR1_TERM"

															x => (array) array(
																"ATTR1_SLUG",
																"ATTR2_SLUG",
																"ATTR3_SLUG"
															)

														) ([0-x] Elements)

			attribute_visibility		false			array(
															[0-x *]
														) ([0-x] Elements)
			attribute_variation			false			array(
															[0-x *]
														) ([0-x] Elements)

			attribute_is_taxonomy		false			array(
															0 => true/false
															1 => true/false
															2 => true/false
															3 => true/false
															...
															X => true/false
														) (x Elements)

			attribute_position			false			array(

														)

			_sale_price_dates_from		false			{ '', (Date) "Y-m-d" }
			_sale_price_dates_to		false			{ '', (Date) "Y-m-d" }

			_regular_price				false			{ '', double $price }
			_sale_price					false			{ '', double $price }

			previous_parent_id			false			* Grouped Products *

			_sold_individually			false			-

			_stock_status				false			{'instock', ''}
			_manage_stock				false			-
			_backorders					false			{'yes', 'no'}

			upsell_ids					false			(string) "productID1, productID2, productID3, productID4"
			crosssell_ids				false			(string) "productID1, productID2, productID3, productID4"

			_download_limit				false			* Downloadable Product *
			_download_expiry			false			* Downloadable Product *
			_wc_file_urls				false			* Downloadable Product *
			_wc_file_names				false			* Downloadable Product *

			_product_url				false			* External Product *
			_button_text				false			* External Product *


		**/

		$_POST['product-type'] = $_POST['product_type']; //js keys cannot contain dashes

		WC_Meta_Box_Product_Data::save( $post_id, get_post( $post_id ) );

		/**
			KEY						|			TYPE: array ( ) (n Elements)


			variable_post_id					id / false
			variable_sku

			variable_regular_price
			variable_sale_price

			upload_image_id

			variable_download_limit
			variable_download_expiry

			variable_shipping_class
			variable_tax_class

			variation_menu_order

			variable_sale_price_dates_from
			variable_sale_price_dates_to

			variable_weight
			variable_length
			variable_width
			variable_height

			variable_enabled

			variable_is_virtual
			variable_is_downloadable

			variable_manage_stock
			variable_stock
			variable_backorders
			variable_stock_status

			variable_description

			default_attribute_{$attribute_name}
		**/
		
		foreach($_POST['variable_post_id'] as $key => $variation_id){
			if(empty($variation_id)){
				$variation_post = array( // Setup the post data for the variation
					'post_title'  => 'Variation #'.$key.' of product #'. $post_id,
					'post_status' => 'publish',
					'post_parent' => $post_id,
					'post_type'   => 'product_variation'
				);
				
				$_POST['variable_post_id'][$key] = wp_insert_post($variation_post); // Insert the variation and get the key
			}else{
				wp_update_post(array(
					"ID"          => $variation_id,
					'post_status' => 'publish',
					"post_parent" => $post_id,
					'post_type'   => 'product_variation'
				));
			}
		}


		WC_Meta_Box_Product_Data::save_variations( $post_id, get_post( $post_id ) );
		
		wc_delete_product_transients($post_id);
		
		WC_Product_Variable::sync($post_id);
		
		if($new){
			die(json_encode(array("message"=>__("New product was successfully imported!", "b4st"))));
		}else{
			die(json_encode(array("message"=>__("Existing product was successfully updated!", "b4st"))));
		}


	} else {
		die(json_encode(array("errors"=>array(__("This operation can only be performed by admins!", "b4st")))));
	}
}

/* Ajax Import Reseller(s) */


function feuerschutz_ajax_import_reseller(){
	header('Content-Type: application/json');
	
	/*if(!empty($_POST['product_type']) && $_POST['product_type'] == 'variable'){
		die(json_encode(array("message"=>__("$" . "_POST: " . print_r($_POST, true), "b4st"), "errors"=>array())));
	}*/

	//but first check if 1) user is admin and 2) all needed params are set
	
	WC()->mailer();
	require(get_template_directory() . '/functions/emails/new-reseller.php');
	new WC_Reseller_Added();

	if ( current_user_can( 'manage_options' ) ) {
		
		$new = false;
		
		$userdata = array(
			'user_nicename'			=> sanitize_title($_POST['display_name']),
			'user_url'				=> $_POST['website'],
			'user_email'			=> $_POST['email'],
			'display_name'			=> $_POST['display_name'],
			'nickname'				=> $_POST['display_name'],
			'first_name'			=> $_POST['first_name'],
			'last_name'				=> $_POST['last_name'],
			'show_admin_bar_front'	=> false
		);
		
		//find user
		$user = get_user_by('email', $_POST['email']);
		
		if($user === false){
			$new = true;
			
			$userdata['user_pass']		= bin2hex(random_bytes(10)); //start with random password
			$userdata['user_login']		= $_POST['email'];
			$userdata['role']			= 'reseller';
			
		}else{
			$userdata['ID']				= (int) $user->ID;
		}
		
		//updates or inserts the user
		$user_id = wp_insert_user($userdata);
		
		if($new){
			//send mail
			
			global $wpdb, $wp_hasher;
			
			$key = wp_generate_password( 20, false );
			
			if(empty($wp_hasher)){
				require_once ABSPATH . 'wp-includes/class-phpass.php';
				$wp_hasher = new PasswordHash(8, true);
			}
			
			$hashed = $wp_hasher->HashPassword($key);
			
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $_POST['email'] ) );
			
			//Load woocommerce mailer
			do_action( 'feuerschutz_new_reseller_imported', $user_id, $key );
		}
		
		feuerschutz_import_reseller_discount($user_id, $_POST['discounts']);
		
		$woocommerce_meta = array(
			"additional_line_above",
			"first_name",
			"last_name",
			"company",
			"address_1",
			"address_2",
			"post_office_box",
			"postcode",
			"city",
			"country",
			"phone",
			"email"
		);
		$woocommerce_prefixes = array('billing_', 'shipping_');
		
		foreach($woocommerce_prefixes as $woocommerce_prefix){
			foreach($woocommerce_meta as $meta_field){
				
				if(!empty($_POST[$meta_field])){
					
					update_user_meta($user_id, $woocommerce_prefix . $meta_field, $_POST[$meta_field]);
				}
			}
		}
		
		$additional_meta = array(
			"address_number"
		);
		
		foreach($additional_meta as $meta_field){
			if(!empty($_POST[$meta_field])){
				update_user_meta($user_id, $meta_field, $_POST[$meta_field]);
			}
		}
		
		
		if($new){
			die(json_encode(array("message"=>__("New reseller was successfully imported!", "b4st"))));
		}else{
			die(json_encode(array("message"=>__("Existing reseller was successfully updated!", "b4st"))));
		}
		
	} else {
		die(json_encode(array("errors"=>array(__("This operation can only be performed by admins!", "b4st")))));
	}
	
}

 
function feuerschutz_billing_fields($fields){
	if(isset($fields['billing_phone'])){
		$fields['billing_phone']['placeholder']		= '000 000 00 00';
	}
	if(isset($fields['billing_state'])){
		$fields['billing_state']['required'] 		= true;
		//Woocommerce bug, currently not showing the asteriks
		//$fields['billing_state']['class'][]			= 'autocomplete-state';
	}
	
	$fields['billing_additional_line_above'] = array(
		'label'				=> __('Additional line above', 'b4st'),
		'placeholder'		=> '',
		'required'			=> false,
		'class'				=> array(''),
		'clear'				=> true
	);
	
	$fields['billing_description'] = array(
		'label'				=> __('Description', 'b4st'),
		'placeholder'		=> '',
		'required'			=> false,
		'class'				=> array(''),
		'clear'				=> true
	);
	
	$fields['billing_post_office_box'] = array(
		'label'				=> __('Post office box', 'b4st'),
		'placeholder'		=> '',
		'required'			=> false,
		'class'				=> array(''),
		'clear'				=> true
	);
	
	$order = array(
		"billing_additional_line_above",
		"billing_company",
		"billing_first_name",
		"billing_last_name",
		"billing_description",
		"billing_address_1",
		/*"billing_address_2",*/
		"billing_post_office_box",
		"billing_postcode",
		"billing_city",
		"billing_state",
		"billing_country",
		"billing_email",
		"billing_phone"
    );
    
    $ordered_fields = array();
    
    foreach($order as $field){
        $ordered_fields[$field] = $fields[$field];
    }
	
	return $ordered_fields;
}

function feuerschutz_shipping_fields($fields){
	if(isset($fields['shipping_phone'])){
		$fields['shipping_phone']['placeholder']		= '000 000 00 00';
	}
	if(isset($fields['shipping_state'])){
		$fields['shipping_state']['required'] 		= true;
		//Woocommerce bug, currently not showing the asteriks
		//$fields['shipping_state']['class'][]			= 'autocomplete-state';
	}
	
	$fields['shipping_additional_line_above'] = array(
		'label'				=> __('Additional line above', 'b4st'),
		'placeholder'		=> '',
		'required'			=> false,
		'class'				=> array(''),
		'clear'				=> true
	);
	
	$fields['shipping_description'] = array(
		'label'				=> __('Description', 'b4st'),
		'placeholder'		=> '',
		'required'			=> false,
		'class'				=> array(''),
		'clear'				=> true
	);
	
	$fields['shipping_post_office_box'] = array(
		'label'				=> __('Post office box', 'b4st'),
		'placeholder'		=> '',
		'required'			=> false,
		'class'				=> array(''),
		'clear'				=> true
	);
	
	$order = array(
		"shipping_additional_line_above",
		"shipping_company",
		"shipping_first_name",
		"shipping_last_name",
		"shipping_description",
		"shipping_address_1",
		/*"shipping_address_2",*/
		"shipping_post_office_box",
		"shipping_postcode",
		"shipping_city",
		"shipping_state",
		"shipping_country"
    );
    
    $ordered_fields = array();
    
    foreach($order as $field){
        $ordered_fields[$field] = $fields[$field];
    }
	
	return $ordered_fields;
	
	return $fields;
}

function feuerschutz_checkout_process($order){
	$states = array(
		'AG',
		'AI',
		'AR',
		'BE',
		'BL',
		'BS',
		'FR',
		'GE',
		'GL',
		'GR',
		'JU',
		'LU',
		'NE',
		'NW',
		'OW',
		'SG',
		'SH',
		'SO',
		'SZ',
		'TI',
		'TG',
		'UR',
		'VD',
		'VS',
		'ZG',
		'ZH'
	);
	
	/*if(isset($_POST['billing_state'])){
		feuerschutz_validate_state($_POST['billing_state'], $states);
	}*/
	/*if(isset($_POST['shipping_state']) && isset($_POST['ship_to_different_address']) && $_POST['ship_to_different_address'] == 1){
		feuerschutz_validate_state($_POST['shipping_state'], $states);
	}*/
	
	if(isset($_POST['billing_phone'])){
		feuerschutz_validate_phone_number($_POST['billing_phone']);
	}
	if(isset($_POST['shipping_phone']) && isset($_POST['ship_to_different_address']) && $_POST['ship_to_different_address'] == 1){
		feuerschutz_validate_phone_number($_POST['shipping_phone']);
	}
	
	
	
	//Check for minimum purchase qty
	foreach(WC()->cart->cart_contents as $key => $item){
		$min_qty = intval(get_post_meta($item['product_id'], '_feuerschutz_min_purchase_qty', true));
	
		if(!empty($min_qty) && $min_qty > 1){
			if($item['quantity'] < $min_qty){
				wc_add_notice(sprintf(__( 'You need to order at least %d of %s!', 'b4st' ), $min_qty, get_the_title($item['product_id'])), 'error' );
			}
		}
	}
}

function feuerschutz_checkout_update_order_meta($order_id){
	if(!empty($_POST['billing_additional_line_above'])){
		update_post_meta($order_id, '_billing_additional_line_above', esc_html($_POST['billing_additional_line_above']));
	}
	if(!empty($_POST['shipping_additional_line_above'])){
		update_post_meta($order_id, '_shipping_additional_line_above', esc_html($_POST['shipping_additional_line_above']));
	}
	
	if(!empty($_POST['billing_description'])){
		update_post_meta($order_id, '_billing_description', esc_html($_POST['billing_description']));
	}
	if(!empty($_POST['shipping_description'])){
		update_post_meta($order_id, '_shipping_description', esc_html($_POST['shipping_description']));
	}
	
	if(!empty($_POST['billing_post_office_box'])){
		update_post_meta($order_id, '_billing_post_office_box', esc_html($_POST['billing_post_office_box']));
	}
	if(!empty($_POST['shipping_post_office_box'])){
		update_post_meta($order_id, '_shipping_post_office_box', esc_html($_POST['shipping_post_office_box']));
	}
	
}

function feuerschutz_woocommerce_email_headers($headers = '', $id = '', $order){
	if ($id == 'new_order'){
		
		$name = $order->billing_first_name . " " . $order->billing_last_name;
		$from = $name . " via Schilder Portal <" . $order->billing_email . ">";
		
		$client = $name . " <" . $order->billing_email . ">";
		
		$headers .= "Reply-To: " . $client . "\r\n";
		$headers .= "Sender: " . $client . "\r\n";
    }
    return $headers;
}

function feuerschutz_woocommerce_email_subject_new_order($subject, $order){
	global $woocommerce;

	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$subject = sprintf('Bestellung (%s) von %s %s', $order->id, $order->billing_first_name, $order->billing_last_name);

	return $subject;
}

function feuerschutz_validate_state($state, $states){
	if(! in_array($state, $states)){
		wc_add_notice( __( 'Invalid state!', 'b4st' ), 'error' );
	}
}
function feuerschutz_validate_phone_number($number){
	if(! preg_match("/[0-9]{3}\ [0-9]{3}\ [0-9]{2}\ [0-9]{2}/", $number)){
		wc_add_notice( __( 'Invalid phone number!', 'b4st' ), 'error' );
	}
}

function feuerschutz_before_calculate_totals($cart){
	
	if(feuerschutz_check_coupons()){
		return;
	}
	
	//var_dump("count", count($cart->cart_contents));
	//var_dump("userid", get_current_user_id());
	
	foreach ($cart->cart_contents as $cart_item_key => $cart_item){
		
		//var_dump($cart_item['data'], feuerschutz_reseller_discount_enabled($cart_item['data']), feuerschutz_get_reseller_discount_by_user());
		
		if(feuerschutz_reseller_discount_enabled($cart_item['data'])){
			$cart_item['data']->set_price(feuerschutz_reseller_discount_get_price($cart_item['data']));
			
		}else if($cart_item['data']->is_on_sale()){
			$cart_item['data']->set_price($cart_item['data']->get_sale_price());
		}else if(feuerschutz_bulk_discount_enabled($cart_item['data'])){
			$cart_item['data']->set_price(feuerschutz_bulk_discount_get_price($cart_item['data'], $cart_item['quantity']));
			
		}
	}
	
}

function feuerschutz_check_coupons() {
	global $woocommerce;
	
	return (!empty($woocommerce->cart->applied_coupons));
}

function feuerschutz_quantity_input_min($min, $product){
	$min_qty = intval(get_post_meta($product->get_id(), '_feuerschutz_min_purchase_qty', true));
	
	if(!empty($min_qty) && $min_qty > 1){
		return $min_qty;
	}
	
	return $min;
}

function feuerschutz_bulk_discount_field($post_id, $name){
	$id = "bulk-discount-" . $post_id;
	
	$bulk_discount = get_post_meta($post_id, '_feuerschutz_bulk_discount', true);
	
	if(empty($bulk_discount) || !isJson($bulk_discount)){
		$bulk_discount = "[]";
	}
	
	echo "<div id='".$id."' class='bulk-discount'>";
	
		echo "<label>".__("Bulk Discount", 'b4st')."</label>";
		
		echo "<table class='bulk-discount' data-init='" . $bulk_discount . "'>";
			echo "<thead style='text-align:left;'><tr><td></th><th>".__("Min. Amount", 'b4st')."</th><th>".__("Price per unit", 'b4st')."</th><th>".__("Actions", 'b4st')."</th></tr></thead>";
		echo "<tbody></tbody>".
			"<tfoot><td colspan='3'></td><td><button type='button' class='button add' style='width: 100%;'>+</button></td></tfoot>".
			"</table>";
		
		woocommerce_wp_hidden_input(array( 
			'id'    => $name, 
			'value' => '[]'
		));
		
	echo "</div>";
	
	echo "<script>jQuery('#".$id."').bulk_discount();</script><!-- Sorry found no better way to do this -->";
}


/* Add custom fields */
function feuerschutz_product_options_general_product_data(){
	
	global $woocommerce, $post;
	
	echo '<div class="options_group">';
		woocommerce_wp_text_input( 
			array( 
				'id'                => '_feuerschutz_min_purchase_qty', 
				'label'             => __( 'Minimum purchase quantity', 'b4st' ), 
				'placeholder'       => '0', 
				'description'       => __( 'Enter the desired minimum purchase quantity.', 'b4st' ),
				'type'              => 'number', 
				'custom_attributes' => array(
					'step' 	=> '1',
					'min'	=> '0'
				) 
			)
		);
	echo '</div>';
	
	echo '<div class="options_group show_if_simple hidden">';
		feuerschutz_bulk_discount_field($post->ID, '_feuerschutz_bulk_discount');
	echo '</div>';
	
}

/* Save custom fields */
function feuerschutz_process_product_meta($post_id){
	
	if(!empty($_POST['_feuerschutz_min_purchase_qty'])){
		update_post_meta($post_id, '_feuerschutz_min_purchase_qty', esc_attr($_POST['_feuerschutz_min_purchase_qty']));
	}
	
	$bulk_discount = stripslashes($_POST['_feuerschutz_bulk_discount']);
	if(!empty($bulk_discount) && isJson($bulk_discount)){
		update_post_meta($post_id, '_feuerschutz_bulk_discount', $bulk_discount);
	}
	
}

function feuerschutz_product_after_variable_attributes($loop, $variation_data, $variation){
	feuerschutz_bulk_discount_field($variation->ID, '_feuerschutz_variable_bulk_discount[' . $variation->ID . ']');
}

function feuerschutz_save_product_variation($post_id){
	
	$variation = new WC_Product_Variation($post_id);
	$parent_product = wc_get_product($variation->get_parent_id());
	
	$bulk_discount = stripslashes($_POST['_feuerschutz_variable_bulk_discount'][$post_id]);
	if(!empty($bulk_discount) && isJson($bulk_discount)){
		update_post_meta($post_id, '_feuerschutz_bulk_discount', $bulk_discount);
	}
	
	$bulk_discount_enabled = false;
	
	$variations = $parent_product->get_available_variations();
	
	foreach($variations as $variation){
		$discount_tmp = removeBOM(get_post_meta($variation['variation_id'], '_feuerschutz_bulk_discount', true));
		
		if(!empty($discount_tmp) && isJson($discount_tmp) && $discount_tmp != "[]"){
			$bulk_discount_enabled = true;
			break;
		}
		
	}
	if($bulk_discount_enabled){
		update_post_meta($parent_product->get_id(), '_feuerschutz_variable_bulk_discount_enabled', '1');
	}else{
		update_post_meta($parent_product->get_id(), '_feuerschutz_variable_bulk_discount_enabled', '0');
	}
	
}

function feuerschutz_bulk_discount_enabled($product){
	
	if(is_a($product, "WC_Product_Variation")){
		$discount = get_post_meta($product->get_id(), '_feuerschutz_bulk_discount', true);
		
		return empty($discount) ? false : ($discount != "[]");
	}else if($product->is_type('simple')){
		$discount = get_post_meta($product->get_id(), '_feuerschutz_bulk_discount', true);
		
		return empty($discount) ? false : ($discount != "[]");
		
	}else if($product->is_type('variable')){
		$enabled = get_post_meta($product->get_id(), '_feuerschutz_variable_bulk_discount_enabled', true);
		
		return empty($enabled) ? false : ($enabled == '1');
	}
}

function feuerschutz_bulk_discount_get_price($product, $quantity = 1){
	
	$product_id = $product->get_id();
	if(is_a($product, "WC_Product_Variation")){
		$product_id = $product->get_id();
	}
	
	$discounts = removeBOM(get_post_meta($product_id, '_feuerschutz_bulk_discount', true));
	$discount_price = $product->get_price();
	
	if(!empty($discounts) && isJson($discounts)){
		$discounts = json_decode($discounts);
		$maxDiscountQuantity = -1;
		
		foreach($discounts as $discount){
			if($quantity >= $discount->qty && $discount->qty >= $maxDiscountQuantity){
				$maxDiscountQuantity = $discount->qty;
				$discount_price = $discount->ppu;
			}
		}
	}
	
	return $discount_price;
}

function feuerschutz_get_reseller_discount(){
	global $feuerschutz_reseller_discount;
	
	if(!empty($feuerschutz_reseller_discount)){
		return $feuerschutz_reseller_discount;
	}
	
	
	$discount_rules = get_field('discount_rules', 'option');
	
	$user_discount = array();
	
	if(have_rows('discount_rules', 'option')){
		while(have_rows('discount_rules', 'option') ){
			the_row();
			
			$layout			= get_row_layout();
			
			$users_ids		= array();
			$product_ids	= array();
			
			$discount		= get_sub_field('discount');
			
			if($layout === 'products_single_users'){
				
				$product_ids	= get_sub_field('products');
				$users			= get_sub_field('users');
				
				foreach($users as $user){
					$user_ids[] = $user['ID'];
				}
				
				
			}else if($layout === 'products_user_groups'){
				
				$product_ids	= get_sub_field('products');
				$user_ids		= get_objects_in_term(get_sub_field('users'),		'user_discount');
				
			}else if($layout === 'categories_single_users'){
				
				$product_ids	= get_objects_in_term(get_sub_field('categories'),	'product_discount');
				$users			= get_sub_field('users');
				
				foreach($users as $user){
					$user_ids[] = $user['ID'];
				}
				
			}else if($layout === 'categories_user_groups'){
				$product_ids	= get_objects_in_term(get_sub_field('categories'),	'product_discount');
				$user_ids		= get_objects_in_term(get_sub_field('users'),		'user_discount');
				
			}
			
			foreach($user_ids as $user_id){
				
				foreach($product_ids as $product_id){
					$user_discount[$user_id][$product_id]	= $discount;
				}
				
			}
			
		}
		
		
	}
	
	$feuerschutz_reseller_discount = $user_discount;
	
	return $feuerschutz_reseller_discount;	
}

function feuerschutz_check_if_reseller_discounts($user_id){
	
	$user = new WP_User($user_id);
	
	if(!empty($user->roles) && is_array($user->roles)){
		foreach ($user->roles as $role){
			if($role === 'administrator' || $role === 'reseller'){
				return true;
			}
		}
	}
	
	return false;
}

function feuerschutz_get_reseller_discount_by_user($user_id = null){
	if($user_id === null){
		$user_id = get_current_user_id();
	}
	
	//var_dump("discount by user", $user_id, feuerschutz_check_if_reseller_discounts($user_id) ? "ye" : "no");
	
	if(feuerschutz_check_if_reseller_discounts($user_id)){
		$discount = feuerschutz_get_reseller_discount();
		//var_dump($discount[$user_id]);
	
		return isset($discount[$user_id]) ? $discount[$user_id] : array();
	}else{
		return array();
	}
}

function feuerschutz_reseller_discount_enabled($product){
	
	$discount = feuerschutz_get_reseller_discount_by_user();
	
	if(is_a($product, "WC_Product_Variation")){
		
		return isset($discount[$product->get_parent_id()]);
		
	}else{
		
		return isset($discount[$product->get_id()]);
	}
}

function feuerschutz_reseller_discount_get_price($product){
	
	$discount = feuerschutz_get_reseller_discount_by_user();
	
	//is sometimes executed two times in a row
	
	if(is_a($product, "WC_Product_Variation")){
		
		$product	= new WC_Product_Variation($product->get_id());
		$price		= $product->get_price();
		
		return isset($discount[$product->get_parent_id()]) ? ((1.0 - (floatval($discount[$product->get_parent_id()])/100)) * $price) : $price;
		
	}else{
		
		$product	= wc_get_product( $product->get_id() );
		$price		= $product->get_price();
		
		return isset($discount[$product->get_id()]) ? ((1.0 - (floatval($discount[$product->get_id()])/100)) * $price) : $price;
	}
}

function feuerschutz_import_reseller_discount($user_id, $discount_groups){
	
	//$user			= get_user_by('ID', $user_id);
	
	$discount_rules		= get_field('discount_rules', 'option');
	$user_disc_terms	= wp_get_object_terms($user_id, 'user_discount', array('fields' => 'all'));
	
	foreach($discount_groups as $discount_group_name => $discount){
		
		$discount_group_name	= trim($discount_group_name);
		$discount				= trim($discount);
		
		//remove all user discount groups related to this product group name form this user
		
		
		$name				= $discount_group_name . "-" . $discount;
		$user_disc_term		= false;
		
		$to_remove			= array();
		
		foreach($user_disc_terms as $term){
			
			if(feuerschutz_starts_with($term->name, $discount_group_name . "-")){
				if($term->name === $name){//same
					$user_disc_term = $term;
				}else{
					//remove it, no longer active
					$to_remove[] = (int) $term->term_id;
				}
			}
			
		}
		
		wp_remove_object_terms($user_id, $to_remove, 'user_discount');
		
		if($user_disc_term === false){
			$user_disc_term = get_term_by('name', $name, 'user_discount');
		}
		
		if($user_disc_term === false){
			$tmp = wp_insert_term(trim($name), 'user_discount');
			
			if(is_wp_error($tmp)){
				continue;
			}
			
			$user_disc_term = get_term_by('id', $tmp['term_id'], 'user_discount');
		}
		
		
		$user_ids = get_objects_in_term($user_disc_term->term_id, 'user_discount');
		
		if(!in_array($user_id, $user_ids)){
			wp_set_object_terms($user_id, ((int) $user_disc_term->term_id), 'user_discount', true /* Append */);
		}
		
		$product_disc_term = get_term_by('name', $discount_group_name, 'product_discount');
		
		if($product_disc_term === false){
			
			$tmp = wp_insert_term(trim($discount_group_name), 'product_discount');
			
			if(is_wp_error($tmp)){
				continue;
			}
			
			$product_disc_term = get_term_by('id', $tmp['term_id'], 'product_discount');
		}
		
		
		foreach($discount_rules as $key => $rule){
			
			$found = false;
			
			if($rule['acf_fc_layout'] == 'categories_user_groups'){ //only this one is auto generated
				
				$categories = $rule['categories'];
				$user_tax	= $rule['users'];
				
				if(count($categories) === 1 && $categories[0] === $product_disc_term->term_id && count($user_tax) === 1 && $user_tax[0] === $user_disc_term->term_id && $rule['discount'] == $discount){
					//the correct rule was already added
					$found = true;
				}
				
				
				
			}
		}
		
		if(!$found){
			$discount_rules[] = array(
				'acf_fc_layout'		=> 'categories_user_groups',
				'categories'		=> array($product_disc_term->term_id),
				'users'				=> array($user_disc_term->term_id),
				'discount'			=> $discount
			);
		}
		
	}
	
	update_field('discount_rules', $discount_rules, 'option');
}

function isJson($string){
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}