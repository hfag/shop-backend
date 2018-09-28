<?php
/*
All the B4ST functions are in the PHP pages in the `functions/` folder.
*/

/*add_action('wp_footer', function(){
	echo "<h1>∆TIME: " . (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) . "</h1>";
});*/

define("FEUERSCHUTZ_SEARCH_INDEX_CRON_ONLY", false);



/*add_action( 'wp_footer', function(){
	if(current_user_can('manage_options')){
		global $wp_filter;
		var_dump( $wp_filter['wp_loaded'] );
	}
} );*/

require_once locate_template('/functions/cleanup.php');
require_once locate_template('/functions/setup.php');
require_once locate_template('/functions/enqueues.php');
require_once locate_template('/functions/navbar.php');
require_once locate_template('/functions/widgets.php');
require_once locate_template('/functions/search-widget.php');
require_once locate_template('/functions/feedback.php');
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
add_action(		'woocommerce_before_main_content',			'feuerschutz_woocommerce_before_main_content',			0			);
add_action(		'woocommerce_sidebar',						'feuerschutz_woocommerce_after_sidebar',				100			);
add_action(		'woocommerce_before_single_product',		'feuerschutz_woocommerce_before_single_product'						);
add_action(		'woocommerce_after_single_product',			'feuerschutz_woocommerce_after_single_product'						);
add_action(		'wp_head',									'feuerschutz_ajaxurl',									0			);

remove_action(	'woocommerce_sidebar',						'woocommerce_get_sidebar',								10			);
remove_action(	'woocommerce_before_main_content',			'woocommerce_output_content_wrapper',					10			);
remove_action(	'woocommerce_after_main_content',			'woocommerce_output_content_wrapper_end',				10			);
remove_action(	'woocommerce_after_shop_loop_item_title',	'woocommerce_template_loop_rating',						5			);
remove_action(	'woocommerce_after_shop_loop_item_title',	'woocommerce_template_loop_price',						10			);
remove_action(	'woocommerce_after_shop_loop_item',			'woocommerce_template_loop_add_to_cart',				10			);
remove_action(	'woocommerce_before_single_product_summary','woocommerce_show_product_sale_flash',					10			);
remove_action(	'woocommerce_before_single_product_summary','woocommerce_show_product_images',						20			);

remove_action(	'woocommerce_before_shop_loop',				'woocommerce_catalog_ordering',							30			);

add_action(		'woocommerce_before_single_product_summary','woocommerce_template_single_title',					1			);
add_action(		'woocommerce_before_single_product_summary','woocommerce_show_product_images',						2			);
add_action(		'woocommerce_before_single_product_summary','feuerschutz_woocommerce_before_single_product_summary',21			);
add_action(		'woocommerce_single_product_summary',		'feuerschutz_variations_slider',						1			);
add_action(		'woocommerce_single_product_summary',		'feuerschutz_horizontal_line',							2			);

remove_action(	'woocommerce_single_product_summary',		'woocommerce_template_single_title',					5			);
remove_action(	'woocommerce_single_product_summary',		'woocommerce_template_single_rating',					10			);
remove_action(	'woocommerce_single_product_summary',		'woocommerce_template_single_price',					10			);
remove_action(	'woocommerce_single_product_summary',		'woocommerce_template_single_excerpt',					20			);
remove_action(	'woocommerce_single_product_summary',		'woocommerce_template_single_meta',						40			);
remove_action(	'woocommerce_single_product_summary',		'woocommerce_template_single_sharing',					50			);

remove_action(	'woocommerce_after_single_product_summary',	'woocommerce_output_product_data_tabs',					10			);
remove_action(	'woocommerce_after_single_product_summary',	'woocommerce_output_related_products',					20			);

add_action(		'woocommerce_archive_description',			'feuerschutz_close_tax_desc_background',				99			);
add_action(		'woocommerce_archive_description',			'feuerschutz_product_filters',							100			);

add_action(		'woocommerce_checkout_order_review',		'feuerschutz_checkout_order_review',					19			);

add_action(		'woocommerce_after_cart_table',				'feuerschutz_after_cart_table',							10			);

add_action(		'woocommerce_edit_account_form_end',		'feuerschutz_edit_account_form_end',					100			);

add_filter(		'product_cat_class',						'feuerschutz_product_cat_class',						20,		3	);
add_filter(		'woocommerce_breadcrumb_defaults', 			'feuerschutz_woocommerce_breadcrumbs',					100			);
add_filter(		'woocommerce_subcategory_count_html',		'feuerschutz_woocommerce_subcategory_count_html',		100,	2	);
add_filter(		'woocommerce_enqueue_styles',				'__return_empty_array',									20			);
add_filter(		'post_class',								'feuerschutz_post_class'											);
add_filter(		'woocommerce_product_tabs',					'feuerschutz_woocommerce_product_tabs',					100			);
//add_filter(		'woocommerce_sale_flash',					'feuerschutz_woocommerce_sale_flash',					100,	3	);

add_filter(		'woocommerce_cart_shipping_method_full_label','feuerschutz_cart_shipping_method_full_label',		100			);

add_filter(		'woocommerce_order_formatted_billing_address', 'feuerschutz_order_formatted_billing_address',		100,	2	);
add_filter(		'woocommerce_order_formatted_shipping_address', 'feuerschutz_order_formatted_shipping_address',		100,	2	);

add_filter(		'woocommerce_my_account_my_address_formatted_address', 'feuerschutz_my_account_my_address_formatted_address', 100, 3);

add_filter(		'woocommerce_localisation_address_formats', 'feuerschutz_localisation_address_formats',				100,	1	);

add_filter(		'woocommerce_formatted_address_force_country_display', '__return_true'											);
add_filter(		'woocommerce_formatted_address_replacements','feuerschutz_formatted_address_replacements',			100,	2	);

add_filter(		'show_admin_bar',							'__return_false');

add_filter(		'woocommerce_mail_content',					'feuerschutz_woocommerce_mail_content',					100,	1	);

add_filter(		'manage_edit-product_cat_columns',			'feuerschutz_fix_woocommerce_description_columns',		100,	1	);

add_filter(		'woocommerce_order_shipping_to_display_tax_label', 'feuerschutz_order_shipping_to_display_tax_label',100,	0	);
add_filter(		'woocommerce_order_shipping_to_display_shipped_via', 'feuerschutz_order_shipping_to_display_shipped_via', 100, 0);
add_filter(		'woocommerce_get_order_item_totals',		'feuerschutz_get_order_item_totals',					100,	2	);

add_action(		'woocommerce_before_shop_loop_item_title',	'feuerschutz_before_shop_loop_item_title',				10			);


/* Change woocommerce behaviour */
add_filter(		'woocommerce_billing_fields' ,				'feuerschutz_billing_fields',							100,	1	);
add_filter(		'woocommerce_shipping_fields' ,				'feuerschutz_shipping_fields',							100,	1	);

add_action(		'woocommerce_checkout_process',				'feuerschutz_checkout_process',							100,	1	);

add_filter(		'woocommerce_ajax_variation_threshold',		'feuerschutz_wc_ajax_variation_threshold',				100,	2	);

add_filter(		'woocommerce_payment_gateways',				'feuerschutz_payment_gateways',							20			);

add_filter(		'woocommerce_thankyou_order_received_text',	'feuerschutz_woocommerce_thankyou_order_received_text',	100,	2	);

add_action(		'woocommerce_email',						'feuerschutz_wc_mails',									100,	1	);

add_action(		'woocommerce_thankyou',						'feuerschutz_thankyou',									100,	1	);

add_action(		'woocommerce_quantity_input_min',			'feuerschutz_quantity_input_min',						10,		2	);


add_action(		'woocommerce_checkout_update_order_meta',	'feuerschutz_checkout_update_order_meta',				10,		1	);

add_filter(		'woocommerce_email_headers',				'feuerschutz_woocommerce_email_headers',				1000,	3	);
add_filter(		'woocommerce_email_subject_new_order',		'feuerschutz_woocommerce_email_subject_new_order',		1,		2	);

add_action(		'woocommerce_account_content',				'feuerschutz_woocommerce_account_content',				5, 		0	);

add_filter(		'woocommerce_account_menu_items',			'feuerschutz_woocommerce_account_menu_items',			10			);

add_filter( 'loop_shop_per_page', function($cols){
	return 50;
}, 20 );

//Custom my-account endpoints
add_action(		'woocommerce_account_discount_endpoint',	'feuerschutz_woocommerce_account_discount',				10			);

/* Adjust price by applying discount */
add_action(		'woocommerce_before_calculate_totals',		'feuerschutz_before_calculate_totals',					10,		1	);

/* Add custom fields */
add_action(		'woocommerce_product_options_general_product_data', 'feuerschutz_product_options_general_product_data', 10		);
add_action(		'woocommerce_process_product_meta',			'feuerschutz_process_product_meta',						10			);

add_action(		'woocommerce_product_after_variable_attributes', 'feuerschutz_product_after_variable_attributes',	10,		3	);
add_action(		'woocommerce_save_product_variation',		'feuerschutz_save_product_variation',					10,		1	);

/* Save them */
add_action(		'personal_options_update',					'feuerschutz_user_profile_save'										);
add_action(		'edit_user_profile_update',					'feuerschutz_user_profile_save'										);

/* Shortcodes */

remove_shortcode(	'gallery'																									);
add_shortcode(		'gallery',								'feuerschutz_photoswipe_gallery_shortcode_func'						);

/* Image compression */

//add_filter(			'wp_generate_attachment_metadata',		'feuerschutz_custom_image_compression'								);

//Reduce min password strength because clients are ... well "smart"
//add_filter( 'woocommerce_min_password_strength', function(){return 3;/*range: 0-3*/});

add_action('after_password_reset', function(){
	wp_redirect(home_url());
    exit;
});

//Ajax

	//Search
	add_action(	'wp_ajax_product_search',					'feuerschutz_ajax_product_search' );
	add_action(	'wp_ajax_nopriv_product_search',			'feuerschutz_ajax_product_search' );

	//Import products
	add_action(	'wp_ajax_import_product',					'feuerschutz_ajax_import_product' );
	add_action(	'wp_ajax_import_reseller',					'feuerschutz_ajax_import_reseller' );

	//Get product id by sku
	add_action(	'wp_ajax_get_product_id_attachment_id',		'feuerschutz_ajax_get_product_id_attachment_id' );
	
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

//Modify wp search
/*add_filter(		'posts_search' ,							'feuerschutz_posts_search',								100,	2	);
add_filter(		'posts_where' ,								'feuerschutz_where_clause',								100,	2	);
add_filter(		'posts_orderby',							'feuerschutz_posts_orderby',							100,	2	);*/

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

//Adjust wc fields factory rendering
add_action(		'wccpf/before/field/rendering',				'feuerschutz_wccpf_before_field_rendering',				5			);
add_action(		'wccpf/after/field/rendering',				'feuerschutz_wccpf_after_field_rendering',				5			);

//WP filters
//add_filter(		'body_class',								'feuerschutz_body_class',								10,		1	);

function feuerschutz_activation(){
	wp_schedule_event(strtotime("Tomorrow 3am"), 'daily', 'feuerschutz_index_search');
	
	flush_rewrite_rules();
}


function feuerschutz_deactivation(){
	wp_clear_scheduled_hook('feuerschutz_index_search');
}

function feuerschutz_query_vars($vars){
	$vars[] = 'discount';

    return $vars;
}


function feuerschutz_get_cols($xs = 12, $sm = 4, $md = 3, $lg = 2, $xl = 2){
	return 'col-xs-'.$xs.' col-sm-'.$sm.' col-md-'.$md.' col-lg-'.$lg.' col-xl-'.$xl;
}

function feuerschutz_get_cols_full_on_mobile($xs = 12, $sm = 4, $md = 3, $lg = 2, $xl = 2){
	return feuerschutz_get_cols($xs, $sm, $md, $lg, $xl);
}

function feuerschutz_get_product_cols($xs = 6, $sm = 4, $md = 3, $lg = 2, $xl = 2){
	return feuerschutz_get_cols($xs, $sm, $md, $lg, $xl);
}

function feuerschutz_get_small_cols($xs = 4, $sm = 4, $md = 3, $lg = 2, $xl = 2){
	return feuerschutz_get_cols($xs, $sm, $md, $lg, $xl);
}

add_action('woocommerce_before_single_product_summary',			'feuerschutz_start_card', 0);

function feuerschutz_start_card(){
	echo "<div class='card card-block'>";
}

add_action('woocommerce_after_single_product_summary',			'feuerschutz_end_card', 25);

function feuerschutz_end_card(){
	echo "</div>";
}

/**
 * feuerschutz_init function.
 *
 * @access public
 * @return void
 */
function feuerschutz_init() {

	load_theme_textdomain('b4st', get_template_directory() . '/lang/');
	
	
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

	//Intercept $_POST and $_GET

	if(isset($_GET['s'])){
		if(empty($_GET['s'])){
			unset($_GET['s']);
		}else{
			unset($_GET['product_category']);
		}
	}

	if(isset($_GET['taxonomies'])){
		foreach($_GET['taxonomies'] as $taxonomy_name => $taxonomy_slugs){
			$_GET['filter_'.$taxonomy_name] = implode(",", $taxonomy_slugs);
		}
	}
	
	//Disable emojis
	
	// all actions related to emojis
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	
	// filter to remove TinyMCE emojis
	add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
	
	function disable_emojicons_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}
	
	add_filter( 'emoji_svg_url', '__return_false' );

}

function feuerschutz_late_init(){
	
	//Intercept late $_POST and $_GET
	
	if(isset($_GET['product_category'])){

		$slug = $_GET['product_category'];

		$url = get_term_link(get_term_by('slug', $slug, 'product_cat'));

		if(!is_wp_error($url)){
			unset($_GET['product_category']);
			
			$url .= "?" . http_build_query($_GET);

			header("Location: " . $url);
			die();
		}
	}
	
	feuerschutz_custom_fields();
	
	//Add custom my account endpoints
	add_rewrite_endpoint('discount', EP_ROOT | EP_PAGES);
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

function feuerschutz_custom_fields(){
	if( function_exists('acf_add_local_field_group') ):
	
		$choices = array();
		$term_selects = array();
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		
		foreach ( $attribute_taxonomies as $tax ) {
			if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
				$choices[ $tax->attribute_name ] = $tax->attribute_label;
				
				$term_selects[] = array (
					'key' => 'field_' . 'feuerschutz_product_filter_attribute_select_' . $tax->attribute_name, //has to be unique
					'label' => $tax->attribute_label . ' ' . __("Categories", 'b4st'),
					'name' => 'filterable_attributes_' . $tax->attribute_name,
					'type' => 'taxonomy',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => array (
						array (
							array (
								'field' => 'field_570ff26fb5655',
								'operator' => '==',
								'value' => $tax->attribute_name,
							),
						),
					),
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'taxonomy' => 'pa_' . $tax->attribute_name ,
					'field_type' => 'checkbox',
					'allow_null' => 0,
					'add_term' => 0,
					'save_terms' => 0,
					'load_terms' => 0,
					'return_format' => 'object',
					'multiple' => 1,
				);
			}
		}
		
		$fields = array(
			array (
				'key' => 'field_570ff26fb5655',
				'label' => __("Filterable Attributes", 'b4st'),
				'name' => 'filterable_attributes',
				'type' => 'checkbox',
				'instructions' => __("Which attributes should be filterable?", 'b4st'),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => $choices,
				'default_value' => array_keys($choices),
				'layout' => 'vertical',
				'toggle' => 0,
			)
		);
		
		foreach($term_selects as $term_select){
			$fields[] = $term_select;
		}
		
		acf_add_local_field_group(array (
			'key' => 'group_570ff266f16ae',
			'title' => __("Attributes", 'b4st'),
			'fields' => $fields,
			'location' => array (
				array (
					array (
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'acf-options-globale-eigenchaftseinstellungen',
					),
				),
				array(
					array(
						'param' => 'taxonomy',
						'operator' => '==',
						'value' => 'product_cat',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'seamless',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',
		));
		
	endif;
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
			
			//Send Mail
			
			/*add_filter('wp_mail_content_type','feuerschutz_set_content_type_html', 100);
			
			$invoice_handling = feuerschutz_generate_invoice($order, 'de_DE');
			wp_mail(get_option( 'admin_email' ), "Neue Bestellung Nr. " . $order->id, $invoice_handling, array(
				'Reply-To' => get_bloginfo('name') . " <info@feuerschutz.ch>"
			));
			
			$invoice_customer = feuerschutz_generate_invoice($order);
			wp_mail($order->billing_email, __("New Order, #", 'b4st') . " " . $order->id . " - " . get_bloginfo('name'), $invoice_customer, array(
				'Reply-To' => get_bloginfo('name') . " <info@feuerschutz.ch>"
			));
			
			remove_filter('wp_mail_content_type', 'feuerschutz_set_content_type_html', 100);*/
			
			// Reduce stock levels
			$order->reduce_order_stock();
		
			// Remove cart
			$woocommerce->cart->empty_cart();
			
			// Mark as on-hold
			//$order->update_status('on-hold', __( 'Awaiting cheque payment', 'b4st' ));
			
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

function feuerschutz_generate_invoice($order, $locale = false){
	
	//custom fields
	$billing_additional_line_above = get_post_meta($order->ID, 'billing_additional_line_above', true);
	$shipping_additional_line_above = get_post_meta($order->ID, 'shipping_additional_line_above', true);
	
	$billing_description = get_post_meta($order->ID, 'billing_description', true);
	$shipping_description = get_post_meta($order->ID, 'shipping_description', true);
	
	$billing_post_office_box = get_post_meta($order->ID, 'billing_post_office_box', true);
	$shipping_post_office_box = get_post_meta($order->ID, 'shipping_post_office_box', true);
	
	$current_locale = get_locale();
	
	if($locale !== false){setlocale(LC_ALL, $locale);}
	
	$table_rows = "";
	$order_items = $order->get_items();
	
	$odd = true;
	
	foreach($order_items as $order_item){
		
		$product = null;
		if(!empty($order_item['variation_id'])){
			$product = new WC_Product($order_item['variation_id']);
		}else{
			$product = new WC_Product($order_item['product_id']);
		}
		
		$table_rows .= "<tr bgcolor='" . ($odd ? "#EEEEEE" : "#F8F7F5") . "'>";
			$table_rows .= "<td style='padding:5px;'>";
				$table_rows .= "<b>" . $order_item['name'] . "</b>";
			$table_rows .= "</td>";
			$table_rows .= "<td style='padding:5px;'>";
				$table_rows .= $product->get_sku();
			$table_rows .= "</td>";
			$table_rows .= "<td style='text-align:center;padding:5px;'>";
				$table_rows .= $order_item['qty'];
			$table_rows .= "</td>";
			$table_rows .= "<td style='text-align:right;padding:5px;'>";
				$table_rows .= "CHF " . number_format($order_item['line_subtotal'], 2, '.', '\'');
			$table_rows .= "</td>";
		$table_rows .= "</tr>";
		
		$odd = !$odd;
	}
	
	$table_rows .= "<tr bgcolor='" . ($odd ? "#EEEEEE" : "#F8F7F5") . "'>";
		$table_rows .= "<td colspan='3' style='text-align:right;padding:5px;'>";
			$table_rows .= 	__("Subtotal", 'b4st');
		$table_rows .= "</td>";
		$table_rows .= "<td style='text-align:right;padding:5px;'>";
			$table_rows .= "CHF " .  number_format($order->get_subtotal(), 2, '.', '\'');
		$table_rows .= "</td>";
	$table_rows .= "</tr>";
	
	$table_rows .= "<tr bgcolor='" . ($odd ? "#EEEEEE" : "#F8F7F5") . "'>";
		$table_rows .= "<td colspan='3' style='text-align:right;padding:5px;'>";
			$table_rows .= __("Shipping & Handling", 'b4st');
		$table_rows .= "</td>";
		$table_rows .= "<td style='text-align:right;padding:5px;'>";
			$table_rows .= "CHF " . number_format($order->get_total_shipping(), 2, '.', '\'');
		$table_rows .= "</td>";
	$table_rows .= "</tr>";
	
	$taxes = $order->get_tax_totals();
	
	foreach($taxes as $taxkey => $tax){
		$table_rows .= "<tr bgcolor='" . ($odd ? "#EEEEEE" : "#F8F7F5") . "'>";
			$table_rows .= "<td colspan='3' style='text-align:right;padding:5px;'>";
				$table_rows .= $tax->label;
			$table_rows .= "</td>";
			$table_rows .= "<td style='text-align:right;padding:5px;'>";
				$table_rows .= "CHF " . number_format($tax->amount, 2, '.', '\'');
			$table_rows .= "</td>";
		$table_rows .= "</tr>";
	}
	
	$table_rows .= "<tr bgcolor='#DEE5E8' style='font-size:16px;'>";
		$table_rows .= "<td></td><td></td>";
		$table_rows .= "<td style='text-align:right;padding:5px;'>";
			$table_rows .= "<b>" . __("Total", 'b4st') . "</b>";
		$table_rows .= "</td>";
		$table_rows .= "<td style='text-align:right;padding:5px;'>";
			$table_rows .= "<b>CHF " . number_format($order->get_total(), 2, '.', '\'') . "</b>";
		$table_rows .= "</td>";
	$table_rows .= "</tr>";
	
	
	
	$date = DateTime::createFromFormat('Y-m-d H:i:s', $order->order_date);

	
	$invoice = file_get_contents(get_template_directory() . '/html/mail/invoice_customer.html');
	$invoice = feuerschutz_mail_templater($invoice, array(
		
		'salutation'					=> __("Hi", 'b4st'),
		'salutation_text'				=> sprintf(
		
			__("Thanks a lot for your order in the Hauser Feuerschutz AG Online-Shop. Should you have any questions, please do not hesitate sending a mail to %s or calling us from monday to thursday in between 8am and 5pm and on friday in between 8am and 4pm on the numer %s.", 'b4st'),
			'<a style="color:#666666;" href="mailto:info@feuerschutz.ch">info@feuerschutz.ch</a>',
			'<a style="color:#666666;" href="tel:0628340540">062 834 05 40</a>'
										),
		
		'confirmation_text'				=> __("In the following your order confirmation", 'b4st'),
		
		'L_your_invoice'				=> __("Your invoice", 'b4st'),
		
		
		'L_billing_information'			=> __("Billing information", 'b4st'),
		'billing_address'				=> str_replace("<br>", "<br/>", $order->get_formatted_billing_address())."<br/>"/*html mail compatible*/,
		'billing_mail'					=> (!empty($order->billing_email) ? __("Email", 'b4st') . ': ' . $order->billing_email . "<br/>" : ''),
		'billing_phone'					=> (!empty($order->billing_phone) ? __("Phone", 'b4st') . ': ' . $order->billing_phone . "<br/>" : ''),
		
		'L_shipping_information'		=> __("Shipping information", 'b4st'),
		
		'shipping_address'				=>	str_replace("<br>", "<br/>", $order->get_formatted_shipping_address())."<br/>"/*html mail compatible*/,
		
		'L_payment_method'				=> __("Payment method", 'b4st'),
		'payment_method'				=> __("Invoice", 'b4st'),
		
		'shipping_method'				=> __("Swiss Post - Prio", 'b4st'),
		
		'customer_message'				=> empty($order->customer_message) ? '' : "<b>" . __("Your message", 'b4st') . "</b></br>" . $order->customer_message,
		
		'L_article'						=> __("Article", 'b4st'),
		'L_sku'							=> __("SKU", 'b4st'),
		'L_amount'						=> __("Amount", 'b4st'),
		'L_subtotal'					=> __("Subtotal", 'b4st'),
		
		
		'invoice_rows'					=> $table_rows,
		
		'date'							=> $date->format('d. F Y'),
		
		'order_id'						=> $order->id,
		
		'site_url'						=> get_site_url(),
		'theme_url'						=> get_template_directory_uri(),
		
		'greetings'						=> __("Should the articles not be in stock, you will immediately receive an email with your terms and conditions. Thank you!", 'b4st'),
		
		'L_phone'						=> __("Phone", 'b4st'),
		'L_telefax'						=> __("Telefax", 'b4st'),
		'L_email'						=> __("Email", 'b4st'),
		'L_web'							=> __("Web", 'b4st'),
		
	));
	
	if($locale !== false){setlocale(LC_ALL, $current_locale);}
	
	return $invoice;
}


/**
 * feuerschutz_mail_templater function.
 * 
 * @access public
 * @param string $content (default: "")
 * @param array $keys (default: array())
 * @return void
 */
function feuerschutz_mail_templater($content = "", $keys = array()){
	foreach($keys as $key => $value){
		$content = str_replace("%" . $key . "%", $value, $content);
	}
	
	$content = preg_replace("/%[A-z]+%/", '', $content);
	
	return $content;
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

/**
 * feuerschutz_woocommerce_breadcrumbs function.
 *
 * @access public
 * @return void
 */
function feuerschutz_woocommerce_breadcrumbs(){
	return array(
        'delimiter'   => '',
        'wrap_before' => '<ol class="woocommerce-breadcrumb breadcrumb" itemprop="breadcrumb">',
        'wrap_after'  => '</ol>',
        'before'      => '<li>',
        'after'       => '</li>',
        'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
    );
}

/**
 * feuerschutz_checkout_order_review function.
 * 
 * @access public
 * @return void
 */
function feuerschutz_checkout_order_review(){
	echo "<h5 class='margin-bottom'>" . __( 'Payment Methods', 'b4st' ) . "</h5>";
}

/**
 * feuerschutz_woocommerce_subcategory_count_html function.
 *
 * @access public
 * @param mixed $html
 * @param mixed $category
 * @return void
 */
function feuerschutz_woocommerce_subcategory_count_html($html, $category){
	return '<mark class="count card marker-top-right hidden-xs-down">' . $category->count . '</mark>';
}

/**
 * feuerschutz_woocommerce_before_main_content function.
 *
 * @access public
 * @return void
 */
function feuerschutz_woocommerce_before_main_content(){
	echo '<div class="container">';
		echo '<div id="content" role="main">';
}

/**
 * feuerschutz_woocommerce_after_sidebar function.
 *
 * @access public
 * @return void
 */
function feuerschutz_woocommerce_after_sidebar(){
		echo '</div>';
	echo '</div>';
}

/**
 * feuerschutz_product_cat_class function.
 *
 * @access public
 * @param mixed $classes
 * @param mixed $class
 * @param mixed $category
 * @return void
 */
function feuerschutz_product_cat_class($classes, $class, $category){
	$classes[] = feuerschutz_get_product_cols();
	return $classes;
}

function feuerschutz_woocommerce_mail_content($styled_message){
	//we wanna inject some print styles in here :)
	//$print_styles = "<style>".file_get_contents(get_template_directory() . '/css/print_styles.min.css')."</style>";
	
	return $styled_message;// . $print_styles;
}

/**
 * feuerschutz_post_class function.
 *
 * @access public
 * @param mixed $classes
 * @return void
 */
function feuerschutz_post_class($classes){
	global $post;

	if(is_single() || is_page()){
		$classes[] = "col-xs-12";
	}else{
		$classes[] = feuerschutz_get_product_cols();
	}

	return $classes;
}

/**
 * feuerschutz_woocommerce_before_single_product function.
 *
 * @access public
 * @return void
 */
function feuerschutz_woocommerce_before_single_product(){
	echo "<div class='row'>";
}
/**
 * feuerschutz_woocommerce_after_single_product function.
 *
 * @access public
 * @return void
 */
function feuerschutz_woocommerce_after_single_product(){
	echo "</div>";
}

/**
 * feuerschutz_woocommerce_product_tabs function.
 *
 * @access public
 * @param mixed $tabs
 * @return void
 */
function feuerschutz_woocommerce_product_tabs($tabs){
	unset($tabs['reviews']);
	return $tabs;
}

function feuerschutz_before_shop_loop_item_title(){
	global $post, $product;
	
	$product_id = $product->get_id();
	
	$icons = array();
	
	if(feuerschutz_reseller_discount_enabled($product)){
		$icons[] = '<span class="hint--top" aria-label="' . __("Reseller Discount", 'b4st') . '"><i class="fa fa-percent"></i></span>';
	}else if($product->is_on_sale()){
		$icons[] = '<span class="hint--top" aria-label="' . __("Flash Sale", 'b4st') . '"><i class="fa fa-bolt"></i></span>';
	}else if(feuerschutz_bulk_discount_enabled($product)){
		$icons[] = '<span class="hint--top" aria-label="' . __("Bulk Discount", 'b4st') . '"><i class="fa fa-percent"></i></span>';
	}
	
	
	if(!empty($icons)){
		echo '<div class="card marker-top-right">' . implode("", $icons) . '</div>';
	}
}

//override thumbnail function to add a wrapper
if(!function_exists( 'woocommerce_template_loop_product_thumbnail')){
	function woocommerce_template_loop_product_thumbnail() {
		
		$meta = wp_get_attachment_metadata(get_post_thumbnail_id());
		
		if(empty($meta)){
			return;
		}
		
		if($meta['width'] >= $meta['height']){
			echo "<figure class='thumbnail-wrapper fix-width'>" . woocommerce_get_product_thumbnail('feuerschutz_fix_width') . "</figure>";
		}else{
			echo "<figure class='thumbnail-wrapper fix-height'>" . woocommerce_get_product_thumbnail('feuerschutz_fix_height') . "</figure>";
		}
		
	}
}

if(!function_exists( 'woocommerce_template_loop_product_title')){
	function woocommerce_template_loop_product_title() {
		global $product;
		
		$terms = get_the_terms( $product->ID, 'product_cat' );
		
		echo "<div class='info'>";
		
			echo "<div class='title'>" . get_the_title($product->ID) . "</div>";
			
			if ($product instanceof WC_Product_Variable) {
				echo "<div class='meta'>";
					echo "<div class='price'>" . __("From", 'b4st') . " <u>" . wc_price($product->get_variation_price("min")) . "</u></div>";
					
					echo implode(", ", wp_list_pluck($terms, 'name'));
					
				echo "</div>";
			}
			
		echo "</div>";
	}
}

if(!function_exists( 'woocommerce_template_loop_category_title')){
	function woocommerce_template_loop_category_title($category) {
		echo "<div class='info'>";
		
			echo "<div class='title'>" . $category->name . "</div>";
			
			if ( $category->count > 0 ){
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
			}
			
			if($category->parent){
				$parent = get_term($category->parent, 'product_cat');
			
				echo "<div class='meta'>";
					echo $parent->name;
				echo "</div>";
			}
			
		echo "</div>";
	}
}

if(!function_exists('woocommerce_subcategory_thumbnail')){
	function woocommerce_subcategory_thumbnail( $category ) {
		$small_thumbnail_size  	= apply_filters( 'subcategory_archive_thumbnail_size', 'shop_catalog' );
		$dimensions    			= wc_get_image_size( $small_thumbnail_size );
		$thumbnail_id  			= get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );
		
		if($thumbnail_id){
			$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size  );
			$image = $image[0];
		}else{
			$image = wc_placeholder_img_src();
		}
		
		if ($image){
			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: https://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );
			echo "<figure class='thumbnail-wrapper'>" . '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />' . "</figure>";
		}
	}
}

if(!function_exists('woocommerce_taxonomy_archive_description')){
	function woocommerce_taxonomy_archive_description() {
		if ( is_product_taxonomy() && 0 === absint( get_query_var( 'paged' ) ) ) {
			
			$uid = "uid-" . substr(sanitize_title(get_the_title()), 0, 20);
			
			$description = wc_format_content( term_description() );
			if ( $description ) {
				echo '<a data-toggle="collapse" href="#' . $uid . '" class="term-description-collapse hidden-lg-up"><i class="fa fa-info-circle"></i></a>';
				echo '<div class="term-description hidden-md-down" id="' . $uid . '">' . $description . '</div>';
			}
		}
	}
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

function feuerschutz_my_account_my_address_formatted_address($address, $customer_id, $name){
	
	$address['additional_line_above']		= get_user_meta($customer_id, $name . '_additional_line_above', true);
	$address['description']					= get_user_meta($customer_id, $name . '_description', true);
	$address['post_office_box']				= /*__("Post office box", 'b4st') . ": " .*/ get_user_meta($customer_id, $name . '_post_office_box', true);
	
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

function feuerschutz_woocommerce_account_content(){
	
	$customer_id = get_user_meta(get_current_user_id(), "address_number", true);
	
	if($customer_id){
		echo __("Customer id", "b4st") . ": " . $customer_id;
	}
}

function feuerschutz_woocommerce_account_menu_items($items){
	
	$downloads = WC()->customer->get_downloadable_products();
	
	if(count($downloads) <= 0){
		unset($items['downloads']);
	}
	
	return feuerschutz_array_insert_at_position($items, array("discount" => __("Discounts", "b4st")), 1);
}

function feuerschutz_woocommerce_account_discount(){
	echo "<table>";
		
		echo "<thead><tr><th>" . __("Products", "woocommerce") . "</th><th>" . __("Discount", 'b4st') . "</th></tr></thead>";
		echo "<tbody>";
		
		$discount_rules = feuerschutz_get_reseller_discount_by_user();
		
		
		$discount_groups = array();
		
		foreach($discount_rules as $product_id => $discount){
			$discount_groups[$discount][] = $product_id;
		}
		
		foreach($discount_groups as $discount => $product_ids){
			echo "<tr><td>";
			
			$first = true;
			
			foreach($product_ids as $product_id){
				if($first){$first=false;}else{echo ", ";}
				echo "<a target='_blank' href='" . get_the_permalink($product_id) . "'>" . get_the_title($product_id) . "</a>";
			}
			
			echo "</td><td>" . $discount . "%</td></tr>";
		}
		echo "</tbody>";
	echo "</table>";
	
	
}

/**
 * feuerschutz_woocommerce_before_single_product_summary function.
 *
 * @access public
 * @return void
 */
function feuerschutz_woocommerce_before_single_product_summary(){
	global $post;
	echo "<main>";
		the_content();
	echo "</main>";
}

function feuerschutz_after_cart_table(){
	echo sprintf(__("Would you like to continue shopping? <a href='%s'>Back to the shop.</a>", 'b4st'), get_permalink( woocommerce_get_page_id( 'shop' ) )) . "<br><br>";
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

/**
 * feuerschutz_horizontal_line function.
 *
 * @access public
 * @return void
 */
function feuerschutz_horizontal_line(){
	echo "<hr>";
}

/**
 * feuerschutz_variations_slider function.
 *
 * @access public
 * @return void
 */
function feuerschutz_variations_slider(){

	global $post, $woocommerce, $product;

	if($product->is_type( 'variable' )){

		echo '<div class="variation-slider">';

			$variations = $product->get_available_variations();
			$unique_variation_images = array();
			
			$last_title = false;
			$all_equal	= true;

			foreach($variations as $variation){
				if($variation['variation_is_visible'] && $variation['variation_is_active'] && $variation['is_purchasable'] && $variation['is_in_stock']){
					$unique_variation_images[$variation['image']['src']]['ids'][] = $variation['variation_id'];
					
					if($all_equal && $last_title !== false && $variation[/*'image_title'*/'variation_description'] != $last_title){
						$all_equal = false;
					}
					
					$last_title = $unique_variation_images[$variation['image']['src']]['image_title'] = $variation[/*'image_title'*/'variation_description'];
				}
			}

			echo "<div class='form-label'>" . __("Variations", 'woocommerce') . "</div>";

			feuerschutz_horizontal_line();
			
			echo "<div class='flyder-wrapper'>";
				echo "<div class='flyder-horizontal slider variations-slider' tabindex='-1'>";
					echo "<div class='flyder-slides slides no-margin row'>";
						foreach($unique_variation_images as $unique_variation_image => $variation){
							echo "<div class='flyder-slide slide variation ".feuerschutz_get_small_cols()."' data-select-attributes='".feuerschutz_get_common_attributes($product, $variation['ids'])."'>";
								echo "<div role='button' class='select-variant'>";
									echo "<img src='".$unique_variation_image."' alt='".trim(strip_tags($variation['image_title']))."'>";
									if(!$all_equal && count($unique_variation_images) !== 1){
										echo "<h6>" . trim(strip_tags($variation['image_title'])) . "</h6>";
									}
								echo "</div>";
							echo "</div>";
						}
					echo "</div>";
				echo "</div>";
			echo "</div>";
			
			echo "<nav class='flyder-nav'>";
				echo "<div class='arrow left'>";
					echo "<i class='fa fa-angle-left'></i>";
				echo "</div>";
				echo "<div class='arrow right'>";
					echo "<i class='fa fa-angle-right'></i>";
				echo "</div>";
			echo "</nav>";

		echo '</div>';
	}
}

function feuerschutz_get_common_attributes($product, $variation_ids = array()){
	
	$attributes = array();
	foreach($variation_ids as $variation_id){
		$variation = new WC_Product_Variation($variation_id);
		$attributes[] = $variation->get_variation_attributes();
	}
	
	if(count($attributes) == 1){
		return json_encode($attributes[0]);
	}else{
		$common_attributes = call_user_func_array("array_intersect", $attributes);
		return json_encode($common_attributes);
	}
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

function feuerschutz_generate_product_square($term, $parent){
	echo "<div class='product-category " . feuerschutz_get_product_cols() . "'>";
		echo "<div class='product-masonry'>";
				$thumbnail_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
				
				echo "<figure class='thumbnail-wrapper'>";
					echo "<a href='" . get_term_link($term) . "'>";
						echo "<img src='" . wp_get_attachment_image_src( $thumbnail_id, 'feuerschutz_fix_width')[0] . "'>";
					echo "</a>";
				echo "</figure>";
				echo "<div class='info'>";
					echo "<a href='" . get_term_link($term) . "'>";
						echo "<div class='title'>" . $term->name . "</div>";
					echo "</a>";
					echo "<a href='" . get_term_link($parent) . "'>";
						echo "<div class='meta'>" . $parent->name . "</div>";
					echo "</a>";
				echo "</div>";
		echo "</div>";
	echo "</div>";
}

function feuerschutz_close_tax_desc_background(){
	echo "</div>";
}


/**
 * feuerschutz_product_filters function.
 *
 * @access public
 * @return void
 */
function feuerschutz_product_filters(){

	echo "<form action='" . get_permalink( woocommerce_get_page_id( 'shop' ) ) . "#product-grid' method='GET'>";
	
		echo "<button class='visible-xs-down hidden-sm-up btn btn-secondary toggle-filters' data-toggle='collapse' data-target='#filterToggles'>".__("Filters", 'b4st')."</button>";

		echo "<div class='filters row'>";
		
					/*global $wpdb;
			
					$currency_pos = get_option( 'woocommerce_currency_pos' );
			
					$sql  = "SELECT min( CAST( price_meta.meta_value AS UNSIGNED ) ) as min_price, max( CAST( price_meta.meta_value AS UNSIGNED ) ) as max_price FROM {$wpdb->posts} ";
					$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id";
					$sql .= " WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish' AND price_meta.meta_key LIKE '%price' AND price_meta.meta_value > '' ";
			
					$result = $wpdb->get_row( $sql );
			
					$current_min = isset($_GET['min_price']) ? $_GET['min_price'] : $result->min_price;
					$current_max = isset($_GET['max_price']) ? $_GET['max_price'] : $result->max_price;
			
					echo "<div class='card card-block'>";
						echo "<h3 class='card-title'>".__("Price", "woocommerce")."</h3>";
			
						echo "<div>";
							echo "<h5>" . __("Min.", 'b4st') . ($currency_pos == 'left_space' ? " " . get_woocommerce_currency_symbol() . " " : '') . "<span data-display='range'>".$current_min."</span>" . ($currency_pos == 'right_space' ? " " . get_woocommerce_currency_symbol() . " " : '') . "</h5>";
							echo "<input id='price-min' type='range' min='".$result->min_price."' max='".$result->max_price."' step='1' name='min_price' value='" . $current_min . "' data-value='".$current_min."' data-range-max='price-max'>";
						echo "</div>";
			
						echo "<div>";
							echo "<h5>" . __("Max.", 'b4st') . " " . get_woocommerce_currency_symbol() . " " . "<span data-display='range'>".$current_max."</span></h5>";
							echo "<input id='price-max' type='range' min='".$result->min_price."' max='".$result->max_price."' step='1' name='max_price' value='" . $current_max . "' data-value='".$current_max."' data-range-min='price-min'>";
						echo "</div>";
			
					echo "</div>";*/
				
				$current_term_id	= -1;
				$current_term_slug	= '';
				
				if(isset(get_queried_object()->taxonomy) && get_queried_object()->taxonomy == 'product_cat' && isset(get_queried_object()->term_id)){
					$current_term_id = get_queried_object()->term_id;
					$current_term_slug = get_queried_object()->slug;
				}
					
				echo "<div id='filterToggles' class='col-xs-12 hidden-xs-up'>";
					
					echo "<h3>" . __("Filter by", 'b4st') . "</h3>";
					
					echo "<button class='visible-xs-down hidden-sm-up btn btn-secondary' data-toggle='collapse' data-target='#filtersCategory'>".__("Categories", 'b4st')."</button>";
					echo "<button class='visible-xs-down hidden-sm-up btn btn-secondary' data-toggle='collapse' data-target='#filtersAttributes'>".__("Attributes", 'b4st')."</button>";
					//echo "<button class='visible-xs-down hidden-sm-up btn btn-secondary' data-toggle='collapse' data-target='#filtersSearch'>".__("Search", 'b4st')."</button>";
				
				echo "</div>";

				echo "<div class='".feuerschutz_get_cols_full_on_mobile(12, 6, 6, 4, 4)."'>";
					
					echo "<div class='hidden-xs-down visible-sm-up' id='filtersCategory'>";

						$product_categories = get_taxonomy_hierarchy('product_cat');
	
						if(!empty($product_categories)){
							echo "<div class='card card-block'>";
								echo "<h3 class='card-title'>".__("Categories", "b4st")."</h3>";
	
								echo "<div class='scrollable-taxonomies categories custom-scrollbar'>";
									feuerschutz_select_taxonomies($product_categories, array($current_term_slug), 0, "radio");
								echo "</div>";
	
							echo "</div>";
						}
						
					echo "</div>";

				echo "</div>";

				$attribute_array      	= array();
				$filterable_attributes	= get_field('filterable_attributes', "product_cat_" . $current_term_id);
				
				if($filterable_attributes === null || $filterable_attributes === ""){
					$filterable_attributes = get_field('filterable_attributes', 'option');
				}else if($filterable_attributes === ""){
					$filterable_attributes = array();
				}
				
				
				foreach($filterable_attributes as $filterable_attribute){
					$attribute_array[ $filterable_attribute ] = get_taxonomy(wc_attribute_taxonomy_name($filterable_attribute));
				}
				
				if(!empty($filterable_attributes)){
					
					echo "<div class='".feuerschutz_get_cols_full_on_mobile(12, 6, 6, 5, 5)."'>";//$xs = 12, $sm = 6, $md = 4, $lg = 3, $xl = 3
					
						echo "<div class='hidden-xs-down visible-sm-up' id='filtersAttributes'>";
						
							echo "<div class='card card-block'>";
								echo "<h3 class='card-title'>".__("Attributes", "woocommerce")."</h3>";
		
								/*tab navigation for different attributes*/
		
								echo "<div class='scrollable-pills custom-scrollbar'>";
									echo "<ul class='nav nav-pills nav-stacked' role='tablist'>";
										$i=0;
										foreach($attribute_array as $attribute_name => $taxonomy){
											echo "<li class='nav-item'>";
												echo "<a class='nav-link".($i==0 ? ' active' : '')."' data-toggle='tab' href='#".$attribute_name."' role='tab'>".$taxonomy->labels->name."</a>";
											echo "</li>";
											$i++;
										}
									echo "</ul>";
								echo "</div>";
		
								//content of each taxonomy
		
								echo "<div class='tab-content'>";
									$i = 0;
		
									foreach($attribute_array as $attribute_name => $taxonomy){
		
										$selected_term_slugs = array();
										$name = substr($taxonomy->name, 3/*pa_*/);
										
										$attributes_to_show = get_field('filterable_attributes_' . $name, "product_cat_" . $current_term_id);
										
										if(empty($attributes_to_show)){
											$attributes_to_show = get_taxonomy_hierarchy($taxonomy->name);
										}
		
										if(isset($_GET['taxonomies'][$name])){
											$selected_term_slugs = (array) $_GET['taxonomies'][$name];
										}
		
										echo "<div class='tab-pane".($i==0 ? ' active' : '')."' id='".$attribute_name."' role='tabpanel'>";
		
											echo "<div class='scrollable-taxonomies attributes custom-scrollbar'>";
												feuerschutz_select_taxonomies($attributes_to_show, $selected_term_slugs, 0, "checkbox");
											echo "</div>";
		
										echo "</div>";
										$i++;
									}
		
									echo "<div class='selected-taxonomies'>";
										if(!empty($_GET['taxonomies'])){
											foreach($_GET['taxonomies'] as $taxonomy_name => $selected_term_slugs){
												foreach($selected_term_slugs as $selected_term_slug){
													$term = get_term_by('slug', $selected_term_slug, wc_attribute_taxonomy_name($taxonomy_name));
													echo "<div class='chip' data-taxonomy-name='".$taxonomy_name."' ".
														"data-term-slug='".$selected_term_slug."'>".
														"<i class='fa fa-times'></i>".
														$term->name.
													"</div>";
												}
											}
										}
									echo "</div>";
		
								echo "</div>";
		
							echo "</div>";
							
						echo "</div>";
						
					echo "</div>";
					
				}

				echo "<div class='".feuerschutz_get_cols_full_on_mobile($xs = 12, 6, 6, 3, 3)."'>";
				
					echo "<div class='card card-block filter-blocks' id='filtersSearch'>";
	
						echo "<input type='text' name='s' placeholder='".__("Search", "woocommerce")."...' value='".get_query_var('s',"")."'>";
						
						/*ob_start();
						woocommerce_catalog_ordering();
						$ordering = ob_get_contents();
						ob_end_clean();
					
						if(function_exists('woocommerce_catalog_ordering') && !empty($ordering)){
	
							//echo "<div class='".feuerschutz_get_cols_full_on_mobile()."'>";
	
							echo $ordering;
	
							//echo "</div>";
	
						}
						
						echo "<hr>";*/
						
						echo "<input type='submit' class='margin-bottom' value='".__("Search", "woocommerce")."'>";
						
						echo "<a class='btn btn-outline' href='" . get_permalink( woocommerce_get_page_id( 'shop' ) ) . "' type='reset'>".__("Reset", "b4st")."</a>";
						
					echo "</div>";

				echo "</div>";
				
				//echo "<div class='".feuerschutz_get_cols_full_on_mobile(12, 6, 4, 2, 2)."'>";
					
				//echo "</div>";

			echo "</div>";

		echo "</div>";

	echo "</form>";

}

function feuerschutz_select_taxonomies($taxonomies=array(), $selected_term_slugs=array(), $depth=0, $input_type="checkbox"){

	echo "<ul class='" . ($depth <= 0 ? '' : 'sub') . "'>";
		foreach($taxonomies as $taxonomy){

			$childless = empty($taxonomy->children);

			echo "<li>";
				if(true/*$childless*/){
					echo "<span class='styled-input'><input type='".$input_type."' id='" . $taxonomy->slug . "' name='".($input_type=="checkbox" ? "taxonomies[".substr($taxonomy->taxonomy, 3/*pa_*/)."][]" : "product_category")."'" .
					" value='" . $taxonomy->slug . "' " .
					(in_array($taxonomy->slug, $selected_term_slugs) ? " checked " : "") . "><label for='".$taxonomy->slug."'></label></span>";
				}

				echo "<label for='".$taxonomy->slug."'><span>" . $taxonomy->name . "</span></label>";
				if(!$childless){
					feuerschutz_select_taxonomies($taxonomy->children, $selected_term_slugs, $depth+1, $input_type);
				}
			echo "</li>";
		}
	echo "</ul>";
}

function feuerschutz_photoswipe_gallery_shortcode_func($attr){
	$attributes = shortcode_atts( array(
		/*'link' => '',*/
		'ids' => '',
		/*'orderby' => '',*/
	), $attr );
	
	feuerschutz_photoswipe_gallery(explode(",", $attributes['ids']));
	
}

function feuerschutz_photoswipe_gallery($attachment_ids){
	
	if (count($attachment_ids) > 0 ) {
		
		echo "<div class='images-pswp'>";
			
			include(get_template_directory().'/html/photoswipe-template.php');
			
			echo "<div class='thumbnails row same-width'>";
			
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
					
					echo "<div class='".feuerschutz_get_small_cols()." image-preview'".
					
						" data-full-src='".$link."' data-title='".$caption."'".
						" data-width='".$meta['width']."' data-height='".$meta['height']."'".
						
					">";
					
						echo wp_get_attachment_image( $attachment_id, 'shop_single', false, array() );
						
					echo "</div>";
				}
			echo "</div>";
		echo "</div>";
		
	}
}

/* Ajax URL */

/**
 * feuerschutz_ajaxurl function.
 *
 * @access public
 * @return void
 */
function feuerschutz_ajaxurl(){

	echo '<script type="text/javascript">';
		echo 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";';
	echo '</script>';

}

/* Ajax Search */

/**
 * feuerschutz_ajax_product_search function.
 *
 * @access public
 * @return void
 */
function feuerschutz_ajax_product_search(){

	if(isset($_POST['s'])){
	
		$productsToSend = 20;

		$wp_query = new WP_Query(array(
			'post_type'			=> 'product',
			's'					=> $_POST['s'],
			'post_status'		=> 'publish'
		));

		$count = count($wp_query->posts);

		$JSON['count'] = sprintf(__("%d of %d results are listed below.", "b4st"), min($productsToSend, $count), $count);


		if($count > $productsToSend){
			$JSON['count'] .= " <a href='" . get_permalink( woocommerce_get_page_id( 'shop' ) ) . sprintf("?s=%s", $_POST['s']) . "'>" . __("To see all search results click here.", "b4st") . "</a>";
		}

		$i = 0;
		$c = 0;
		
		$JSON['products'] = array();

		foreach($wp_query->posts as $post){

			$product = wc_get_product($post->ID);
			$c = count($product->get_available_variations());
			
			if($c > 0){
				$variations = $c . " " . ($c > 1 ? __("Variations", 'b4st') : __("Variation", 'b4st'));
				
			}else{
				$variations = "";
			}
			
			$currency_pos = get_option( 'woocommerce_currency_pos' );

			/*$price =	$product->get_price() !== "" ? (($currency_pos == 'left_space' ? html_entity_decode(get_woocommerce_currency_symbol()) . " " : '') .
						$product->get_price() .
						($currency_pos == 'right_space' ? " " . html_entity_decode(get_woocommerce_currency_symbol()) : '')) : "";*/
						
			$meta			= wp_get_attachment_metadata(get_post_thumbnail_id($post->ID));
			$thumbnail_type = "feuerschutz_fix_width";
			
			if(empty($meta)){
				return;
			}
			
			if($meta['width'] >= $meta['height']){
				$thumbnail_type = "feuerschutz_fix_width";
			}else{
				$thumbnail_type = "feuerschutz_fix_height";
			}
			
			/*if($version > 1){
				$thumbnail_type = "thumbnail";
			}*/

			$product = array();
			
			if($count === 1 && defined("SKU_REDIRECT") && defined("SKU_REDIRECT_ID")){
				$product = array(
					'id'		=> SKU_REDIRECT_ID,
					'title'		=> $post->post_title,
					'variations'=> "1 " . __("Variation", 'b4st'),
					'price'		=> $price,
					'preview'	=> get_the_post_thumbnail_url(SKU_REDIRECT_ID, $thumbnail_type),
					'url'		=> SKU_REDIRECT
				);
			}else{
				$product = array(
					'id'		=> $post->ID,
					'title'		=> $post->post_title,
					'variations'=> $variations,
					'price'		=> $price,
					'preview'	=> get_the_post_thumbnail_url($post, $thumbnail_type),
					'url'		=> feuerschutz_product_id_get_permalink($post->ID)
				);
			}
			
			$JSON['products'][] = $product;

			$i++;
			if($i >= $productsToSend){
				break;
			}
		}

		unset($wp_query);

	}
	
	header('Content-Type: application/json');
	die(json_encode($JSON));
}


/**
 * feuerschutz_ajax_get_attachment_id_by_image_code function.
 *
 * @access public
 * @param string $image_code The attachment filename
 * @return void
 */
function feuerschutz_get_attachment_id_by_image_code($image_code){
	global $wpdb;

	$row = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) as count, post_id FROM " . $wpdb->postmeta . " WHERE meta_key='_wp_attached_file' AND meta_value LIKE '%s'", '%/'.$image_code));

	if($row->count > 0){

		return ((int) $row->post_id);

	}else{
		return false;
	}
}

/**
 * feuerschutz_ajax_get_product_id_attachment_id function.
 *
 * @access public
 * @return void
 */
function feuerschutz_ajax_get_product_id_attachment_id(){
	$JSON = array(
		'sku' => array(),
		'image_code' => array()
	);

	if(!empty($_POST['data'])){

		global $wpdb;

		foreach($_POST['data']['sku'] as $sku){
			$id = wc_get_product_id_by_sku($sku);
			$JSON['sku'][$sku] = ($id == 0 ? false : $id); //probably not necessary
		}

		foreach($_POST['data']['image_code'] as $image_code){
			$JSON['image_code'][$image_code] = feuerschutz_get_attachment_id_by_image_code($image_code);
		}
	}

	header('Content-Type: application/json');
	die(json_encode($JSON));
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

		//The dirty way because I don't want to create a big overhad
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
	$states = feuerschutz_get_states();
	
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


function feuerschutz_wc_ajax_variation_threshold($qty, $product){
	return 105; /* don't use ajax, selection is messed up because it only fetches a product after selecting all the fields to not cause to many requests */
}

function feuerschutz_edit_account_form_end(){
	printf(__("By the way, we're using <a href='%s' target='_blank'>Gravatar</a>, so if you would like to add a profile picture, register the same mail you're using in this shop on Gravatar and set your profile picture there. What are the benefits? Well if you ever encounter another site supporting Gravatar, you won't need to upload another profile picture as it already exists.", 'b4st'), 'https://gravatar.com');
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

function feuerschutz_wccpf_before_field_rendering($args){
	echo '<div class="form-group">';
		echo '<div class="form-label">';
			echo '<label for="' . $args['name'] . '">' . $args['label'] . ($args['required'] == 'yes' ? '*' : '') .  '</label>';
		echo '</div>';
}

function feuerschutz_wccpf_after_field_rendering(){
	echo '</div>';
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

function feuerschutz_bulk_discount_echo_table($product){
	$bulk_discount = feuerschutz_get_discount_coeffs($product);

	if(!empty($bulk_discount) && !feuerschutz_check_if_reseller_discounts(get_current_user_id()) && !$product->is_on_sale() && feuerschutz_bulk_discount_enabled($product)){
		echo "<div class='bulk-discount col-xs-12 col-md-6 col-lg-4'>";
		
			echo "<div class='form-title'>" . __("Bulk Discount", 'b4st') . "</div>";
		
			echo "<table class='bulk-discount small-padding no-collapse'>";
				echo "<thead><tr><th>" . __("Min. Amount", 'b4st') . "</th><th>" . __("Price per unit", 'b4st') . "</th></tr></thead>";
				echo "<tbody>";
				
					foreach($bulk_discount as $id => $data_array){
						
						foreach($data_array as $data){
							echo "<tr data-id='" . $id . "' style='display:none;'><td>" . $data->qty . "</td><td>" . number_format($data->ppu, 2) . "</td></tr>";
						}
					}
				echo "</tbody>";
			echo "</table>";
		
		echo "</div>";
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


/**
 * feuerschutz_get_states function.
 * 
 * @access public
 * @return array
 */
function feuerschutz_get_states(){
	return array(
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
}

function isJson($string){
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

function feuerschutz_escape_array(&$array = array()){ /*save a little bit of ram, not modifying them in here*/
    global $wpdb;
    
    $escaped = array();
    
    foreach($array as $k => $v){
        if(is_numeric($v)){
	      $escaped[] = $wpdb->prepare('%d', $v);  
        }else{
	      $escaped[] = $wpdb->prepare('%s', $v);  
        }
    }
    return implode(',', $escaped);
}

function feuerschutz_index_search(){
	global $wpdb;
	
	set_time_limit(10 * 60); // 10 minutes should be pleeenty of time
	
	$t = feuerschutz_relevance_search_table_terms();
	
	if(FEUERSCHUTZ_SEARCH_INDEX_CRON_ONLY){
		feuerschutz_index_search_terms(wp_list_pluck($wpdb->get_results("SELECT term FROM " . $t, ARRAY_A), 'term'), false);
	}
}

function feuerschutz_get_relevance_term_ids(&$terms, &$t){ /*save a little bit of ram, not modifying them in here*/
	global $wpdb;
	
	$term_ids = array();
	
	$query =	"SELECT " . $t . ".*" .
				" FROM " . $t .
				" WHERE " . $t . ".term IN (" . feuerschutz_escape_array($terms) . ")";
				
	$results = $wpdb->get_results($query);
	
	foreach($results as $row){
		$term_ids[$row->term] = $row->ID;
	}
	
	return $term_ids;
}

function feuerschutz_index_search_terms($terms = array(), $redirect = true){
	if(empty($terms)){return;}
	
	global $wpdb;
	
	$term_ids = array();
	
	$r = feuerschutz_relevance_search_table_relations();
	$t = feuerschutz_relevance_search_table_terms();
	
	//ensure that all terms all present in the db

	//first check which are in it
	$term_ids = feuerschutz_get_relevance_term_ids($terms, $t);
	
	// and which aren't
	$terms_to_add = array();
	
	foreach($terms as $term){
		if(!isset($term_ids[$term])){
			//not present in the db, add it
			$terms_to_add[] = $term;
		}
	}
	
	//add the ones missing
	$query =	"INSERT INTO " . $t . "(term) VALUES (" . feuerschutz_escape_array($terms_to_add) . ")";
	$wpdb->query($query);
	
	//and now get all ids
	$term_ids = array_merge($term_ids, feuerschutz_get_relevance_term_ids($terms_to_add, $t));
	
	unset($terms_to_add);
	
	
	$query =	"SELECT" . 
					" " . $wpdb->posts . ".ID," .
					" " . $wpdb->posts . ".post_title," .
					" " . $wpdb->posts . ".post_content," .
					" GROUP_CONCAT(DISTINCT " . $wpdb->prefix . "terms.name SEPARATOR ', ') as terms," .
					" GROUP_CONCAT(DISTINCT variations_sku.meta_value SEPARATOR ', ') as skus," .
					" postmeta_attributes.meta_value as attributes" .
				" FROM " . $wpdb->posts . "" .
					" LEFT JOIN " . $wpdb->posts . " variations ON " . $wpdb->posts . ".ID = variations.post_parent" .
					" LEFT JOIN " . $wpdb->prefix . "term_relationships ON " . $wpdb->posts . ".ID = " . $wpdb->prefix . "term_relationships.object_id" .
					" LEFT JOIN " . $wpdb->prefix . "term_taxonomy ON " . $wpdb->prefix . "term_relationships.term_taxonomy_id = " . $wpdb->prefix . "term_taxonomy.term_taxonomy_id" .
					" LEFT JOIN " . $wpdb->prefix . "terms ON " . $wpdb->prefix . "term_taxonomy.term_id = " . $wpdb->prefix . "terms.term_id" .
					" LEFT JOIN " . $wpdb->prefix . "postmeta variations_sku ON variations.ID = variations_sku.post_id" .
					" LEFT JOIN " . $wpdb->prefix . "postmeta postmeta_attributes ON " . $wpdb->posts . ".ID = postmeta_attributes.post_id" .
				" WHERE " . $wpdb->posts . ".post_type = 'product'" .
					" AND variations_sku.meta_key = '_sku'" .
					" AND postmeta_attributes.meta_key = '_product_attributes'" .
				" GROUP BY " . $wpdb->posts . ".ID";
	
	$products = $wpdb->get_results($query);
	
	foreach($products as $product){
		
		$url = "";
		
		if(is_string($product->skus)){
			$product->skus = explode(", ", $product->skus);
		}
		if(is_string($product->terms)){
			$product->terms = explode(", ", $product->terms);
		}
		if(is_string($product->attributes)){
			$product->attributes = unserialize($product->attributes);
		}
		
		foreach($product->attributes as $attribute_key => $attribute){
			if(!empty($attribute['value'])){
				$product->terms = array_merge($product->terms, explode(" | ", $attribute['value']));
			}
		}
		
		foreach($terms as $term){
			
			$score	= 0;
			$sku_product_id	= false;
			
			foreach($product->skus as $sku){
				$sku_score = feuerschutz_calc_occurences($sku, $term);
				
				
				if($sku_score == 10){//exact match
					//the user searched exactly for this product soo we gonna directly open it for him
					$sku_product_id = wc_get_product_id_by_sku($sku);
					$url = feuerschutz_product_id_get_permalink($sku_product_id);
				}
				
				$score += $sku_score * 100;
			}
			
			$score	+=		feuerschutz_calc_occurences($product->post_title, $term)			* 10;
			
			foreach($product->terms as $term_){
				$score += feuerschutz_calc_occurences($term_, $term)							* 10;
			}
			
			$score	+=		feuerschutz_calc_occurences($product->post_content, $term)			* 5;
			
			//store score for term
			if(isset($term_ids[$term])){
				//present in the db, update relation
				$wpdb->query($wpdb->prepare("INSERT INTO " . $r . " (post_id, term_id, score) VALUES(%d, %d, %d) ON DUPLICATE KEY UPDATE score = %d", $product->ID, $term_ids[$term], $score, $score));
			}
			
			if(!empty($url)){
				if($redirect){
					//if exactly one match was found, redirect to this url
					header("Location: " . $url);
					die();
				}else{
					define("SKU_REDIRECT", $url);
					define("SKU_REDIRECT_ID", $sku_product_id);
				}
			}
		}
	}
}

function feuerschutz_product_id_get_permalink($product_id = -1){
	
	$parent_id = wp_get_post_parent_id($product_id);
	
	if($parent_id == 0 || empty($parent_id)){
		//simple
		$wc_product = new WC_Product($product_id);
		return $wc_product->get_permalink();
	}else{
		// a variable product, add the selection params
		$wc_variation = new WC_Product_Variation($product_id);
		return  $wc_variation->get_permalink();
	}
}

function feuerschutz_calc_occurences($content = "", $term = "", $prioritizeEndings = true){
	$score = 0;
	$len = strlen($term);
	
	$content = strtolower($content);
	
	if($content == $term){
		//exact match
		return 10;
		
	}else{
		$lastPos = 0;
		
		while (($lastPos = strpos($content, $term, $lastPos)) !== false) {
				
			if($lastPos == 0 || $content{$lastPos-1} == " " || strlen($content) <= ($lastPos + $len + 1)){
				//starts or ends with
				$score += 5;
			}else{
				//"normal" occurence
				$score ++;
			}
			
		    $lastPos = $lastPos + strlen($term);
		    
		}
	}
	
	return $score;
}

function feuerschutz_search($query){
	global $wpdb, $feuerschutz_search_ids;
	
	if(isset($feuerschutz_search_ids) && is_array($feuerschutz_search_ids)){
		return $feuerschutz_search_ids;
	}
	
	$r = feuerschutz_relevance_search_table_relations();
	$t = feuerschutz_relevance_search_table_terms();
	
	if(empty($query->query_vars['s'])){
		$feuerschutz_search_ids = array();
		
		return $feuerschutz_search_ids;
	}
	
	$search = preg_replace("/(\"|')/", "", strtolower($query->query_vars['s'])); //remove all quotes
	$search_terms = array();
	
	$auto_mapping = array(
		'iso 7010'		=> '"iso 7010"',
		'iso7010'		=> '"iso 7010"',
		'en i 4604'		=> '"en i 4604"',
		'eni 4604'		=> '"en i 4604"',
		'en i4604'		=> '"en i 4604"',
		'eni4604'		=> '"en i 4604"',
		'din en 3'		=> '"din en3"',
		'din en3'		=> '"din en3"',
		'dinen 3'		=> '"din en3"',
		'dinen3'		=> '"din en3"'
	);
	
	foreach($auto_mapping as $toReplace => $replace){
		$search = str_replace($toReplace, $replace, $search);
	}
	
	$search = preg_replace('/"+/','"', $search); //replace possible double/tripple/... quotes
	
	$search = preg_replace("/[^\S ]+/", "", $search); //remove all non-space whitespace chars
	
	$search_terms = str_getcsv($search, ' '); //abuse this function to split by space except in enclosed in "
	
	/*if(count($search_terms) > 7){//prevent useless use of a accurate but slow algo
		return array();
	}*/
	
	$search_terms = array_unique($search_terms); //remove duplicates
	$new_terms = array();
	
	foreach($search_terms as $key => $search_term){
		
		$len = strlen($search_term);
		
		if($len < 3/* || $len > 20*/){
			unset($search_terms[$key]);//prevent useless use of a accurate but slow algo
		}else{
			if(FEUERSCHUTZ_SEARCH_INDEX_CRON_ONLY){
				if($wpdb->get_row($wpdb->prepare("SELECT COUNT(*) as count FROM " . $t . " WHERE " . $t . ".term = %s", $search_term))->count == 0){
					$new_terms[] = $search_term;
				}
			}else{
				$new_terms[] = $search_term;
			}
		}
	}
	
	feuerschutz_index_search_terms($new_terms, (!defined('DOING_AJAX') || !DOING_AJAX));
	
	$query =	"SELECT " . $wpdb->posts . ".ID as id, SUM(" . $r . ".score) as total_score" .
				" FROM " . $wpdb->posts .
					" LEFT JOIN " . $r . " ON " . $wpdb->posts	. ".ID = "			. $r . ".post_id" .
					" LEFT JOIN " . $t . " ON " . $r			. ".term_id = "		. $t . ".ID" .
				" WHERE " . $t . ".term IN (" . feuerschutz_escape_array($search_terms) . ")" .
				" GROUP BY " . $wpdb->posts . ".ID" .
				" HAVING total_score != 0" .
				" ORDER BY total_score DESC, " . $wpdb->posts . ".post_title";
				
	$feuerschutz_search_ids = wp_list_pluck($wpdb->get_results( $query , ARRAY_A ), 'id');
	
	return $feuerschutz_search_ids;
				
	
}

function feuerschutz_relevance_search_table_relations(){
	global $wpdb;
	
	return $wpdb->prefix . "relevance_search_relations";
}

function feuerschutz_relevance_search_table_terms(){
	global $wpdb;
	
	return $wpdb->prefix . "relevance_search_terms";
}

function feuerschutz_apply_relevance_search($query){
	return (($query->is_search() && !is_admin()) || (defined('DOING_AJAX') && DOING_AJAX))
		&& !empty($query->query_vars['s'])
		&& (strlen($query->query_vars['s']) > 2) && $query->query['post_type'] === "product";
}

function feuerschutz_posts_search($search, $query){
	
	global $wpdb;
	
	/* Search query */
	if (feuerschutz_apply_relevance_search($query)){
		$search_ids = feuerschutz_search($query);

		$search = " AND " . $wpdb->posts . ".ID IN (" . implode(",", $search_ids) . ") ";
	}
	
	return $search;
}

function feuerschutz_where_clause($where, $query){
	global $wpdb;
	
	/* Moved to 'posts_search' filter */
	
	return $where;
}

function feuerschutz_posts_orderby($orderby, $query){
	global $wpdb;
	
	if ( feuerschutz_apply_relevance_search($query) ) {
		$search_ids = feuerschutz_search($query);
		
		$orderby = "FIELD(".$wpdb->posts.".ID,".implode(",", $search_ids).")," . $orderby;
	}
	
	return $orderby;
}

function feuerschutz_custom_image_compression($image_data){
	
	$upload_dir = wp_upload_dir();
	
	$path = $upload_dir['basedir'] . "/" . dirname($image_data['file']);
	
	foreach($image_data['sizes'] as $thumbnail_name => $image_array){
		
		if(in_array($image_array['mime-type'], array("image/png", "image/jpg", "image/jpeg", "image/gif"))){
			$filelocation	= $path . "/" . $image_array['file'];
			
			if(!file_exists($filelocation)){
				continue;
			}
			
			$image = new imagick($filelocation);
			$pathinfo = pathinfo($filelocation);
			
			$width	= $image->getImageWidth();
			$height	= $image->getImageHeight();
			
			$image->setResolution($image_array['width'], $image_array['height']);
			$image->setImageBackgroundColor('white');
			$image->setImageCompression(true);
			$image->setCompression(Imagick::COMPRESSION_JPEG);
			$image->setCompressionQuality(40); 
			
			unlink($filelocation); //delete the old file prior to write
			
			$image->writeImage($path . "/" . $pathinfo['filename'] . ".jpeg");
			$image->clear();
			$image->destroy();
			
			$image_array['file'] = $pathinfo['filename'] . ".jpeg";
			
			$image_data['sizes'][$thumbnail_name] = $image_array;
			
		}
		
	}
	
	
	return $image_data;
}

function feuerschutz_array_insert_at_position($array, $items, $position){
	return array_slice($array, 0, $position, true) + $items + array_slice($array, $position, count($array) - 1, true);
}


/**
 * Recursively get taxonomy hierarchy, from http://www.daggerhart.com/wordpress-get-taxonomy-hierarchy-including-children/
 *
 * @param string $taxonomy
 * @param int $parent - parent term id
 * @return array
 */
function get_taxonomy_hierarchy( $taxonomy, $parent = 0 ) {
	// only 1 taxonomy
	$taxonomy = is_array( $taxonomy ) ? array_shift( $taxonomy ) : $taxonomy;

	// get all direct decendents of the $parent
	$terms = get_terms( $taxonomy, array( 'parent' => $parent ) );

	// prepare a new array.  these are the children of $parent
	// we'll ultimately copy all the $terms into this new array, but only after they
	// find their own children
	$children = array();

	// go through all the direct decendents of $parent, and gather their children
	foreach ( $terms as $term ){
		// recurse to get the direct decendents of "this" term
		$term->children = get_taxonomy_hierarchy( $taxonomy, $term->term_id );
	
		// add the term to our new array
		$children[ $term->term_id ] = $term;
	}

  // send the results back to the caller
  return $children;
}

class Walker_Feuerschutz_FA extends Walker_Nav_Menu {
	
	public $el; //The element tag
	public $dropdown; //whether the dropdown-item class should be used or not
	
	function __construct($el = 'a', $dropdown = false){
		$this->el			= $el;
		$this->dropdown		= $dropdown;
	}

	/*
		Copied and modified code from wp-includes/nav-menu-template.php

		* Removed *most* filters
		* changed submenu ul to div
		* changed li to a
		* added custom class
		* absused unused container_class to set the element
	*/

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= PHP_EOL . $indent . "<div class=\"sub-menu\">" . PHP_EOl;
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= $indent . "</div>" . PHP_EOL;
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join(' ', array_filter( $classes )) . ($this->dropdown ? ' dropdown-item' : '');
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = 'menu-item-'. $item->ID;
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		
		if($this->el == 'a'){
			$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		}

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$output .= $args->before;
		
		$output .= '<' . $this->el . $id . $class_names . ' ' . $attributes .'>' . (empty($item->description) ? '' : '<i class="'.$item->description.'"></i>');
		
		if($this->el != 'a'){
			$output .= '<a href='.$item->url.'>';
		}
		$output .= $args->link_before . $title . $args->link_after;
	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if($this->el != 'a'){
			$output .= "</a>";
		}
		$output .= "</" . $this->el . ">" . PHP_EOL;
		$output .= $args->after;
	}

}
