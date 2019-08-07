<?php
	
	class Hfag_Admin_Menu {
		
		public function __construct(){
			add_action('admin_menu', array($this, 'modify_admin_menu'), 20);
			add_filter('tiny_mce_plugins', array($this, 'modify_tiny_mce_plugins'));
			add_action(	'admin_head', array($this, 'fix_svg_thumb_display'), 10);
			
			//ajax actions
			add_action('wp_ajax_import_product', array($this, 'ajax_import_product') );
			add_action('wp_ajax_import_reseller', array($this, 'ajax_import_reseller') );
			add_action('wp_ajax_refresh_taxonomy_count', array($this, 'ajax_refresh_taxonomy_count'));
		}
		
		public function modify_admin_menu(){
			remove_menu_page('edit-comments.php');
	
			add_submenu_page(
				'tools.php',
				__('Import Products from Excel', 'b4st'),
				__('Import Products from Excel', 'b4st'),
				'manage_options',
				'import-wc-products-from-excel',
				array($this, 'admin_page_import_products')
			);
			add_submenu_page(
				'tools.php',
				__('Import Resellers from Excel', 'b4st'),
				__('Import Resellers from Excel', 'b4st'),
				'manage_options',
				'import-resellers-from-excel',
				array($this, 'admin_page_import_resellers')
			);
			add_submenu_page(
				'tools.php',
				__('Refresh Taxonomy Count', 'b4st'),
				__('Refresh Taxonomy Count', 'b4st'),
				'manage_options',
				'refresh-taxonomy-count',
				array($this, 'admin_page_refresh_taxonomy_count')
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
		
		public function modify_tiny_mce_plugins($plugins){
			if ( is_array( $plugins ) ) {
				return array_diff( $plugins, array( 'wpemoji' ) );
			} else {
				return array();
			}
		}
		
		public function fix_svg_thumb_display(){
			echo '<style type="text/css">';
				echo '.media-icon img[src$=".svg"] { width: 100% !important; height: auto !important; }';
				echo 'img.thumbnail,.thumbnail img{max-width: 100% !important;max-height: 100% !important;}';
			echo '</style>';
		}
		
		public function admin_page_import_products(){
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
		
		public function admin_page_import_resellers(){
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
				
				//$this->import_reseller_discount(-1, array());
		
				echo "<script src='" . get_template_directory_uri() . '/js/min/excel-json-reseller.min.js' . "' type='text/javascript'></script>";
		
			echo "</div>";
		}
		
		public function admin_page_refresh_taxonomy_count(){
			echo "<div class='wrap'>";
				echo "<h1>" . __('Refresh Taxonomy Count','b4st') . "</h1>";
				
				echo '<script>';
					echo 'function refresh_taxonomy_count(){';
						echo 'jQuery("#refresh-button").hide();';
						echo 'jQuery("#refresh-progress").show();';
						echo 'jQuery.post(ajaxurl, {action: \'refresh_taxonomy_count\', data: {}}, function(response){';
							echo 'jQuery("#response").text(response.message);';
							echo 'jQuery("#refresh-progress").attr("min", "0").attr("max", "1").attr("value", "1");';
						echo '});';
					echo '}';
				echo '</script>';
				
				echo "<button id='refresh-button' class='button button-secondary' onclick='refresh_taxonomy_count()'>" . __("Do It!", 'b4st') . "</button>";
				echo "<h4 id='response'></h4>";
				echo "<progress id='refresh-progress' style='width:100%;display:none;'></progress>";
		
			echo "</div>";
		}
		
		public function import_reseller_discount($user_id, $discount_groups){
			
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
					
					if($this->starts_with($term->name, $discount_group_name . "-")){
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
		
		public function starts_with($haystack, $needle){
			$length = strlen($needle);
			return (substr($haystack, 0, $length) === $needle);
		}
		
		public function ends_with($haystack, $needle){
			$length = strlen($needle);
			if ($length == 0) {
				return true;
			}
			
			return (substr($haystack, -$length) === $needle);
		}
		
		public function ajax_import_product(){
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
							
							usort($input, $this->bulk_discount_cpm_array);
							
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
		
		public function ajax_import_reseller(){
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
					do_action( 'hfag_new_reseller_imported', $user_id, $key );
				}
				
				$this->import_reseller_discount($user_id, $_POST['discounts']);
				
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
		
		public function ajax_refresh_taxonomy_count(){
	
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
		
		public function bulk_discount_cpm_array($a, $b){
			return ($a['qty'] < $b['qty']) ? -1 : 1;
		}
	}
	
	new Hfag_Admin_Menu();
	
?>