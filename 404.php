<?php
	get_header();
?>

<div class="container">
	<div class="padding-top">
		<h1><?php _e('Error', 'b4st'); ?> 404</h1>
		
		<div class="row">
	    	<div class="col-xs-12 col-md-6">
				<div id="content" role="main">
					<p><?php _e('Sorry, the page you were looking for wasn\'t found.', 'b4st'); ?></p>
					<p>
						<?php printf(__('If you want to find something specific, try the search function below or if you simply want to return to the main page, <a href="%s">click here</a>.', 'b4st'), '/'); ?>
						<form action="/" method="GET">
							<?php echo __("Search", 'b4st') . ": "; ?><input class="small-width inline" type="text" name="s">
						</form>
					</p>
					<p><?php echo sprintf(__('Should you feel like this is an error, do not hesitate to <a href="%s">contact</a> us.', 'b4st'), 'mailto:' . get_field('footer_mail', 'option')); ?></p>
				</div><!-- /#content -->
			</div>
			<div class="col-xs-12 col-md-6 full-size-image">
				<!--<img src="<?php echo get_template_directory_uri() . '/img/compass.jpg'; ?>">-->
			</div>
		</div><!-- /.row -->
	</div>
</div><!-- /.container -->

<?php get_footer(); ?>