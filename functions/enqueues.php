<?php
	
	class Hfag_Enqueues {
		public function __construct(){
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );
		}
		
		public function enqueue_admin_scripts(){
			wp_register_script('bulk-discount', get_template_directory_uri() . '/js/min/bulk-discount.min.js', array('jquery-ui-core', 'jquery-ui-sortable'));
			wp_enqueue_script('bulk-discount');
		}
	}
	
	
	new Hfag_Enqueues();