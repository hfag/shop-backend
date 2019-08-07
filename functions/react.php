<?php
	
	class Hfag_React{
		
		public function __construct(){
			add_action('init', array($this, 'init'), 0);
			
			add_action('after_password_reset', array($this, 'redirect_to_react_frontend'));
			add_filter('lostpassword_redirect', array($this, 'get_react_frontend_url'));
		}
		
		public function init(){
			global $pagenow;
			
			//immediately redirect if someone somehow lands on the api login page
			if('wp-login.php' == $pagenow && !isset($_GET["action"]) && $_GET["action"] !== "rp"){
				wp_redirect('https://shop.feuerschutz.ch/login');
				exit();
			}
		}
		
		public function get_react_frontend_url(){
			return "https://shop.feuerschutz.ch";
		}
		
		public function redirect_to_react_frontend(){
			wp_redirect($this->get_react_frontend_url()); 
			exit;
		}
	}
	
	new Hfag_React();
	
?>