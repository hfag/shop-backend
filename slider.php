<div class="container-fluid">
	<div class="slider message-slider hidden-sm-down">
		<div class="slides full-width no-margin negative dots-inside-slider arrows-inside-slider">
			<?php
				$slides = get_field('slides');
				foreach($slides as $slide){
					$supports = explode("-", $slide['acf_fc_layout']);
					
					$attrs = array(
						'class'		=> 'slide message ' . $slide['acf_fc_layout'],
						'style'		=> (in_array('image', $supports) ? "background-image:url(\"".$slide['image']['url']."\");" : '') .
									   (in_array('color', $supports) ? "background-color:".$slide['color'].";" : '') .
									   "color:" . (isset($slide['font_color']) ? $slide['font_color'] : "#FFFFFF")
					);
					
					$attr_names = "";
					foreach($attrs as $attr => $content){
						$attr_names .= " " . $attr . "='" . $content . "'";
					}
					
					echo "<div".$attr_names.">";
					
						echo "<div class='container'>";
					
							if(in_array('title', $supports)){
								echo "<h1>".$slide['title']."</h1>";
							}
							
							if(in_array('subtitle', $supports)){
								echo "<h4>".$slide['subtitle']."</h4>";
							}
							
							if(in_array('content', $supports)){
								echo "<div class='content row'><div class='col-xs-12 col-sm-9 col-md-7'>".$slide['content']."</div></div>";
							}
						
						echo "</div>";
					
					echo "</div>";
				}
			?>
		</div>
	</div>
</div>