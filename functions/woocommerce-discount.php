<?php
	
	class Hfag_Woocommerce_Discount {
		
		public function __construct(){
			add_action('woocommerce_before_calculate_totals', array($this, 'before_calculate_totals'), 10, 1);
			
			add_action('woocommerce_product_options_general_product_data', array($this, 'product_options_general_product_data'), 10);
			add_action('woocommerce_product_after_variable_attributes', array($this, 'product_after_variable_attributes'), 10, 3);
			add_action('woocommerce_process_product_meta', array($this, 'process_product_meta'), 10);
			add_action('woocommerce_save_product_variation', array($this, 'save_product_variation'), 10, 1);
		}
		
		public function check_coupons(){
			global $woocommerce;
			return (!empty($woocommerce->cart->applied_coupons));
		}
		
		public function reseller_discount_enabled($product){
	
			$discount = $this->get_reseller_discount_by_user();
			$product_id = -1;
			
			if(is_a($product, "WC_Product_Variation")){
				$product_id = apply_filters( 'wpml_object_id', $product->get_parent_id(), 'product', false, "de");
			}else{
				$product_id = apply_filters( 'wpml_object_id', $product->get_id(), 'product', false, "de");
			}
			
			return isset($discount[$product_id]);
		}
		
		public function reseller_discount_get_price($product){
			
			//is sometimes executed two times in a row
			$discountByUser = $this->get_reseller_discount_by_user();
			
			if(is_a($product, "WC_Product_Variation")){
				
				$product_id = apply_filters( 'wpml_object_id', $product->get_id(), 'product_variation', false, "de");
				
				$product	= new WC_Product_Variation($product_id);
				$price		= $product->get_price();
				
				$discount = floatval($discountByUser[$product->get_parent_id()]);
				
				return isset($discountByUser[$product->get_parent_id()]) ? ((1.0 - ($discount/100)) * $price) : $price;
				
			}else{
				
				$product_id = apply_filters( 'wpml_object_id', $product->get_id(), 'product', false, "de");
				
				$product	= wc_get_product($product_id);
				$price		= $product->get_price();
				
				$discount = floatval($discountByUser[$product->get_id()]);
				
				return isset($discountByUser[$product->get_id()]) ? ((1.0 - ($discount/100)) * $price) : $price;
			}
		}
		
		
		
		public function bulk_discount_enabled($product){
			//always get the discount from the german products
	
			if(is_a($product, "WC_Product_Variation")){
				
				$product_id = apply_filters( 'wpml_object_id', $product->get_id(), 'product', false, "de");
				
				$discount = get_post_meta($product_id, '_feuerschutz_bulk_discount', true);
				
				return empty($discount) ? false : ($discount != "[]");
			}else if($product->is_type('simple')){
				
				$product_id = apply_filters( 'wpml_object_id', $product->get_id(), 'product_variation', false, "de");
				
				$discount = get_post_meta($product_id, '_feuerschutz_bulk_discount', true);
				
				return empty($discount) ? false : ($discount != "[]");
				
			}else if($product->is_type('variable')){
				
				$product_id = apply_filters( 'wpml_object_id', $product->get_id(), 'product', false, "de");
				
				$enabled = get_post_meta($product_id, '_feuerschutz_variable_bulk_discount_enabled', true);
				
				return empty($enabled) ? false : ($enabled == '1');
			}
		}
		
		public function bulk_discount_get_price($product, $quantity = 1){
			
			//always get the discount from the german products
			$product_id = $product->get_id();
			
			if(is_a($product, "WC_Product_Variation")){
				$product_id = apply_filters( 'wpml_object_id', $product_id, 'product_variation', false, "de");
			}else{
				$product_id = apply_filters( 'wpml_object_id', $product_id, 'product', false, "de");
			}
			
			$product = wc_get_product($product_id);
			
			$discounts = $this->remove_bom(get_post_meta($product_id, '_feuerschutz_bulk_discount', true));
			$discount_price = $product->get_price();
			
			if(!empty($discounts) && $this->is_json($discounts)){
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
		
		public function get_reseller_discount_by_user($user_id = null){
			global $feuerschutz_reseller_discount;
			
			if($user_id === null){
				$user_id = get_current_user_id();
			}
			
			if(!$this->check_if_reseller_discounts($user_id)){
				return array();	
			}
			
			if(empty($feuerschutz_reseller_discount)){
				$feuerschutz_reseller_discount = array();
			}
			
			if(!empty($feuerschutz_reseller_discount[$user_id])){
				return $feuerschutz_reseller_discount[$user_id];
			}
			
			//use german values
			add_filter('acf/settings/current_language', '__return_false');
				
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
				
				if(in_array($user_id, $user_ids)){
					foreach($product_ids as $product_id){
						$feuerschutz_reseller_discount[$user_id][$product_id]	= $discount;
					}	
				}
				
			}
			//reset language
			remove_filter('acf/settings/current_language', '__return_false');
			
			return isset($feuerschutz_reseller_discount[$user_id]) ? $feuerschutz_reseller_discount[$user_id] : array();
		}
		
		public function check_if_reseller_discounts($user_id){
			
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
		
		public function before_calculate_totals($cart){
			if($this->check_coupons()){
				return;
			}
			
			//var_dump("count", count($cart->cart_contents));
			//var_dump("userid", get_current_user_id());
			
			foreach ($cart->cart_contents as $cart_item_key => $cart_item){
				
				//var_dump($cart_item['data'], $this->reseller_discount_enabled($cart_item['data']), $this->get_reseller_discount_by_user());
				
				if($this->reseller_discount_enabled($cart_item['data'])){
					$cart_item['data']->set_price($this->reseller_discount_get_price($cart_item['data']));
					
				}else if($cart_item['data']->is_on_sale()){
					$cart_item['data']->set_price($cart_item['data']->get_sale_price());
				}else if($this->bulk_discount_enabled($cart_item['data'])){
					$cart_item['data']->set_price($this->bulk_discount_get_price($cart_item['data'], $cart_item['quantity']));
				}
			}
		}
		
		public function process_product_meta($post_id){
	
			if(!empty($_POST['_feuerschutz_min_purchase_qty'])){
				update_post_meta($post_id, '_feuerschutz_min_purchase_qty', esc_attr($_POST['_feuerschutz_min_purchase_qty']));
			}
			
			$bulk_discount = stripslashes($_POST['_feuerschutz_bulk_discount']);
			if(!empty($bulk_discount) && $this->is_json($bulk_discount)){
				update_post_meta($post_id, '_feuerschutz_bulk_discount', $bulk_discount);
			}
			
		}
		
		public function save_product_variation($post_id){
			
			$variation = new WC_Product_Variation($post_id);
			$parent_product = wc_get_product($variation->get_parent_id());
			
			$bulk_discount = stripslashes($_POST['_feuerschutz_variable_bulk_discount'][$post_id]);
			if(!empty($bulk_discount) && $this->is_json($bulk_discount)){
				update_post_meta($post_id, '_feuerschutz_bulk_discount', $bulk_discount);
			}
			
			$bulk_discount_enabled = false;
			
			$variations = $parent_product->get_available_variations();
			
			foreach($variations as $variation){
				$discount_tmp = $this->remove_bom(get_post_meta($variation['variation_id'], '_feuerschutz_bulk_discount', true));
				
				if(!empty($discount_tmp) && $this->is_json($discount_tmp) && $discount_tmp != "[]"){
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
		
		
		public function remove_bom($data) {
			if (0 === strpos(bin2hex($data), 'efbbbf')) {
				return substr($data, 3);
			}
			
			return $data;
		}
		
		public function is_json($string){
			json_decode($string);
			return (json_last_error() == JSON_ERROR_NONE);
		}
		
		public function get_discount_coeffs($product){
			//return array();
			if($product->is_type('simple')){
				
				$german_id = apply_filters( 'wpml_object_id', $product->get_id(), 'product', false, "de");
				$discount = $this->remove_bom(get_post_meta($german_id, '_feuerschutz_bulk_discount', true));
				
				if(empty($discount) || !$this->is_json($discount)){
					return array();
				}
				
				$array = array();
				$array[$product->get_id()] = json_decode($discount);
				
				return $array;
				
			}else if($product->is_type('variable')){
				
				$discount = array();
				
				$variations = $product->get_available_variations();
				foreach($variations as $variation){
					//get the discount of the german equivalent
					$german_id = apply_filters( 'wpml_object_id', $variation['variation_id'], 'product_variation', false, "de");
					
					$discount_tmp = $this->remove_bom(get_post_meta($german_id, '_feuerschutz_bulk_discount', true));
					
					if(empty($discount_tmp) || !$this->is_json($discount_tmp)){
						$discount_tmp = "[]";
					}
						
					$discount[$variation['variation_id']] = (empty($discount_tmp) ? array() : json_decode($discount_tmp));
				}
				
				return $discount;
				
			}
		
		}
		
		function product_after_variable_attributes($loop, $variation_data, $variation){
			$this->bulk_discount_field($variation->ID, '_feuerschutz_variable_bulk_discount[' . $variation->ID . ']');
		}
		
		public function product_options_general_product_data(){
	
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
				$this->bulk_discount_field($post->ID, '_feuerschutz_bulk_discount');
			echo '</div>';
			
		}
		
		public function bulk_discount_field($post_id, $name){
			$id = "bulk-discount-" . $post_id;
			
			$bulk_discount = get_post_meta($post_id, '_feuerschutz_bulk_discount', true);
			
			if(empty($bulk_discount) || !$this->is_json($bulk_discount)){
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
	}
	
	global $hfag_discount;
	
	$hfag_discount = new Hfag_Woocommerce_Discount();
	
?>