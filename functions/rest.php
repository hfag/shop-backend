<?php
	
	class Hfag_Rest {
		
		public function __construct(){
			add_action('init', array($this, 'add_rest_support'), 25);
			add_action('rest_api_init', array($this, 'init_routes'));
			add_action('hfag_cache_product_json', array($this, 'cache_product_json'));
			
			//deactivation hook
			add_action('switch_theme', array($this, 'theme_deactivation'));
			//activation hook
			add_action('after_switch_theme', array($this, 'theme_activation'));
			
			add_filter('rest_pre_serve_request', array($this, "pre_serve_request"), 10, 4);
			
			add_filter('rest_product_cat_collection_params', array($this, "enable_product_cat_orderby"), 10, 1);
			
			/**
			 * We have to tell WC that this should not be handled as a REST request.
			 * Otherwise we can't use the product loop template contents properly.
			 * Since WooCommerce 3.6
			 *
			 * @param bool $is_rest_api_request
			 * @return bool
			 */
			add_filter( 'woocommerce_is_rest_api_request', function ( $is_rest_api_request ) {
				if ( empty( $_SERVER['REQUEST_URI'] ) ) {
						return $is_rest_api_request;
				}
			
				// Bail early if this is not our request.
				if ( false === strpos( $_SERVER['REQUEST_URI'], "hfag" ) ) {
					return $is_rest_api_request;
				}
				
				wc()->frontend_includes();
			
				return false;
			} );
			
			
			//change some woocommerce behaviour
			add_filter('rest_product_collection_params', function($params){
				$params['orderby']['enum'][] = 'menu_order';
				return $params;
			}, 10, 1);
		}
		
		public function theme_deactivation(){
			wp_unschedule_event(wp_next_scheduled("hfag_cache_product_json"), "hfag_cache_product_json");
		}
		
		public function theme_activation(){
			$today = new DateTime();
			$today->setTime(2,0);
			$today->add(new DateInterval('P1D'));
			
			wp_schedule_event($today->getTimestamp(), "daily", "hfag_cache_product_json");
			$this->cache_product_json();//run once
		}
		
		public function add_rest_support(){
			global $wp_taxonomies;
			global $wp_post_types;
			
			$taxonomies = array('product_cat');
			
			foreach($taxonomies as $taxonomy){
				
				if(isset($wp_taxonomies[$taxonomy])){
					$wp_taxonomies[$taxonomy]->show_in_rest = true;
					$wp_taxonomies[$taxonomy]->rest_base = $taxonomy;
					$wp_taxonomies[$taxonomy]->rest_controller_class = 'WP_REST_Terms_Controller';
				}
			}
			
			register_rest_field('product', 'minPrice', array(
				'get_callback' => function($product_data){
					$variable = new WC_Product_Variable($product_data['id']);
					
					return $variable ? $variable->get_variation_price("min") : null;
				},
				'schema' => array(
					'description' => __('The lowest price for this product.', 'b4st'),
					'type' => 'array',
					'context' => array('view'),
				)
			));
			
			register_rest_field('product', 'minOrderQuantity', array(
				'get_callback' => function($product_data){
					return get_post_meta($product_data["id"], "_feuerschutz_min_order_quantity", true) || 1;
				},
				'schema' => array(
					'description' => __('The mininum order quantity for this product.', 'b4st'),
					'type' => 'array',
					'context' => array('view'),
				)
			));
			
			register_rest_field('post', 'description', array(
				'get_callback' => function($post_data){
					return get_field("description", $post_data->ID);
				},
				'schema' => array(
					'description' => __('A short description', 'b4st'),
					'type' => 'array',
					'context' => array('view'),
				)
			));
			
			add_filter('rest_prepare_post', function($data, $post, $context){
				
				unset($data->data["date"]);
				unset($data->data["date_gmt"]);
				unset($data->data["guid"]);
				unset($data->data["modified"]);
				unset($data->data["modified_gmt"]);
				unset($data->data["status"]);
				unset($data->data["type"]);
				unset($data->data["link"]);
				unset($data->data["content"]["protected"]);
				unset($data->data["excerpt"]);
				unset($data->data["comment_status"]);
				unset($data->data["ping_status"]);
				unset($data->data["template"]);
				unset($data->data["meta"]);
				
				$data->remove_link('collection');
			    $data->remove_link('self');
			    $data->remove_link('about');
			    $data->remove_link('author');
			    $data->remove_link('replies');
			    $data->remove_link('version-history');
			    //$data->remove_link('https://api.w.org/featuredmedia');
			    //$data->remove_link('https://api.w.org/attachment');
			    $data->remove_link('https://api.w.org/term');
			    $data->remove_link('curies');
				
				return $data;
			}, 100, 3);
			
			add_filter('rest_prepare_product', function($data, $post, $context){
				
				unset($data->data["date"]);
				unset($data->data["date_gmt"]);
				unset($data->data["guid"]);
				unset($data->data["modified"]);
				unset($data->data["modified_gmt"]);
				unset($data->data["status"]);
				unset($data->data["type"]);
				unset($data->data["link"]);
				unset($data->data["content"]["protected"]);
				unset($data->data["excerpt"]);
				unset($data->data["comment_status"]);
				unset($data->data["ping_status"]);
				unset($data->data["template"]);
				unset($data->data["meta"]);
				
				$data->remove_link('collection');
			    $data->remove_link('self');
			    $data->remove_link('about');
			    $data->remove_link('author');
			    $data->remove_link('replies');
			    $data->remove_link('version-history');
			    //$data->remove_link('https://api.w.org/featuredmedia');
			    //$data->remove_link('https://api.w.org/attachment');
			    $data->remove_link('https://api.w.org/term');
			    $data->remove_link('curies');
				
				return $data;
			}, 100, 3);
			
			add_filter('rest_prepare_product_cat', function($data, $post, $context){
				
				unset($data->data["link"]);
				unset($data->data["_links"]);
				
				remove_filter('the_content', 'prepend_attachment');
				$data->data["description"] = apply_filters(
					'the_content',
					$data->data["description"]
				);
				
				return $data;
			}, 100, 3);
			
			register_rest_field('product_cat', 'thumbnail', array(
				'get_callback' => function($term_data){
					$id = get_term_meta($term_data['id'], 'thumbnail_id', true);
					
					$mediaRestController = new WP_REST_Attachments_Controller("media");
					
					return $id ? ($mediaRestController->prepare_item_for_response(get_post($id), new WP_REST_Request()))->data : null;
				},
				'schema' => array(
					'description' => __('The thumbnail id of the term.', 'b4st'),
					'type' => 'array',
					'context' => array('view'),
				)
			));
			
			register_rest_field('product_cat', 'short_description', array(
				'get_callback' => function($term_data){
					return get_field("description", $term_data["taxonomy"] . "_" . $term_data["id"]);
				},
				'schema' => array(
					'description' => __('The seo description of the term.', 'b4st'),
					'type' => 'array',
					'context' => array('view'),
				)
			));
			
			register_rest_field('product_cat', 'excerpt', array(
				'get_callback' => function($term_data){
					return get_field("excerpt", $term_data["taxonomy"] . "_" . $term_data["id"]);
				},
				'schema' => array(
					'description' => __('An excerpt of content.', 'b4st'),
					'type' => 'array',
					'context' => array('view'),
				)
			));
			
			register_rest_field('product_cat', 'links', array(
				'get_callback' => function($term_data){
					$links = get_field("links", $term_data["taxonomy"] . "_" . $term_data["id"]);
					
					if(empty($links)){
						return array();
					}
					
					foreach($links as $key => $link){
						$links[$key]["type"] = $link["acf_fc_layout"];
						unset($links[$key]["acf_fc_layout"]);
						
						if($links[$key]["type"] == "pdf"){
							$links[$key] = array(
								"title" => $links[$key]["file"]["title"],
								"url" => $links[$key]["file"]["url"],
								"type" => $links[$key]["type"]
							);
						}
					}
					
					return $links;
				},
				'schema' => array(
					'description' => __('An excerpt of content.', 'b4st'),
					'type' => 'array',
					'context' => array('view'),
				)
			));
			
			$post_type_name = 'product_variation';
			if( isset( $wp_post_types[ $post_type_name ] ) ) {
				$wp_post_types[$post_type_name]->show_in_rest = true;
				// Optionally customize the rest_base or controller class
				$wp_post_types[$post_type_name]->rest_base = $post_type_name;
				$wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
			}
		}
		
		public function init_routes(){
			
			
			register_rest_route('hfag', '/login', array(
				'methods' => 'POST',
				'callback' => array($this, 'post_login')
			));
			register_rest_route('hfag', '/register', array(
				'methods' => 'POST',
				'callback' => array($this, 'post_register')
			));
			
			register_rest_route('hfag', '/suggestions', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_suggestions'),
				'args' => array(
					'query' => array(
						'required' => true
					)
				)
			));
			
			register_rest_route('hfag', '/more-suggestions', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_more_suggestions'),
				'args' => array(
					'query' => array(
						'required' => true
					)
				)
			));
			
			register_rest_route('hfag', '/product-categories', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_product_categories')
			));
			
			register_rest_route('hfag', '/product', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_product')
			));
			
			register_rest_route('hfag', '/products-simple', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_products_simple')
			));
			
			register_rest_route('hfag', '/product-variations', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_product_variations')
			));
			
			register_rest_route('hfag', '/product-attributes', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_product_attributes')
			));
			
			register_rest_route('hfag', '/product-discount', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_product_discount')
			));
			
			register_rest_route('hfag', '/shopping-cart', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_shopping_cart')
			));
			
			register_rest_route('hfag', '/shopping-cart', array(
				'methods' => 'POST',
				'callback' => array($this, 'post_shopping_cart')
			));
			
			register_rest_route('hfag', '/shopping-cart', array(
				'methods' => 'PUT',
				'callback' => array($this, 'put_shopping_cart')
			));
			
			register_rest_route('hfag', '/submit-order', array(
				'methods' => 'POST',
				'callback' => array($this, 'post_submit_order')
			));
			
			register_rest_route('hfag', '/countries', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_countries')
			));
			
			register_rest_route('hfag', '/sales', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_sales')
			));
			
			register_rest_route('hfag', '/user-account', array(
				'methods' => 'PUT',
				'callback' => array($this, 'put_user_account'),
				'args' => array(
					'firstName' => array(
						'required' => true
					),
					'lastName' => array(
						'required' => true
					),
					'email' => array(
						'required' => true
					),
					'password' => array(
						'required' => false
					),
					'newPassword' => array(
						'required' => false
					)
				)
			));
			
			register_rest_route('hfag', '/user-account', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_user_account')
			));
			
			register_rest_route('hfag', '/user-address', array(
				'methods' => 'PUT',
				'callback' => array($this, 'put_user_address'),
				'args' => array(
					'address' => array(
						'required' => true,
						'validate_callback' => function($param, $request, $key){
							
						}
					),
					'type' => array(
						'required' => true,
						'validate_callback' => function($param, $request, $key){
							return in_array($param, array("billing", "shipping"));
						}
					),
				)
			));
			
			register_rest_route('hfag', '/user-orders', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_user_orders')
			));
			
			register_rest_route('hfag', '/user-order', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_user_order'),
				'args' => array(
					'orderId' => array(
						'required' => true
					),
					'format' => array(
						'required' => true,
						'validate_callback' => function($param, $request, $key){
							return $param === "html";
						}
					)
				)
			));
		}
		
		public function enable_product_cat_orderby($params){
			$params['orderby']['enum'][] = 'menu_order';
			return $params;
		}
		
		public function validate_address($address, $type = "billing"){
			$countries_obj = new WC_Countries();
			$country_map = $countries_obj->get_allowed_countries();
			
			
			$errors = array();
			$validatedAddress = array();
			
			$required_keys = array(
				"first_name",
				"last_name",
				"country",
				"state",
				"address_1",
				"postcode",
				"city"
			);
			$all_keys = array_merge($required_keys, array(
				"additional_line_above",
				"description",
				"company",
				"post_office_box"
			));
			
			if($type === "billing"){
				$required_keys[] = "phone";
				$required_keys[] = "email";
			}
			
			foreach($required_keys as $required_key){
				if(empty($address[$required_key])){
					$errors[] = "The $type $required_key is required!";
				}
				
				if($required_key === "country"){
					if(in_array($address[$required_key], array_keys($country_map))){
						$validatedAddress[$required_key] = $country_map[$address[$required_key]];
					}else{
						$errors[] = "The $type $required_key has to be one of the following: " . implode(",", array_keys($country_map));
					}
					
				}else if($required_key === "state"){
					
					if(empty($address["country"])){
						$errors[] = "The $type country is required!";
					}
					
					$state_map = $countries_obj->get_states($address["country"]);
					foreach($state_map as $key => $state){
						$state_map[$key] = urldecode($state_map[$key]);
					}
					
					$flipped_state = array_flip($state_map);
					
					if(!$state_map){
						$validatedAddress[$required_key] = "-";
					}else if(in_array($address[$required_key], array_keys($state_map))){
						$validatedAddress[$required_key] = $state_map[$address[$required_key]];
					}else if(in_array($address[$required_key], array_keys($flipped_state))){
						$validatedAddress[$required_key] = $address[$required_key];
					}else{
						
						$errors[] = "The $type $required_key has to be one of the followings: " . implode(",", array_keys($state_map)) . " but is '" . $validatedAddress[$required_key]."'";
					}
				}else{
					$validatedAddress[$required_key] = $address[$required_key];
				}
				
			}
			
			foreach($all_keys as $key){
				if(isset($address[$key])){
					$validatedAddress[$key] = $address[$key];
				}
			}
			
			return array("errors" => $errors, "address" => $validatedAddress);
		}
		
		public function get_account_from_customer($customer){
			$account = array();
			
			global $hfag_discount;
			
			$account["id"] = $customer->get_id();
			$account["first_name"] = $customer->get_first_name("rest-api");
			$account["last_name"] = $customer->get_last_name("rest-api");
			$account["email"] = $customer->get_email("rest-api");
			$account["role"] = $customer->get_role("rest-api");
			$account["billing"] = apply_filters(
				'woocommerce_my_account_my_address_formatted_address',
				$customer->get_billing("rest-api"),
				$customer->get_id(),
				"billing"
			);
			$account["shipping"] = apply_filters(
				'woocommerce_my_account_my_address_formatted_address',
				$customer->get_shipping("rest-api"),
				$customer->get_id(),
				"shipping"
			);
			$account["created"] = $customer->get_date_created("rest-api");
			
			$account["discount"] = $hfag_discount->get_reseller_discount_by_user($customer->get_id());
			
			return $account;
		}
		
		/*public function post_wc_callback(WP_REST_Request $request){
			//request from our server to send us the credentials
			
			$credentials = get_option('hfag-wc-oauth-credentials');
			$credentials = $credentials ? $credentials : [];
			
			$credentials[] = array(
				"key_id" => $request["key_id"],
				"user_id" => $request["user_id"],
				"consumer_key" => $request["consumer_key"],
				"consumer_secret" => $request["consumer_secret"],
				"key_permissions" => $request["key_permissions"],
				"timestamp" => time()
			);
			
			update_option('hfag-wc-oauth-credentials', $credentials);
			
			return rest_ensure_response(array("status" => "success"));
		}
		
		public function get_wc_callback(WP_REST_Request $request){
			
			$credentials = get_option('hfag-wc-oauth-credentials');
			$credentials = $credentials ? $credentials : array();
			$needed_credentials = false;
			$filteredCredentials = array();
			
			foreach($credentials as $credentialsElement){
				if($credentialsElement['timestamp'] + 30 < time()){
					continue;
				}
				
				if($credentialsElement['user_id'] === $request['user_id']){
					$needed_credentials = $credentialsElement;
					continue;
				}
				
				$filteredCredentials[] = $credentialsElement;
			}
			
			update_option('hfag-wc-oauth-credentials', $filteredCredentials, 'no');
			
			if(!$needed_credentials){
				return new WP_Error(400, 'Transaction invalid, please try again!');
			}
			
			header(
				"Location: " .
				$this->callback_map[$request['environment']] .
				"?" . http_build_query(array(
					'consumerKey' => $needed_credentials['consumer_key'],
					'consumerSecret' => $needed_credentials['consumer_secret']
				))
			);
		}*/
		
		public function post_login(WP_REST_Request $request){
			$signon = wp_signon(array(
				"user_login" => $request["username"],
				"user_password" => $request["password"],
				"remember" => $request["remember"] ? $request["password"] : false
			));
			
			$success = false;
			$code = "";
			$account = array();
			
			if(!is_wp_error($signon)){
				wp_set_auth_cookie($signon->ID, $request["remember"] ? $request["remember"] : false, true);
				$success = true;
				
				$account = $this->get_account_from_customer(new WC_Customer($signon->ID));
			}else{
				$code = $signon->get_error_code();
			}
			
			return rest_ensure_response(array(
				"success" => $success,
				"code" => $code,
				"account" => is_wp_error($signon) ? null : $account
			));
		}
		
		public function post_register(WP_REST_Request $request){
			$userId = wp_create_user($request["username"], $request["password"], $request["username"]);
			$success = false;
			$code = "";
			
			if(!is_wp_error($userId)){
				//wp_new_user_notification($userId, null, 'user');

				$u = new WP_User($userId);
				if($u != null){
					$u->set_role('customer');
				}
				
				$wc = new WC_Emails();
				$wc->customer_new_account($userId);
				
				wp_set_auth_cookie($userId, false, true);

				update_user_meta($userId, "billing_email", $request["username"]);

				$success = true;
			}else{
				$code = $userId->get_error_code();
			}
			
			return rest_ensure_response(array(
				"success" => $success,
				"code" => $code
			));
		}
		
		public function get_product(WP_REST_Request $request){
			global $hfag_discount;
			
			$productObject = get_page_by_path($request["productSlug"], OBJECT, 'product');
			$variable = new WC_Product_Variable($productObject->ID);
			$postRestController = new WP_REST_Posts_Controller("product");
			
			$variations = $this->get_product_variations($request)->data;
			$attributes = $this->get_product_attributes($request)->data;
			$discount = $this->get_product_discount($request)->data;
			$product = $postRestController->prepare_item_for_response(get_post($productObject->ID), $request)->data;
			$product['discount'] = $discount;
			$product['sku'] = $variable->get_sku();
			
			$fields = get_field("fields", $productObject->ID);
			$product['fields'] = $fields ? $fields : array();
			$product['galleryImageIds'] = $variable->get_gallery_image_ids();
			$product['crossSellIds'] = $variable->get_cross_sell_ids();
			
			$description = get_field("description", $productObject->ID);
			$product['description'] = $description ? $description : "";
			
			unset($product["excerpt"]);
			unset($product["date"]);
			unset($product["date_gmt"]);
			unset($product["guid"]);
			unset($product["modified"]);
			unset($product["modified_gmt"]);
			unset($product["link"]);
			unset($product["type"]);
			unset($product["content"]["protected"]);
			unset($product["status"]);
			unset($product["comment_status"]);
			unset($product["ping_status"]);
			unset($product["template"]);
			unset($product["meta"]);
			
			
			return rest_ensure_response(
				array(
					"product" => $product,
					"variations" => $variations,
					"attributes" => $attributes
				)
			);
		}
		
		public function get_products_simple(WP_REST_Request $request){
			
			//$user_id = wp_validate_auth_cookie('', 'logged_in');
			
			/*if(!$user_id){
				return rest_ensure_response(array(
					"errors" => array(
						"You have to be logged in to perform this action"
					),
					"products" => null,
					"success" => false
				));
			}*/
			
			//wp_set_current_user($user_id);
			//$this->cache_product_json();
			$json = json_decode(file_get_contents(get_template_directory() . "/cache/products.json"));
			
			if(empty($json)){
				return rest_ensure_response(array(
					"errors" => array(
						"Data not cached!"
					),
					"products" => null,
					"success" => false
				));
			}
			
			/*$reseller_discount = $hfag_discount->get_reseller_discount_by_user();
			
			foreach($json as $key => $product){
				if(!empty($reseller_discount[$product->id])){
					$json[$key]->discount->reseller = $reseller_discount[$product->id];
				}
			}*/
			
			return rest_ensure_response(
				array(
					"errors" => array(),
					"products" => $json,
					"success" => true
				)
			);
		}
		
		public function get_product_variations(WP_REST_Request $request){
			$productObject = get_page_by_path($request["productSlug"], OBJECT, 'product');
			$variable = new WC_Product_Variable($productObject->ID);
			
			$mediaRestController = new WP_REST_Attachments_Controller("media");
			
			$variable = new WC_Product_Variable($productObject->ID);
			$variations = $variable->get_available_variations();
			
			
			return rest_ensure_response(
				array_map(
					function($item) use ($mediaRestController, $request){
						$image = $mediaRestController->prepare_item_for_response(get_post($item['image_id']), $request)->data;
						
						unset($image["date"]);
						unset($image["date_gmt"]);
						unset($image["guid"]);
						unset($image["modified"]);
						unset($image["modified_gmt"]);
						unset($image["status"]);
						unset($image["type"]);
						unset($image["link"]);
						unset($image["template"]);
						unset($image["post"]);
						
						$item['image'] = $image;
						return $item;
					},
					$variations
				)
			);
		}
		
		public function get_product_attributes(WP_REST_Request $request){
			$productObject = get_page_by_path($request["productSlug"], OBJECT, 'product');
			$variable = new WC_Product_Variable($productObject->ID);
			
			$attributeMap = array_map(
				function($attribute) use ($variable){
					
					$data = $attribute->get_data();
					$taxonomy = $data["name"];
					
					if($data["id"] == 0){
						$data["id"] = sanitize_title($taxonomy);
					}
					
					if($data["is_taxonomy"]){
						
						$data["name"] = get_taxonomy( $taxonomy )->labels->singular_name;
						
						$data["options"] = array_map(
							function($termId) use ($taxonomy){
								return get_term($termId, $taxonomy);
							},
							$data["options"]
						);
					}
					
					return $data;
				},
				$variable->get_attributes()
			);
			
			return rest_ensure_response(
				array_map(
					function($attributeKey) use ($attributeMap){
						$attributeMap[$attributeKey]["slug"] = $attributeKey;
						return $attributeMap[$attributeKey];
					},
					array_keys($attributeMap)
				)
			);
		}
		
		public function get_product_discount(WP_REST_Request $request){
			global $hfag_discount;
			
			$productObject = get_page_by_path($request["productSlug"], OBJECT, 'product');
			$variable = new WC_Product_Variable($productObject->ID);
			
			$reseller_discount_enabled = $hfag_discount->reseller_discount_enabled($variable);
			
			
			if($reseller_discount_enabled){
				$reseller_discount = $hfag_discount->get_reseller_discount_by_user();
				
				return rest_ensure_response(
					array(
						"bulk_discount" => array(),
						"reseller_discount" => $reseller_discount
					)
				);
			}
			
			$bulk_discount_enabled = $hfag_discount->bulk_discount_enabled($variable);
			
			
			if($bulk_discount_enabled){
				$bulk_discount = $hfag_discount->get_discount_coeffs($variable);
				
				return rest_ensure_response(
					array(
						"bulk_discount" => $bulk_discount,
						"reseller_discount" => array()
					)
				);
			}
			
			return rest_ensure_response(
				array(
					"bulk_discount" => array(),
					"reseller_discount" => array()
				)
			);
		}
		
		public function get_shopping_cart(WP_REST_Request $request){
			
			global $hfag_discount;
			
			$user_id = wp_validate_auth_cookie('', 'logged_in');
			wp_set_current_user($user_id);
			
			$this->loadCart();
			
			$items = array();
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$item = array();
				
				if($hfag_discount->reseller_discount_enabled($cart_item['data'])){
					$cart_item['data']->set_price($hfag_discount->reseller_discount_get_price($cart_item['data']));
				}else if($cart_item['data']->is_on_sale()){
					$cart_item['data']->set_price($cart_item['data']->get_sale_price());
				}else if($hfag_discount->bulk_discount_enabled($cart_item['data'])){
					$cart_item['data']->set_price($hfag_discount->bulk_discount_get_price($cart_item['data'], $cart_item['quantity']));
				}
				
				$product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
				$item["id"] = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
				$item["key"] = $cart_item_key;
				$item["title"] = apply_filters( 'woocommerce_cart_item_name', $product->get_title(), $cart_item, $cart_item_key);
				$item["thumbnailId"] = intval($product->get_image_id());
				$item["attributes"] = wc_get_formatted_cart_item_data($cart_item, true);
				$item["sku"] = $product->get_sku();
				
				if(is_a($product, "WC_Product_Variation")){	
					$_product = new WC_Product_Variation($product->variation_id);
				}else{
					$_product = wc_get_product($product->id);
				}

				
				$item["price"] = floatval($_product->get_regular_price());
				if($product->get_price() != $item["price"]){
					$item["discountPrice"] = floatval($product->get_price());	
				}
				$item["minQuantity"] = apply_filters('woocommerce_quantity_input_min', 0, isset($_product->parent) ? $product->parent : $product);
				$item["quantity"] = intval($cart_item['quantity']);
				
				$item["subtotal"] = floatval(
										preg_replace(
											"/[^0-9\.]/",
											'',
											html_entity_decode(
												strip_tags(
													WC()->cart->get_product_subtotal(
														$product,
														$cart_item['quantity']
													)
												)
											)
										)
									);
				
				
				$items[] = $item;
			}
			
			WC()->cart->calculate_totals();
			WC()->cart->calculate_shipping();
			
			return rest_ensure_response(
				array(
					"total" => floatval(WC()->cart->total),
					"shipping" => floatval(WC()->cart->shipping_total),
					"fees" => array_map(function($fee){
						return array(
							"name" => $fee->name,
							"amount" => WC()->cart->display_prices_including_tax() ? $fee->total + $fee->tax : $fee->total
						);
					}, WC()->cart->get_fees()),
					"taxes" => array_map(function($tax){
						return array(
							"label" => $tax->label,
							"amount" => $tax->amount
						);
					}, array_values(WC()->cart->get_tax_totals())),
					"items" => $items,
					"loggedIn" => $user_id ? true : false
				)
			);
		}
		
		public function post_shopping_cart(WP_REST_Request $request){
			
			$user_id = wp_validate_auth_cookie('', 'logged_in');
			wp_set_current_user($user_id);
			
			$this->loadCart();
			
			$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($request['product_id']));
			$product = new WC_Product_Variable($product_id);
			$variation_id = $request['variation_id'];
			$variation  = $request['variation'];
			
			$quantity = empty($request['quantity']) ? 1 : apply_filters( 'woocommerce_stock_amount', $request['quantity']);
			
			remove_filter_unkown_instance('woocommerce_add_to_cart_validation', 'Wcff_ProductFields', 'fields_validator', 99);
			
			$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
			$json = array();
			$add = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);
			if ($passed_validation && $add){
				$json = $this->get_shopping_cart($request)->data;
				$json["upsellIds"] = $product->get_upsell_ids();
			}else{
				$json["error"] = true;
				wc_print_notices();
			}
			
			return rest_ensure_response($json);
		}
		
		public function put_shopping_cart(WP_REST_Request $request){
			
			$user_id = wp_validate_auth_cookie('', 'logged_in');
			wp_set_current_user($user_id);
			
			$items = $request["items"];
			
			$this->loadCart();
			
			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item){
				$found = false;
				foreach($items as $key => $item){
					if($item["key"] === $cart_item_key){
						if($item["quantity"] <= 0){
							WC()->cart->remove_cart_item($cart_item_key);
							$found = true;
							unset($items[$key]);
							break;
						}else {
							WC()->cart->set_quantity($cart_item_key, ceil($item["quantity"]));
							$found = true;
							unset($items[$key]);
							break;	
						}
					}
				}
				if(!$found){
					WC()->cart->remove_cart_item($cart_item_key);
				}
			}
			
			foreach($items as $item){
				$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($item['product_id']));
				$variation_id = $item['variation_id'];
				$variation  = $item['variation'];
				
				$quantity = empty($item['quantity']) ? 1 : apply_filters( 'woocommerce_stock_amount', $item['quantity']);
				
				$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
				
				if (!$passed_validation || !WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation)){
					return rest_ensure_response("ERROR " . $passed_validation);
				}
			}
			
			return rest_ensure_response($this->get_shopping_cart($request));
		}
		
		public function post_submit_order(WP_REST_Request $request){
			
			global $hfag_discount;
			
			$this->loadCart();
			
			$user_id = wp_validate_auth_cookie('', 'logged_in');
			wp_set_current_user($user_id);
			
			$billingValidation = $this->validate_address($request["billingAddress"], "billing");
			$shippingValidation = null;
			
			if(!empty($request["shippingAddress"]["first_name"])){
				$shippingValidation = $this->validate_address($request["shippingAddress"], "shipping");
			}
			
			$errors = $shippingValidation === null ?
				$billingValidation["errors"] :
					array_merge(
						$billingValidation["errors"],
						$shippingValidation["errors"]
					);
			
			$billingAddress = $billingValidation["address"];
			$shippingAddress = empty($shippingValidation) ?
				$billingAddress :
				$shippingValidation["address"];
				
			unset($shippingAddress["phone"]);
			unset($shippingAddress["email"]);
			
			$comments = $request["comments"];
			
			if(!empty($errors)){
				return rest_ensure_response(array(
					"success" => false,
					"errors" => $errors
				));
			}
			
			$today = gmdate("Y-m-d\TH:i:s\Z");
			
			$cart = WC()->cart;
			$checkout = WC()->checkout();
			
			$order_id = $checkout->create_order(array());
			$order = wc_get_order($order_id);
			update_post_meta($order_id, '_customer_user', get_current_user_id());
			$order->set_created_via("REST API");
			$order->set_payment_method("feuerschutz_invoice");
			$order->set_payment_method_title("Rechnung");
			$order->set_customer_ip_address($_SERVER['REMOTE_ADDR']);
			$order->set_customer_user_agent($_SERVER['HTTP_USER_AGENT']);
			$order->set_date_completed($today);
			
			if($user_id){
				$order->set_customer_id($user_id);	
			}
			
			$order->set_address($billingAddress, "billing");
			$order->set_address($shippingAddress, "shipping");
			
			if($order->get_shipping_method() === ""){
				$shipping = new WC_Order_Item_Shipping();
				$shipping->set_name("Lieferkosten");
				$shipping->set_method_title("Versandkostenpauschale");
				$shipping->set_method_id("flat_rate");
				$shipping->set_total(12.50);
				
				$order->add_item($shipping);
			}
			
			//make life a little easier
			$order->set_customer_note(
				$comments . 
				($hfag_discount->check_if_reseller_discounts($user_id) ? "<br/><br/>---<br/>" . "Bestellung durch Wiederverkäufer" : "")
			);
			$order->save();
			
			$order->calculate_shipping();
			$order->calculate_totals();
			
			$order->payment_complete(); 
			$order->update_status('completed');
			
			WC()->cart->empty_cart();
			WC()->session->set('cart', array());
						
			return rest_ensure_response(array(
				"success" => true,
				"errors" => array(),
				"data" => array(
					"transactionId" => $order->get_id(),
					"total" => $order->get_total()
				)
			));
		}
		
		public function get_suggestions(WP_REST_Request $request){
			
			$sections = array();
				
			$posts = relevanssi_do_query(
				new WP_Query(
					array(
						'post_type' => array('product', 'product_variation'),
						's' => $request['query'],
						'post_status' => 'publish',
						'posts_per_page' => 10
					)
				)
			);
			
			
			$products = array();
			$variations = array();
	
			foreach($posts as $post){
				
				$product = wc_get_product($post->ID);
				
				if($post->post_type === "product"){
					$variation_count = count($product->get_available_variations());
					
					$products[] = array(
						"id" => $post->ID,
						"slug" => $post->post_name,
						"title" => $post->post_title,
						"variations" => $variation_count,
						"sku" => $product->get_sku(),
						"price" => $product->get_price(),
						"type" => "product"
					);
				}else{
					$parent = get_post($product->get_parent_id());
					
					$variations[] = array(
						"id" => $post->ID,
						"parent_slug" => $parent->post_name,
						"title" => $post->post_title,
						"price" => $product->get_price(),
						"sku" => $product->get_sku(),
						"type" => "product_variation"
					);
				}
	
				/*$meta = wp_get_attachment_metadata(get_post_thumbnail_id($post->ID));
				
				if(empty($meta)){
					return;
				}*/
			}
			
			$sections[] = array(
				'title' => __("Products", "woocommerce"),
				'suggestions' => $products
			);
			
			$sections[] = array(
				'title' => __("Product Variations", "b4st"),
				'suggestions' => $variations
			);
			
			$search_terms = get_terms(array(
				"taxonomy" => "product_cat",
				"hide_empty" => true,
				"fields" => "all",
				"name__like" => $request["query"]
			));
			
			$terms = array();
			
			foreach($search_terms as $term){
				$terms[] = array(
					"id" => $term->ID,
					"slug" => $term->slug,
					"title" => $term->name,
					"count" => $term->count,
					"type" => "taxonomy"
				);
			}
			
			$sections[] = array(
				'title' => __("Categories", "woocommerce"),
				'suggestions' => $terms
			);
			
			return rest_ensure_response($sections);
		}
		
		public function get_product_categories(WP_REST_Request $request){
			$args = array(
				'taxonomy' => 'product_cat',
				'hide_empty' => true,
				'fields' => 'all_with_object_id'
			);
	
			return rest_ensure_response(
				array_map(
					function($category) {
						return array(
							'id' => $category->term_id,
							'title' => $category->name,
							'parent' => $category->parent
						);
					},
					get_terms($args)
				)
			);
		}
		
		public function get_countries(WP_REST_Request $request){
			$countries_obj = new WC_Countries();
			$country_list = $countries_obj->get_allowed_countries();
			
			$default_country = $countries_obj->get_base_country();
			$default_county_states = $countries_obj->get_states($default_country);
			
			$countries = array();
			
			foreach($country_list as $key => $name){
				$countries[$key] = array(
					"name" => $name,
					"states" => $countries_obj->get_states($key)
				);
			}
			
			return rest_ensure_response($countries);
		}
		
		public function get_sales(WP_REST_Request $request){
			
			$mediaRestController = new WP_REST_Attachments_Controller("media");
			$postRestController = new WP_REST_Posts_Controller("product");
			
			$sales = array();
			$products = array();
			
			foreach(wc_get_product_ids_on_sale() as $productId){
				$product = wc_get_product($productId);
				
				if(!is_a($product, "WC_Product_Variation")){
					$p = $postRestController->prepare_item_for_response(get_post($productId), $request)->data;
					$thumbnailId = $p["featured_media"];
					$p["thumbnail"] = $thumbnailId ? $mediaRestController->prepare_item_for_response(get_post($thumbnailId), new WP_REST_Request())->data : null;
					$products[] = $p;
				}
						
				if(is_a($product, "WC_Product_Variable")){
					continue; //skip variables
				}
				
				$parent = $product->get_parent_id();
				$isVariation = $parent != 0;
				
				$sales_price_to = get_post_meta($productId, '_sale_price_dates_to', true);
				
				$sales[] = array(
					"productId" => $isVariation ? $parent : $productId,
					"variationId" => $isVariation ? $productId : null,
					"price" => $product->get_regular_price(),
					"salePrice" => $product->get_sale_price(),
					"saleEnd" => date("U", $sales_price_to)
				);
			}
			
			$posts = array();
			$sticky = get_option('sticky_posts');
			
			$query = new WP_Query(
				array(
					"post_type" => "post",
					"post_status" => "publish",
					"post__in" => $sticky
				)
			);
			
			foreach($query->posts as $post){
				$thumbnailId = get_post_thumbnail_id($post->ID);
					
				$posts[] = array(
					"id" => $post->ID,
					"slug" => $post->post_name,
					"title" => $post->post_title,
					"thumbnail" => $thumbnailId ? ($mediaRestController->prepare_item_for_response(get_post($thumbnailId), new WP_REST_Request()))->data : null,
					"description" => get_field("description", $post->ID)
				);
			}
			
			return rest_ensure_response(array(
				"products" => $products,
				"sales" => $sales,
				"posts" => $posts
			));
		}
		
		public function put_user_account(WP_REST_Request $request){
			$user_id = wp_validate_auth_cookie('', 'logged_in');
			
			if(!$user_id){
				return rest_ensure_response(array(
					"errors" => array(
						"You have to be logged in to perform this action"
					),
					"account" => null,
					"success" => false
				));
			}
			
			$userdata = get_user_by('id', $user_id);
			$updateData = array(
				'ID' => $userdata->ID,
				'first_name' => $request["firstName"],
				'last_name' => $request["lastName"]
			);
			
			if(!empty($request["password"]) && !empty($request["newPassword"])){
				$result = wp_check_password($request["password"], $userdata->user_pass, $userdata->ID);
			
				if($result === true){
					//Update the user's password
					$updateData["user_pass"] = $request["newPassword"];
				}else{
					return rest_ensure_response(array(
						"errors" => array(
							"The password doesn't match!"
						),
						"account" => null,
						"success" => false
					));
				}
			}
			
			if(!empty($request["email"]) &&$request["email"] !== $userdata->user_email){
				//Update the user's email
				$updateData["user_email"] = $request["email"];
			}
			
			$update = wp_update_user($updateData);
			
			if(is_wp_error($update)){
				return rest_ensure_response(array(
					"errors" => $update->get_error_messages(),
					"account" => null,
					"success" => false
				));
			}
			
			return rest_ensure_response(array(
				"errors" => array(),
				"account" => $this->get_account_from_customer(new WC_Customer($userdata->ID)),
				"success" => true
			));
		}
		
		public function get_user_account(WP_REST_Request $request){
			$user_id = wp_validate_auth_cookie('', 'logged_in');
			if(!$user_id){
				
				return rest_ensure_response(array(
					"errors" => array(
						"You have to be logged in to perform this action"
					),
					"account" => null,
					"success" => false
				));
			}
			
			return rest_ensure_response(array(
				"errors" => array(),
				"account" => $this->get_account_from_customer(new WC_Customer($user_id)),
				"success" => true
			));
		}
		
		public function put_user_address(WP_REST_Request $request){
			$user_id = wp_validate_auth_cookie('', 'logged_in');
			if(!$user_id){
				
				return rest_ensure_response(array(
					"errors" => array(
						"You have to be logged in to perform this action"
					),
					"account" => null,
					"success" => false
				));
			}
			
			$validatedAddress = $this->validate_address($request["address"], $request["type"]);
			
			if(!empty($validatedAddress["errors"])){
				
				return rest_ensure_response(array(
					"errors" => $validatedAddress["errors"],
					"account" => null,
					"success" => false
				));
			}
			
			foreach($validatedAddress["address"] as $key => $value){
				update_user_meta($user_id, $request["type"] . "_" . $key, $value);
				//var_dump("userId" . $user_id . ": " . $request["type"] . "_" . $key . "=>" . $value);
			}

			if(
				$request["type"] === "billing" &&
				!empty($validatedAddress["address"]["first_name"]) &&
				!empty($validatedAddress["address"]["last_name"])
			){
				wp_update_user(
					array(
						'ID' => $user_id,
						'first_name' => $validatedAddress["address"]["first_name"],
						'last_name' => $validatedAddress["address"]["last_name"],
						'display_name' => $validatedAddress["address"]["first_name"] . " " . $validatedAddress["address"]["last_name"]
					)
				);
			}
			
			return rest_ensure_response(array(
				"errors" => array(),
				"account" => $this->get_account_from_customer(new WC_Customer($user_id)),
				"success" => false
			));
		}
		
		public function get_user_orders(WP_REST_Request $request){
			$user_id = wp_validate_auth_cookie('', 'logged_in');
			if(!$user_id){
				
				return rest_ensure_response(array(
					"errors" => array(
						"You have to be logged in to perform this action"
					),
					"orders" => array(),
					"success" => false
				));
			}
			
			$customer_orders = get_posts(array(
			    'numberposts' => -1,
			    'meta_key'    => '_customer_user',
			    'meta_value'  => $user_id,
			    'post_type'   => wc_get_order_types(),
			    'post_status' => array_keys( wc_get_order_statuses() ),
			));
			
			return rest_ensure_response(array(
				"errors" => array(),
				"orders" => array_map(function($order){
					$order_obj = new WC_Order($order->ID);
					
					$order_items = $order_obj->get_items();
					$items = array();
	
					foreach($order_items as $order_item){
						
						$product = null;
						if(!empty($order_item['variation_id'])){
							$product = new WC_Product_Variation($order_item['variation_id']);
						}else{
							$product = new WC_Product($order_item['product_id']);
						}
						
						remove_all_filters("woocommerce_order_item_display_meta_key");
						remove_all_filters("woocommerce_order_item_display_meta_value");
						remove_all_filters("woocommerce_order_item_get_formatted_meta_data");
						
						add_filter("woocommerce_order_item_get_formatted_meta_data", function($meta){
							foreach($meta as $key => $value){
								$meta[$key]->display_value = strip_tags($value->display_value);
							}
							return $meta;
						}, 10);
						
						$items[] = array(
							"id" => $order_item['product_id'],
							"variationId" => $order_item['variation_id'],
							"name" => $order_item['name'],
							"sku" => $product->get_sku(),
							"quantity" => $order_item['qty'],
							"attributes" => $order_item['variation_id'] ? $order_item->get_formatted_meta_data() : array()
						);
					}
					return array(
						"id" => $order->ID,
						"title" => $order->post_title,
						"billing" => $order_obj->get_address("billing"),
						"shipping" => $order_obj->get_address("shipping"),
						"comment" => $order_obj->get_customer_note(),
						"total" => floatval($order_obj->get_total()),
						"items" => $items,
						"status" => $order->post_status,
						"created" => strtotime($order->post_date)
					);
				}, $customer_orders),
				"success" => true
			));
		}
		
		public function get_user_order(WP_REST_Request $request){
			
			$user_id = wp_validate_auth_cookie('', 'logged_in');
			if(!$user_id){
				
				return rest_ensure_response(array(
					"errors" => array(
						"You have to be logged in to perform this action"
					),
					"orders" => array(),
					"success" => false
				));
			}
			
			add_filter( 'woocommerce_email_recipient_customer_invoice' , function(){
				return '';
			}, 10, 2);
			
			add_filter('woocommerce_email_enabled_customer_invoice', '__return_false', 1, 2);
			
			if( isset( $GLOBALS['wc_advanced_notifications'] ) ) {
				unset( $GLOBALS['wc_advanced_notifications'] );
			}
			
			$order = new WC_Order($request["orderId"]);
			
			if($order->get_customer_id() !== $user_id){
				return rest_ensure_response(array(
					"errors" => array(
						"Access denied"
					),
					"orders" => array(),
					"success" => false
				));
			}
			
			$new_email = new WC_Email_Customer_Invoice();
			$new_email->trigger($request["orderId"]);
			
			$html = apply_filters( 'woocommerce_mail_content', $new_email->style_inline($new_email->get_content()));
			$html = str_replace(
				"</head>",
				"<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/3.5.8/iframeResizer.contentWindow.min.js'></script>" . 
				"</head>",
				$html
			);
			return $html;
		}
		
		public function pre_serve_request($served, $result, $request, $server){
			
			switch ( $request['format'] ) {
				case 'html':
					header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
					echo $result->data;
					$served = true; // tells the WP-API that we sent the response already
					
					break;
			}
			
			return $served;
		}
		
		public function generate_product_json(){
			global $hfag_discount;
			
			$simpleProducts = array();
			
			$products = wc_get_products(
				array(
					"limit" => -1,
					"status" => "publish"
				)
			);
			
			foreach($products as $product){
				if(is_a($product, WC_Product_Variable)){
					
					$variations = $product->get_available_variations();
					
					$bulk_discount_enabled = $hfag_discount->bulk_discount_enabled($product);
					$bulk_discount = array();
					
					if($bulk_discount_enabled){
						$bulk_discount = $hfag_discount->get_discount_coeffs($product);
					}
					
					$attributeMap = array_map(
						function($attribute) use ($variable){
							$data = $attribute->get_data();
							$taxonomy = $data["name"];
							
							if($data["id"] == 0){
								$data["id"] = sanitize_title($taxonomy);
							}
							
							if($data["is_taxonomy"]){
								$data["name"] = get_taxonomy($taxonomy)->labels->singular_name;
								
								$data["options"] = array_map(
									function($termId) use ($taxonomy){
										return get_term($termId, $taxonomy);
									},
									$data["options"]
								);
							}
							
							return $data;
						},
						$product->get_attributes()
					);
					
					foreach($variations as $variation){
						$meta = array();
						
						foreach($variation["attributes"] as $attribute_key => $attribute){
							$a = $attributeMap[substr($attribute_key, 10)];
							
							if($a["is_taxonomy"]){
								$name = "";
								foreach($a["options"] as $option){
									if($option->slug === $attribute){
										$name = $option->name;
										break;
									}
								}
								$meta[$a["name"]] = $name;
							}else{
								$meta[$a["name"]] = $attribute;
							}
						}
						
						$simpleProducts[] = array(
							"slug" => $product->get_slug(),
							"variationId" => $variation["variation_id"],
							"sku" => $variation["sku"],
							"name" => trim(strip_tags($variation["variation_description"])),
							"meta" => $meta,
							"discount" => $bulk_discount_enabled ?
								array(
									"bulk" => $bulk_discount[$variation["variation_id"]]
								) :
								array(
									"bulk" => array()
								),
							"price" => "" . $variation["display_price"]
						);
					}
				}else{
					
					$discount = array(
						"bulk" => array()
					);
					
					$bulk_discount_enabled = $hfag_discount->bulk_discount_enabled($product);
					
					if($bulk_discount_enabled){
						$bulk_discount = $hfag_discount->get_discount_coeffs($product);
						
						$discount["bulk"] = $bulk_discount;
					}
					
					$simpleProducts[] = array(
						"id" => $product->get_id(),
						"sku" => $product->get_sku(),
						"name" => $product->get_name(),
						"discount" => $discount,
						"price" => floatval($product->get_price())
					);
					
				}
			}
			
			return $simpleProducts;
		}
		
		public function loadCart(){
			if(WC()->cart === null){
				wc()->frontend_includes();
				WC()->session = new WC_Session_Handler();
				WC()->session->init();
				WC()->customer = new WC_Customer( get_current_user_id(), true );
				WC()->cart = new WC_Cart();	
			}
		}
		
		public function cache_product_json(){
			set_time_limit(120);
			
			$json = $this->generate_product_json();
			file_put_contents(get_template_directory() . "/cache/products.json", json_encode($json));
		}
	}
	
	function remove_filter_unkown_instance($filter_name = '', $class_name = '', $function = '', $priority){
		global $wp_filter;
		
		if(
			!isset($wp_filter[$filter_name][$priority]) ||
			!is_array($wp_filter[$filter_name][$priority])
		){
			return false;
		}
		
		foreach($wp_filter[$filter_name][$priority] as $uid => $filter_array){
			if(isset($filter_array['function']) && is_array($filter_array['function'])){
				if(
					is_object($filter_array['function'][0]) &&
					get_class( $filter_array['function'][0]) == $class_name &&
					$filter_array['function'][1] == $function
				){
					unset($wp_filter[$filter_name]->callbacks[$priority][$uid]);
					return true;
				}
			}
		}
		return false;
	}
	
	new Hfag_Rest();
	
?>