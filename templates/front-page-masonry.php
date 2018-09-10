<?php
	/* Template Name: kompakte Startseite */
	
	get_header();
	
	get_template_part("slider");
?>
<div class="container">
	
	<div id="content" role="main">
		
		<div class="categories">
			<?php
								
				$product_categories = get_taxonomy_hierarchy('product_cat');
				
				echo "<div class='row same-width'>";
				
				foreach($product_categories as $category){
					
					if(count($category->children) == 0){continue;}
					
					//feuerschutz_generate_product_square($category, $i%2==0);
					
					foreach($category->children as $child){
						feuerschutz_generate_product_square($child, $category);
					}
					
				}
				echo "</div>";
				
			?>
		</div>
		
	</div>
	
</div><!-- /.container -->

<?php get_footer(); ?>