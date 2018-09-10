		<footer class="bg-primary">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
					
						<img class="logo hidden-sm-down" src="<?php echo get_template_directory_uri()."/img/logo/logo_negative.svg"; ?>">
						
						<p class="company-name">
							<?php echo get_field('footer_display_name_copyright', 'option'); ?>, &copy; <?php echo date("Y");?>
						</p>
						<p class="design-note">
							Design and Code by <a target='_blank' href='https://tyratox.ch'>Nico</a>
						</p>
					</div>
					<div class="icon-list col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
						
						<a target="_blank" href="<?php echo get_field('footer_map_link', 'option'); ?>">
							<div class="flex-row">
								<div class="icon">
									<i class="fa fa-map-marker"></i>
								</div>
								<div class="content">
									<address>
										<?php echo get_field('footer_address', 'option'); ?>
									</address>
								</div>
							</div>
						</a>
						
						<a href="<?php echo 'tel:' . get_field('footer_tel_link', 'option'); ?>">
							<div class="flex-row">
								<div class="icon">
									<i class="fa fa-phone"></i>
								</div>
								<div class="content">
									<p><?php echo get_field('footer_tel_display', 'option'); ?></p>
								</div>
							</div>
						</a>
						<?php
							$mail = get_field('footer_mail', 'option');
						?>
						<a href="<?php echo 'mailto:' . $mail; ?>">
							<div class="flex-row">
								<div class="icon">
									<i class="fa fa-envelope"></i>
								</div>
								<div class="content">
									<p><?php echo $mail; ?></p>
								</div>
							</div>
						</a>
						
						<?php unset($mail); ?>
						
					</div>
					
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
						<p class="company-about">
							<?php echo get_field('text_right_column', 'option'); ?>
						</p>
					</div>
				</div>
			</div>
		</footer>
		<div id="search-window" class="bg-primary">
			<div class="close-window trigger-search-bar"></div>
			<div class="container">
				<div class="search">
					<i class="fa fa-search trigger-search-bar"></i>
					<input id="ajax-search" type="text" placeholder="<?php _e("Search", 'b4st'); ?>">
				</div>
				<p class="information"><?php printf(__("This is a quick text-based search, if you need <a href='%s'>advanced filter options, click here</a>.", "b4st"), get_permalink(woocommerce_get_page_id('shop'))); ?></p>
				<div class="scrolling">
					<div class="row products same-width">
					
					</div>
				</div>
			</div>
			<div class="sk-folding-cube ajax-search-spinner">
				<div class="sk-cube1 sk-cube"></div>
				<div class="sk-cube2 sk-cube"></div>
				<div class="sk-cube4 sk-cube"></div>
				<div class="sk-cube3 sk-cube"></div>
			</div>
		</div>
		<?php wp_footer(); ?>
	</body>
</html>
