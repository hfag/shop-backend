<?php
	
	class Hfag_Woocommerce_Discount {
		
		public function __construct(){
			add_action('woocommerce_before_calculate_totals', array($this, 'before_calculate_totals'), 10, 1);
			add_action('woocommerce_process_product_meta', array($this, 'process_product_meta'), 10);
			add_action('woocommerce_save_product_variation', array($this, 'save_product_variation'), 10, 1);
		}
		
		public function check_coupons(){
			global $woocommerce;
			return (!empty($woocommerce->cart->applied_coupons));
		}
		
		public function reseller_discount_enabled($product){
	
			$discount = $this->get_reseller_discount_by_user();
			
			if(is_a($product, "WC_Product_Variation")){
				
				return isset($discount[$product->get_parent_id()]);
				
			}else{
				
				return isset($discount[$product->get_id()]);
			}
		}
		
		public function reseller_discount_get_price($product){
			
			$discountByUser = $this->get_reseller_discount_by_user();
			
			//is sometimes executed two times in a row
			
			if(is_a($product, "WC_Product_Variation")){
				
				$product	= new WC_Product_Variation($product->get_id());
				$price		= $product->get_price();
				
				$discount = floatval($discountByUser[$product->get_parent_id()]);
				
				return isset($discountByUser[$product->get_parent_id()]) ? ((1.0 - ($discount/100)) * $price) : $price;
				
			}else{
				
				$product	= wc_get_product( $product->get_id() );
				$price		= $product->get_price();
				
				$discount = floatval($discountByUser[$product->get_id()]);
				
				return isset($discountByUser[$product->get_id()]) ? ((1.0 - ($discount/100)) * $price) : $price;
			}
		}
		
		
		
		public function bulk_discount_enabled($product){
	
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
		
		public function bulk_discount_get_price($product, $quantity = 1){
			
			$product_id = $product->get_id();
			if(is_a($product, "WC_Product_Variation")){
				$product_id = $product->get_id();
			}
			
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
		
		public function get_reseller_discount(){
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
		
		public function get_reseller_discount_by_user($user_id = null){
			if($user_id === null){
				$user_id = get_current_user_id();
			}
			
			//var_dump("discount by user", $user_id, $this->check_if_reseller_discounts($user_id) ? "ye" : "no");
			
			if($this->check_if_reseller_discounts($user_id)){
				$discount = $this->get_reseller_discount();
				//var_dump($discount[$user_id]);
			
				return isset($discount[$user_id]) ? $discount[$user_id] : array();
			}else{
				return array();
			}
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
				$discount = $this->remove_bom(get_post_meta($product->get_id(), '_feuerschutz_bulk_discount', true));
				
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
					$discount_tmp = $this->remove_bom(get_post_meta($variation['variation_id'], '_feuerschutz_bulk_discount', true));
					
					if(empty($discount_tmp) || !$this->is_json($discount_tmp)){
						$discount_tmp = "[]";
					}
						
					$discount[$variation['variation_id']] = (empty($discount_tmp) ? array() : json_decode($discount_tmp));
				}
				
				return $discount;
				
			}
		
		}
	}
	
	global $hfag_discount;
	
	$hfag_discount = new Hfag_Woocommerce_Discount();
	
?>