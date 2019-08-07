<?php
	
	class Hfag_Sitemap {
		
		public $baseUrl = "https://shop.feuerschutz.ch";
		public $languages = array("de", "fr");
		
		public $pathnamesByLanguage = array(
			"de" => array(
				"productCategory" => "produkt-kategorie",
			    "search" => "suche",
			    "product" => "produkt",
			    "post" => "beitrag",
			    "page" => "seite",
			    "login" => "login",
			    "logout" => "logout",
			    "account" => "konto",
			    "orders" => "bestellungen",
			    "details" => "details",
			    "billingAddress" => "rechnungsadresse",
			    "shippingAddress" => "lieferadresse",
			    "cart" => "warenkorb",
			    "confirmation" => "bestaetigung"
			),
			"fr" => array(
				"productCategory" => "produit-categorie",
			    "search" => "recherche",
			    "product" => "produit",
			    "post" => "article",
			    "page" => "page",
			    "login" => "login",
			    "logout" => "logout",
			    "account" => "compte",
			    "orders" => "commandes",
			    "details" => "details",
			    "billingAddress" => "adresse-de-facturation",
			    "shippingAddress" => "adresse-de-livraison",
			    "cart" => "panier-d-achat",
			    "confirmation" => "confirmation"
			)
		);
		
		public function __construct(){
			add_action( 'wp_ajax_robots', array($this, 'get_robots'));
			add_action( 'wp_ajax_nopriv_robots', array($this, 'get_robots'));	
			
			//generate a sitemap for the react site
			add_action( 'wp_ajax_sitemap', array($this, 'get_sitemap'));
			add_action( 'wp_ajax_nopriv_sitemap', array($this, 'get_sitemap'));	
		}
		
		public function get_robots(){
			
			$default_lang = apply_filters( 'wpml_current_language', NULL ); //Store current language
			
			header('Content-type: text/plain');
			
			echo "User-agent: *\n";
			echo "Sitemap: " . $this->baseUrl . "/sitemap.xml";
			
			die("\n");
		}
		
		public function get_sitemap(){
			
			$default_lang = apply_filters( 'wpml_current_language', NULL ); //Store current language
			
			header('Content-type: text/xml');
			
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
				
				foreach($this->languages as $language){
					do_action( 'wpml_switch_language', $language);
					
					//frontpage
					echo $this->get_xml_url("/" . $language . "/", 1.0, false, "daily");
					
					//search
					echo $this->get_xml_url("/" . $language . "/" . $this->$pathnamesByLanguage[$language]["search"], 0.5, false, "daily");
					
					//login
					echo $this->get_xml_url("/" . $language . "/" . $this->$pathnamesByLanguage[$language]["login"], 0.1, false, "monthly");
					
					//login
					echo $this->get_xml_url("/" . $language . "/" . $this->$pathnamesByLanguage[$language]["cart"], 0, false, "always");
					
					$products = new WP_Query(
						array(
							"post_type" => "product",
							"posts_per_page" => -1
						)
					);
					$products = $products->posts;
					
					foreach($products as $product){
						echo $this->get_xml_url(
							"/" . $language . "/" . $this->pathnamesByLanguage[$language]["product"] . "/" . $product->post_name,
							0.9,
							$product->post_modified,
							"weekly"
						);
					}
					
					//free; go gc!
					$products = null;
					
					$productCategories = get_terms(
						array(
							'taxonomy' => 'product_cat',
							'hide_empty' => true
						)
					);
					
					foreach($productCategories as $productCategory){
						echo $this->get_xml_url(
							"/" . $language . "/" . $this->pathnamesByLanguage[$language]["productCategory"] . "/" . $productCategory->slug,
							0.8,
							false,
							"weekly"
						);
					}
					
					$productCategories = null;
					
					$pages = new WP_Query(
						array(
							"post_type" => "page",
							"posts_per_page" => -1
						)
					);
					$pages = $pages->posts;
					
					foreach($pages as $page){
						echo $this->get_xml_url(
							"/" . $language . "/" . $this->pathnamesByLanguage[$language]["page"] . "/" . $page->post_name,
							0.9,
							$page->post_modified,
							"weekly"
						);
					}
					
					$pages = null;
					
					$posts = new WP_Query(
						array(
							"post_type" => "post",
							"posts_per_page" => -1
						)
					);
					$posts = $posts->posts;
					
					foreach($posts as $post){
						echo $this->get_xml_url(
							"/" . $language . "/" . $this->pathnamesByLanguage[$language]["post"] . "/" . $post->post_name,
							0.9,
							$post->post_modified,
							"weekly"
						);
					}
					
					$posts = null;
					
				}
				
			echo '</urlset>';
			
			do_action('wpml_switch_language', $default_lang );
			
			die("\n");
		}
		
		public function get_xml_url($url, $priority = false, $lastmod = false, $changefreq = false){
			$xml = "<url>";
			
				$xml .= "<loc>" . $this->baseUrl . $url . "</loc>";
				
				if($lastmod !== false){
					$xml .= "<lastmod>" . $lastmod . "</lastmod>";
				}
				
				if($changefreq !== false){
					$xml .= "<changefreq>" . $changefreq . "</changefreq>";
				}
				
				if($priority !== false){
					$xml .= "<priority>" . $priority . "</priority>";
				}
			
			$xml .= "</url>";
			
			return $xml;
		}
	}
	
	new Hfag_Sitemap();
	
?>