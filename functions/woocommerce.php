<?php
	
	class Hfag_Woocommerce {
		
		public function __construct(){
			//don't load woocommerce styles in the frontend
			add_filter('woocommerce_enqueue_styles', '__return_empty_array', 20);
			//remove product description in the admin menu from the overview
			add_filter('manage_edit-product_cat_columns', array($this, 'remove_product_description'), 100, 1);
			
			//change how the shipping method is displayed as there is only one
			add_filter('woocommerce_cart_shipping_method_full_label', array($this, 'strip_shipping_label'), 100);
			add_filter('woocommerce_order_shipping_to_display_tax_label', '__return_empty_string', 100, 0);
			add_filter('woocommerce_order_shipping_to_display_shipped_via', '__return_empty_string', 100, 0);
			
			/* Start address format */
			
			//override localization address format to include custom fields
			add_filter('woocommerce_localisation_address_formats', array($this, 'localisation_address_formats'), 100, 1);
			//add custom fields to the address
			add_filter('woocommerce_formatted_address_replacements', array($this, 'formatted_address_replacements'), 100, 2);
			
			//change address format in orders
			add_filter('woocommerce_order_formatted_billing_address', array($this, 'order_formatted_billing_address'), 100, 2);
			add_filter('woocommerce_order_formatted_shipping_address', array($this, 'order_formatted_shipping_address'), 100, 2);
			
			//always show the country
			add_filter('woocommerce_formatted_address_force_country_display', '__return_true');
			
			//custom address validation
			add_action(	'woocommerce_checkout_process',	 'checkout_validation', 100, 1);
			
			//store custom address fields in order
			add_action(	'woocommerce_checkout_update_order_meta', array($this, 'store_custom_address_fields_in_order'), 10, 1);
			
			/* End address format */
			
			//change how shipping is displayed in orders
			add_filter('woocommerce_get_order_item_totals', array($this, 'get_order_item_totals'), 100, 2);
			
			//add custom gateways
			add_filter('woocommerce_payment_gateways', array($this, 'payment_gateways'), 20);
			
			//change emails
			add_action('woocommerce_email', array($this, 'woocommerce_email'), 100, 1);
			add_filter('woocommerce_email_headers', array($this, 'email_headers'), 1000, 3);
			add_filter('woocommerce_email_subject_new_order', array($this, 'email_subject_new_order'), 1, 2);
			
			//mark all orders as done
			add_action('woocommerce_thankyou', array($this, 'mark_orders_as_completed'), 100, 1);
			
			//some products have a minimum order quantity
			add_action('woocommerce_quantity_input_min', array($this, 'quantity_input_min'), 10, 2);
			
			//add custom csv importer functionality
			add_filter('woocommerce_csv_product_import_mapping_options', array($this, 'add_importer_columns'));
			add_filter('woocommerce_csv_product_import_mapping_default_columns', array($this, 'add_importer_column_mapping'));
			add_filter('woocommerce_product_import_inserted_product_object', array($this, 'process_importer_columns'), 10, 2 );
			add_filter('woocommerce_get_product_id_by_sku', array($this, 'get_product_id_by_sku_and_language'), 10, 2);
			
			add_filter('woocommerce_product_import_before_process_item', array($this, "product_import_before_process_item"), 10, 1);
			
		}
		
		public function remove_product_description($columns){
			unset($columns['description']); //prevent wysiwyg content from displaying
			return $columns;
		}
		
		public function strip_shipping_label($label){
			return explode(": ", $label)[1]; //strip everything but the price; there's only one shipping method
		}
		
		public function order_formatted_billing_address($address, $order){
			$address['additional_line_above']		= get_post_meta($order->id, '_billing_additional_line_above', true);
			$address['description']					= get_post_meta($order->id, '_billing_description', true);
			
			$box = get_post_meta($order->id, '_billing_post_office_box', true);
			$address['post_office_box']				= $box ? (__("Post office box", 'b4st') . ": " . $box) : '';
			
			return $address;
		}
		
		public function order_formatted_shipping_address($address, $order){
			$address['additional_line_above']		= get_post_meta($order->id, '_shipping_additional_line_above', true);
			$address['description']					= get_post_meta($order->id, '_shipping_description', true);
			
			$box = get_post_meta($order->id, '_shipping_post_office_box', true);
			$address['post_office_box']				= $box ? (__("Post office box", 'b4st') . ": " . $box) : '';
			
			return $address;
		}
		
		public function localisation_address_formats($formats){
			$formats[ 'CH' ]  = "{additional_line_above}\n{company}\n{name}\n{description}\n{address_1}\n{address_2}\n{post_office_box}\n{country}-{postcode}, {city}, {state}\n";
			return $formats;
		}
		
		public function formatted_address_replacements($replacements, $args){
	
			$replacements['{country}']						= $args['country']; /* country code instead of name */
			
			$replacements['{additional_line_above}']		= !empty($args['additional_line_above']) ? $args['additional_line_above'] : '';
			$replacements['{description}']					= !empty($args['description']) ? $args['description'] : '';
			$replacements['{post_office_box}']				= !empty($args['post_office_box']) ? $args['post_office_box'] : '';
			
			return $replacements;
		}
		
		public function store_custom_address_fields_in_order($order_id){
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
		
		public function get_order_item_totals($rows, $order){
			
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
		
		public function checkout_validation($order){
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
				$this->validate_state($_POST['billing_state'], $states);
			}*/
			/*if(isset($_POST['shipping_state']) && isset($_POST['ship_to_different_address']) && $_POST['ship_to_different_address'] == 1){
				$this->validate_state($_POST['shipping_state'], $states);
			}*/
			
			if(isset($_POST['billing_phone'])){
				$this->validate_phone_number($_POST['billing_phone']);
			}
			if(isset($_POST['shipping_phone']) && isset($_POST['ship_to_different_address']) && $_POST['ship_to_different_address'] == 1){
				$this->validate_phone_number($_POST['shipping_phone']);
			}
		}
		
		public function payment_gateways($methods){
			$methods[] = 'WC_Gateway_Feuerschutz_Invoice'; 
			return $methods;
		}
		
		public function woocommerce_email($email_class){
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
		
		public function email_headers($headers = '', $id = '', $order){
			if ($id == 'new_order'){
		
				$name = $order->billing_first_name . " " . $order->billing_last_name;
				$from = $name . " via Online-Shop der Hauser Feuerschutz AG <" . $order->billing_email . ">";
				
				$client = $name . " <" . $order->billing_email . ">";
				
				$headers .= "Reply-To: " . $client . "\r\n";
				$headers .= "Sender: " . $client . "\r\n";
		    }
		    return $headers;
		}
		
		function email_subject_new_order($subject, $order){
			global $woocommerce;
		
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		
			$subject = sprintf('Bestellung (%s) von %s %s', $order->id, $order->billing_first_name, $order->billing_last_name);
		
			return $subject;
		}
		
		public function mark_orders_as_completed($order_id){
			if ( ! $order_id ) {
		        return;
		    }
		
		    $order = wc_get_order( $order_id );
		    $order->update_status( 'completed' );
		}
		
		public function quantity_input_min($min, $product){
			$min_qty = intval(get_post_meta($product->get_id(), '_feuerschutz_min_purchase_qty', true));
	
			if(!empty($min_qty) && $min_qty > 1){
				return $min_qty;
			}
			
			return $min;
		}
		
		public function validate_state($state, $states){
			if(!in_array(strtoupper($state), $states)){
				wc_add_notice( __( 'Invalid state!', 'b4st' ), 'error' );
			}
		}
		
		public function validate_phone_number($number){
			if(! preg_match("/[0-9]{3}\ [0-9]{3}\ [0-9]{2}\ [0-9]{2}/", $number)){
				wc_add_notice( __( 'Invalid phone number!', 'b4st' ), 'error' );
			}
		}
		
		public function add_importer_columns( $options ) {
			$options['discount_groups'] = 'Discount Groups';
			//$options['minimum_quantity'] = 'Minimum Quantity';
			return $options;
		}
		
		public function add_importer_column_mapping( $columns ) {
			
			// potential column name => column slug
			$columns['Discount Groups'] = 'discount_groups';
			$columns['Discount Group'] = 'discount_groups';
			$columns['discount groups'] = 'discount_groups';
			$columns['discount group'] = 'discount_groups';
			
		
			return $columns;
		}
		
		public function process_importer_columns($object, $data){
			
			$product_id = $object->get_id();
			
			if (!empty($data['discount_groups'])){
				$discount_groups = explode(",", $data["discount_groups"]);
				
				$terms = array();
					
				foreach($discount_groups as $discount_group){
					
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
				
				wp_set_object_terms($product_id, $terms, 'product_discount', false /* Override */);
				wp_update_term_count_now($terms, 'product_discount');
				
			}
		
			return $object;
		}
		
		/*
			- https://github.com/woocommerce/woocommerce/blob/737f6af5e8af27ae768d087e84c0303d8059281a/includes/admin/class-wc-admin-importers.php
			- https://github.com/woocommerce/woocommerce/blob/55692cba870690145afc810227ef0b1ca33fc5c3/includes/admin/importers/class-wc-product-csv-importer-controller.php#L22
			- https://github.com/woocommerce/woocommerce/blob/22726bd74df86cf2fce823e6d13b855318eb169f/includes/import/class-wc-product-csv-importer.php#L27
			- https://github.com/woocommerce/woocommerce/blob/55692cba870690145afc810227ef0b1ca33fc5c3/includes/import/abstract-wc-product-importer.php#L23
			- https://github.com/woocommerce/woocommerce/search?q=%22function+import%22&unscoped_q=%22function+import%22
			- https://docs.woocommerce.com/wc-apidocs/source-function-wc_get_product_id_by_sku.html#604-614
			- https://docs.woocommerce.com/wc-apidocs/source-class-WC_Product_Data_Store_CPT.html#956-984
			- https://wpml.org/forums/topic/wc_get_product_id_by_sku-not-returning-the-right-product-on-current-language/
			- https://wpml.org/wpml-hook/wpml_object_id/
		*/
		
		public function get_product_id_by_sku_and_language($id, $sku){
			return apply_filters( 'wpml_object_id', $id, 'product', false, "de"); //for now hardcode german as it could cause troubles otherwise
		}
		
		//https://github.com/woocommerce/woocommerce/blob/55692cba870690145afc810227ef0b1ca33fc5c3/includes/import/abstract-wc-product-importer.php#L250
		public function product_import_before_process_item($data){
			//modify the data before importing e.g. prevent inserting an empty name!
			
			foreach($data as $key => $value){
				if(isset($data[$key]) && empty($value)){
					unset($data[$key]);
				}
			}
			
			return $data;
		}
		
	}
	
	
	new Hfag_Woocommerce();
	
?>