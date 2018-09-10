<?php

function b4st_setup() {
	add_editor_style('theme/css/editor-style.css');
	add_theme_support('post-thumbnails');
	update_option('thumbnail_size_w', 170);
	update_option('medium_size_w', 470);
	update_option('large_size_w', 970);
	
	add_image_size('feuerschutz_fix_width', 800, 9999, false);
	add_image_size('feuerschutz_fix_height', 9999, 800, false);
	
}
add_action('init', 'b4st_setup');

if (! isset($content_width))
	$content_width = 600;

function b4st_excerpt_readmore() {
	return '&nbsp; <a href="'. get_permalink() . '">' . '&hellip; ' . __('Read more', 'b4st') . ' <i class="fa fa-arrow-right"></i>' . '</a></p>';
}
add_filter('excerpt_more', 'b4st_excerpt_readmore');

// Add post formats support. See http://codex.wordpress.org/Post_Formats
add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat'));

// Bootstrap pagination

if ( ! function_exists( 'b4st_pagination' ) ) {
	function b4st_pagination() {
		global $wp_query;
		$big = 999999999; // This needs to be an unlikely integer
		// For more options and info view the docs for paginate_links()
		// http://codex.wordpress.org/Function_Reference/paginate_links
		$paginate_links = paginate_links( array(
			'base' => str_replace( $big, '%#%', get_pagenum_link($big) ),
			'current' => max( 1, get_query_var('paged') ),
			'total' => $wp_query->max_num_pages,
			'mid_size' => 5,
			'prev_next' => True,
			'prev_text' => '<i class="fa fa-angle-left"></i>' . ' ' . __('Previous', 'b4st'),
			'next_text' => __('Next', 'b4st') . ' ' . '<i class="fa fa-angle-right"></i>',
			'type' => 'list'
		) );
		$paginate_links = str_replace( "<ul class='page-numbers'>", "<ul class='pagination'>", $paginate_links );
		//$paginate_links = str_replace( "<li>", "<li class='active page-item'><a href='#' class='page-link'>", $paginate_links );
		$paginate_links = str_replace( "<li><span class='page-numbers current'>", "<li class='active page-item'><a href='#' class='page-link'>", $paginate_links );
		$paginate_links = str_replace( "<li>", "<li class='page-item'>", $paginate_links );
		$paginate_links = str_replace( "<li>", "<li class='page-item'>", $paginate_links );
		$paginate_links = str_replace( "</span>", "</a>", $paginate_links );
		
		$paginate_links = preg_replace( "/\s*page-numbers/", "page-link", $paginate_links );
		$paginate_links = preg_replace( "/\s*nextpage-link/", "page-link", $paginate_links );
		$paginate_links = preg_replace( "/\s*prevpage-link/", "page-link", $paginate_links );
		
		// Display the pagination if more than one page is found
		if ( $paginate_links ) {
			echo $paginate_links;
		}
	}
}
