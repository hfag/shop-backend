<?php
	class WC_Gateway_Feuerschutz_Invoice extends WC_Payment_Gateway {
			
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

?>