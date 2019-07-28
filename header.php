<?php
	$host = $_SERVER['HTTP_HOST']; 

	$hosts = array(
		'shop.feuerschutz.ch',
	);
	
	if($host !== "shop.feuerschutz.ch") {
	    header('Location: https://shop.feuerschutz.ch');
	}
	
?>