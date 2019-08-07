<?php
	
	class Hfag_Sitemap {
		
		public function __construct(){
			//generate a sitemap for the react site
			add_action( 'wp_ajax_my_action', array($this, 'get_sitemap'));
			add_action( 'wp_ajax_nopriv_my_action', array($this, 'get_sitemap'));	
		}
		
		public function get_sitemap(){
			
		}
	}
	
	new Hfag_Sitemap();
	
?>