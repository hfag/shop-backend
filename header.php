<?php
	$host = $_SERVER['HTTP_HOST']; 

	$hosts = array(
		'shop.feuerschutz.ch',
	);
	
	if($host !== "shop.feuerschutz.ch") {
	    header('Location: https://shop.feuerschutz.ch');
	}
	
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php wp_title('-', true, 'right'); ?></title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  	
  	<?php $fav_url = get_template_directory_uri()."/img/favicon/"; ?>
	
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $fav_url;?>apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $fav_url;?>apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $fav_url;?>apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $fav_url;?>apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $fav_url;?>apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $fav_url;?>apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $fav_url;?>apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $fav_url;?>apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $fav_url;?>apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="<?php echo $fav_url;?>favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="<?php echo $fav_url;?>android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="<?php echo $fav_url;?>favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="<?php echo $fav_url;?>favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="<?php echo $fav_url;?>manifest.json">
	<link rel="shortcut icon" href="<?php echo $fav_url;?>favicon.ico">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="<?php echo $fav_url;?>mstile-144x144.png">
	<meta name="msapplication-config" content="<?php echo $fav_url;?>browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<meta name="format-detection" content="telephone=no">
  	
	<?php
		unset($fav_url); /*not needed anymore*/
		wp_head();
	?>
</head>

<body <?php body_class(); ?>>
	<nav class="navbar navbar-topbar navbar-dark bg-primary">
		<div class="container">
			<div class="row">
				
				<div class="col-xs-2 hidden-sm-up mobile-nav-menu-toggle">
					<button class="navbar-toggler hidden-sm-up center-vertically navbar-icon" type="button">
						<i class="fa fa-navicon"></i>
					</button>
				</div>
	
				<div class="col-xs-5 col-sm-4 col-md-3 col-lg-2">
					<a class="navbar-brand" href="<?php echo home_url('/'); ?>">
						<img src="<?php echo get_template_directory_uri()."/img/logo/logo_negative.svg"; ?>" alt="<?php echo __("Logo negative", "b4st"); ?>">
						<div class="brand-subtitle hidden-sm-up"><?php _e("Online-Shop", 'b4st');?></div>
					</a>
				</div>
				
				<div class="hidden-md-down col-lg-4">
					<a class="navbar-brand" href="<?php echo home_url('/'); ?>"><img src="<?php echo get_template_directory_uri()."/img/logo/name_slogan_negative.svg"; ?>" alt="<?php echo __("Slogan negative", "b4st"); ?>"></a>
				</div>
				
				<div class="col-xs-1 col-xs-offset-3 hidden-sm-up center-vertically navbar-icon trigger-search-bar">
					<i class="fa fa-search"></i>
				</div>
	
				<div class="hidden-xs-down col-xs-8 col-md-9 col-lg-6">
					<ul class="nav navbar-nav pull-right">
						<li class="nav-item">
							<div class="search center-vertically">
								<a href="#" class='trigger-search-bar' data-url="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>">
									<i class="fa fa-search"></i>
								</a>
							</div>
						</li>
						<li class="nav-item dropdown">
							<div class="shopping-cart dropdown-toggle center-vertically" data-toggle="dropdown">
								<i class="fa fa-shopping-cart"></i>
								<div class="counter" data-update-name="cart-qty">
									<?php echo WC()->cart->get_cart_contents_count(); ?>
								</div>
								<i class="fa fa-angle-down"></i>
							</div>
							<?php
	
								wp_nav_menu( array(
						            'theme_location'			=> 'navbar-cart',
						            'menu_class'				=> 'nav navbar-nav',
						            'echo'						=> true,
									'items_wrap'				=> '<div id="%1$s" class="dropdown-menu %2$s">%3$s</div>',
						            'depth'						=> 1,
						            'container'					=> false,
						            'walker'					=> new Walker_Feuerschutz_FA('a', true),
						            'fallback_cb'				=> '__return_false',
						        ));
	
							?>
						</li>
						<li class="nav-item dropdown">
							<div class="user center-vertically dropdown-toggle <?php echo is_user_logged_in() ? 'logged-in' : 'logged-out';?>" data-toggle="dropdown">
								<?php
									echo is_user_logged_in() ? get_avatar(get_current_user_id(), 85) : '<i class="fa fa-sign-in"></i>';
								?>
								<i class="fa fa-angle-down"></i>
							</div>
							<?php
								
								if(is_user_logged_in()){
									wp_nav_menu( array(
							            'theme_location'			=> 'navbar-user',
							            'menu_class'				=> 'nav navbar-nav',
							            'echo'						=> true,
										'items_wrap'				=> '<div id="%1$s" class="dropdown-menu %2$s">%3$s</div>',
							            'depth'						=> 1,
							            'container'					=> false,
							            'walker'					=> new Walker_Feuerschutz_FA('a', true),
							            'fallback_cb'				=> '__return_false',
							        ));
								}else{
									echo '<div class="dropdown-menu nav navbar-nav">';
										echo '<a class="menu-item dropdown-item" href="' . get_permalink(get_option('woocommerce_myaccount_page_id')) . '">';
											echo '<i class="fa fa-sign-in"></i>';
											echo __("Login / Register", 'b4st');
										echo '</a>';
									echo '</div>';
								}
	
							?>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>
	<nav class="visible-xs off-canvas-menu push-menu left">
		
		<div class="logo">
			<img src="<?php echo get_template_directory_uri()."/img/logo/name_slogan_negative.svg"; ?>" alt="<?php echo __("Logo negative", "b4st"); ?>">
		</div>
		
		<ul>
			<li class='spacer'></li>
			<li><i class='fa fa-home'></i><a href="https://feuerschutz.ch" target="_blank"><?php echo __("To our Homepage", 'b4st');?></a></li>
			<li class='spacer'></li>
			<li><i class='fa fa-search'></i><a class='trigger-search-bar' href="#"><?php echo __("Search / Shop", 'b4st');?></a></li>
			<li class='spacer'></li>
			<?php
	
				wp_nav_menu( array(
		            'theme_location'			=> 'navbar-cart',
		            'menu_class'				=> '',
		            'echo'						=> true,
					'items_wrap'				=> '%3$s',
		            'depth'						=> 1,
		            'container'					=> false,
					'walker'					=> new Walker_Feuerschutz_FA('li', false),
					'fallback_cb'				=> '__return_false',
		        ));
		
			?>
			<li class='spacer'></li>
			<?php
	
				wp_nav_menu( array(
		            'theme_location'			=> 'navbar-user',
		            'menu_class'				=> '',
		            'echo'						=> true,
					'items_wrap'				=> '%3$s',
		            'depth'						=> 1,
		            'container'					=> false,
					'walker'					=> new Walker_Feuerschutz_FA('li', false),
					'fallback_cb'				=> '__return_false',
		        ));
	
			?>
		</ul>
	</nav>
	<div class="off-canvas-overlay visible-xs"></div>