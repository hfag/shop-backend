<?php
	/* Template Name: Startseite */
	
	get_header();
	
	get_template_part("slider");
?>
<div class="container">
	
	<div id="content" role="main">
		
		<div class="categories">
			<?php
				
				$product_categories = get_taxonomy_hierarchy('product_cat');
				
				foreach($product_categories as $category){
					
					if(count($category->children) == 0){continue;}
					
					echo "<div class='category'>";
						echo "<a href='".get_term_link($category)."'><h2>" . $category->name . "</h2></a>";
						echo "<div class='flyder-wrapper'>";
							echo "<div class='flyder-horizontal slider category-slider' tabindex='-1'>";
								echo "<div class='flyder-slides slides row'>";
										
									foreach($category->children as $child){
										echo "<div class='flyder-slide slide product-category col-xs-6 col-sm-6 col-md-4 col-lg-3'>";
											echo "<div class='slide-wrapper'>";
												echo "<a class='product-el' href='" . get_term_link($child) . "'>";
													$thumbnail_id = get_woocommerce_term_meta( $child->term_id, 'thumbnail_id', true );
													
													echo "<div class='thumbnail-wrapper'>";
														echo "<img src='" . wp_get_attachment_image_src( $thumbnail_id, 'shop_thumbnail')[0] . "'>";
													echo "</div>";
													echo "<h3>" . $child->name . "</h3>";
												echo "</a>";
											echo "</div>";
										echo "</div>";
									}
									
								echo "</div>";
							echo "</div>";
						echo "</div>";
						echo "<nav class='flyder-nav'>";
							echo "<div class='arrow left'>";
								echo "<i class='fa fa-angle-left'></i>";
							echo "</div>";
							echo "<div class='arrow right'>";
								echo "<i class='fa fa-angle-right'></i>";
							echo "</div>";
						echo "</nav>";
					echo "</div>";
				}
				
			?>
		</div>
		
	</div>
	
</div><!-- /.container -->

<?php get_footer(); ?>