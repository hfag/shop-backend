<?php
	
function feuerschutz_admin_enqueue_scripts(){
	wp_register_script('bulk-discount', get_template_directory_uri() . '/js/min/bulk-discount.min.js', array('jquery-ui-core', 'jquery-ui-sortable'));
	wp_enqueue_script('bulk-discount');
}
add_action( 'admin_enqueue_scripts', 'feuerschutz_admin_enqueue_scripts' );