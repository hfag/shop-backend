<?php
	
	class Hfag_Search{
		
		public function __construct(){
			add_filter('relevanssi_content_to_index', array($this, 'post_to_indexable_content'), 10, 2);
		}
		
		public function post_to_indexable_content($content, $post){
			//include parent content for all variations
			if($post->post_type == "product_variation"){
				$parent = get_post($post->post_parent);
				$content .= " {$parent->post_content}";
			}
		 
			return $content;
		}
	}
	
	new Hfag_Search();
	
?>