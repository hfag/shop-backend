<?php
	class Hfag_Setup {
		
		public function __construct(){
			add_action('after_setup_theme', array($this, 'after_setup_theme'), 20);
			add_action(	'init', array($this, 'register_post_types'), 0);
			add_action(	'init', array($this, 'register_taxonomies'), 1);
			
			add_filter('upload_mimes', function($mime_types){
				$mime_types[] = "text/csv";
				
				return $mime_types;
			}, 10, 1);
		}
		
		public function after_setup_theme(){
			add_theme_support( 'woocommerce' );
			add_theme_support('post-thumbnails');
			
			remove_image_size("search-thumbnail");
			remove_image_size("woocommerce_thumbnail");
			remove_image_size("woocommerce_single");
			remove_image_size("woocommerce_gallery_thumbnail");
			remove_image_size("shop_catalog");
			remove_image_size("shop_single");
			remove_image_size("shop_thumbnail");
			
			add_image_size( 'search-thumbnail', 100, 100, true );
			
			load_theme_textdomain('b4st', get_template_directory() . '/lang/');
		}
		
		public function register_post_types(){
			register_post_type( 'downloads',
				array(
					'labels' => array(
						'name' => __( 'Downloads', 'b4st' ),
						'singular_name' => __( 'Download', 'b4st')
					),
				'public' => false,
				'show_ui' => true,
				'menu_position' => 5,
				'menu_icon' => 'dashicons-download',
				'hierarchical' => false,
				'supports' => array('title'),
				'query_var' => false,
				'has_archive' => false
			));
		}
		
		public function register_taxonomies(){
			$discount_labels = array(
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
			$discount_args = array(
				'labels'                     => $discount_labels,
				'hierarchical'               => true,
				'public'                     => false,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => false,
				'single_value'				 => false,
			);
			
			register_taxonomy( 'product_discount', array( 'product', 'product_variation' ), $discount_args );
			register_taxonomy( 'user_discount', 'user', $discount_args );
			
			
			register_taxonomy('download_categories', array('downloads'),
				array(
					'label' => __( 'Download Categories', 'v4st' ),
					'labels' => array(
						'name' => __( 'Download Categories', 'b4st' ),
						'singular_name' => __( 'Download Category', 'b4st' ),
					),
					'public' => false,
					'show_ui' => true,
					'show_in_nav_menus' => false,
					'show_tagcloud' => false,
					'show_admin_column' => true,
					'hierarchical' => false,
			));
		}
	}
	
	new Hfag_Setup();
?>