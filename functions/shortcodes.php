<?php
	
	function feuerschutz_downloads_func(){
		$code = "";
		
		$terms = get_terms('download_categories', array(
			'orderby'           => 'name', 
			'order'             => 'ASC',
			'hide_empty'        => true,
			'fields'            => 'all',
			'posts_per_page'	=> -1
		));
		
		$code .= "<div class='download-list'>";
		
		
		foreach($terms as $term){
			
			$code .= "<div class='term'>";
			
			$code .= "<strong>".$term->name."</strong>";
			$code .= "<ul>";
			
			$query = new WP_Query(array(
				'post_type' => 'downloads',
				'orderby'   => 'title',
				'order'     => 'ASC',
				'posts_per_page'	=> -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'download_categories',
						'field'    => 'term_id',
						'terms'    => $term->term_id,
					),
				),
			));
			
			foreach($query->get_posts() as $post){
				$code .= "<li><a href='".get_field('file', $post->ID)['url']."' target='_blank'>".$post->post_title."</a></li>";
			}
			
			$code .= "</ul>";
			
			$code .= "</div>";
		}
		
		$code .= "</div>";
		
		
		return $code;
		
	}
	
	add_shortcode( 'downloads', 'feuerschutz_downloads_func' );
?>