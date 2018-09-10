<?php
	
function feuerschutz_admin_enqueue_scripts(){
	wp_register_script('bulk-discount', get_template_directory_uri() . '/js/min/bulk-discount.min.js', array('jquery-ui-core', 'jquery-ui-sortable'));
	wp_enqueue_script('bulk-discount');
}
add_action( 'admin_enqueue_scripts', 'feuerschutz_admin_enqueue_scripts' );

function b4st_enqueues() {
	
	global $wp_scripts;
	
	wp_register_style('b4st-css', get_template_directory_uri() . '/css/b4st.min.css', array(), '1.0.1', 'all');
	wp_enqueue_style('b4st-css');
	
	/*wp_deregister_script('jquery');
	
	wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js', false, false, true);
	wp_enqueue_script('jquery');*/
	
	wp_register_script('tether', get_template_directory_uri() . '/bower_components/tether/dist/js/tether.min.js', false, false, true);
	wp_enqueue_script('tether');

  	wp_register_script('bootstrap-js', get_template_directory_uri() . '/bower_components/bootstrap/dist/js/bootstrap.min.js', array('jquery', 'tether'), null, true);
	wp_enqueue_script('bootstrap-js');
	
	wp_register_script('photoswipe', get_template_directory_uri() . '/bower_components/photoswipe/dist/photoswipe.min.js', array(), false, true);
	wp_enqueue_script('photoswipe');
	
	wp_register_script('photoswipe-ui', get_template_directory_uri() . '/bower_components/photoswipe/dist/photoswipe-ui-default.min.js', array('photoswipe'), false, true);
	wp_enqueue_script('photoswipe-ui');
	
	wp_register_script('perfect-scrollbar', get_template_directory_uri() . '/bower_components/perfect-scrollbar/js/min/perfect-scrollbar.jquery.min.js', array('jquery'), false, true);
	wp_enqueue_script('perfect-scrollbar');
	
	wp_register_script('slick', get_template_directory_uri() . '/bower_components/slick-carousel/slick/slick.min.js', array('jquery'), false, true);
	wp_enqueue_script('slick');
	
	//wp_register_script('awesomplete-js', get_template_directory_uri() . '/js/min/awesomplete.min.js', array(), null, true);
	//wp_enqueue_script('awesomplete-js');
	
	wp_dequeue_style('wcff-style');
	wp_deregister_style('wcff-style');
	
	wp_dequeue_script('bodhi_svg_inline'); //we don't want additional js
	wp_deregister_script('bodhi_svg_inline');
	
	wp_register_style('helvetica-tracking-code', 'https://fast.fonts.net/lt/1.css?apiType=css&c=ba089307-597b-4d3e-ad18-ca7e7a6f2070&fontids=694048,694054', false, false, null);
	wp_enqueue_style('helvetica-tracking-code');

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
	
	
	if (wp_script_is('password-strength-meter', 'registered')){
		wp_deregister_script('password-strength-meter');
		wp_register_script('password-strength-meter', get_template_directory_uri() . '/js/min/password-strength.min.js', array('jquery', 'zxcvbn-async'), false, true);
		
		if(did_action('init')){
			wp_localize_script( 'password-strength-meter', 'pwsL10n', array(
				'unknown'  => _x( 'Password strength unknown', 'password strength' ),
				'short'    => _x( 'Very weak', 'password strength' ),
				'bad'      => _x( 'Weak', 'password strength' ),
				'good'     => _x( 'Medium', 'password strength' ),
				'strong'   => _x( 'Strong', 'password strength' ),
				'mismatch' => _x( 'Mismatch', 'password mismatch' ),
			));
		}
		
	}
	
}
add_action('wp_enqueue_scripts', 'b4st_enqueues', 10000);


function feuerschutz_replace_woocommerce_scripts(){
	global $wp_scripts;
	
	if(wp_script_is( "wc-add-to-cart-variation", "enqueued")){
		wp_register_script('b4st-js', get_template_directory_uri() . '/js/min/b4st.min.js', array('jquery', 'slick', 'photoswipe', 'photoswipe-ui'/*, 'awesomplete-js'*/, 'wc-add-to-cart-variation'), null, true);
	}else{
		wp_register_script('b4st-js', get_template_directory_uri() . '/js/min/b4st.min.js', array('jquery', 'slick', 'photoswipe', 'photoswipe-ui'/*, 'awesomplete-js'*/), null, true);
	}
	
	wp_enqueue_script('b4st-js');
}
add_action('wp_print_footer_scripts', 'feuerschutz_replace_woocommerce_scripts', 0);