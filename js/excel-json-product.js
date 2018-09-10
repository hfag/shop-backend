(function ($) {

	"use strict";
	
	var ajaxurl = window.ajaxurl;
	var XLSX = window.XLSX;
	
	var discount_groups = {};

	var attribute_keys = {
		Ausführung				: {
			name						: 'Ausführung',
			slug						: 'model',
			position					: 50,
			visibility					: true,
			variation					: true,
			is_taxonomy					: false,
			values						: []
		},
		Pfeilrichtung			: {
			name						: 'Pfeilrichtung',
			slug						: 'arrow-dir',
			position					: 0,
			visibility					: true,
			variation					: true,
			is_taxonomy					: false,
			values						: []
		},
		Grösse					: {
			name						: 'Grösse',
			slug						: 'size',
			position					: 1,
			visibility					: true,
			variation					: true,
			is_taxonomy					: true,
			values						: []
		},
		Jahr				: {
			name						: 'Jahr',
			slug						: 'year',
			position					: 60,
			visibility					: true,
			variation					: true,
			is_taxonomy					: false,
			values						: []
		},
		Farbe					: {
			name						: 'Farbe',
			slug						: 'color',
			position					: 70,
			visibility					: true,
			variation					: true,
			is_taxonomy					: false,
			values						: []
		},
		Format					: {
			name						: 'Format',
			slug						: 'format',
			position					: 80,
			visibility					: true,
			variation					: true,
			is_taxonomy					: false,
			values						: []
		},
		Leuchtdichte_mcd		: {
			name						: 'Leuchtdichte',
			slug						: 'luminance',
			position					: 40,
			visibility					: true,
			variation					: true,
			is_taxonomy					: true,
			values						: []
		},
		Material				: {
			name						: 'Material',
			slug						: 'material',
			position					: 10,
			visibility					: true,
			variation					: true,
			is_taxonomy					: true,
			values						: []
		},
		Norm					: {
			name						: 'Norm',
			slug						: 'norm',
			position					: 90,
			visibility					: true,
			variation					: true,
			is_taxonomy					: false,
			values						: []
		},
		PSPA_Class				: {
			name						: 'PSPA Klasse',
			slug						: 'pspa-class',
			position					: 100,
			visibility					: true,
			variation					: true,
			is_taxonomy					: false,
			values						: []
		},
		Ursprungsland			: {
			name						: 'Ursprungsland',
			slug						: 'country',
			position					: 120,
			visibility					: true,
			variation					: true,
			is_taxonomy					: true,
			values						: []
		},
		Eigenschaft_Druck		: {
			name						: 'Druckeigenschaft(-en)',
			slug						: 'print-property',
			position					: 110,
			visibility					: true,
			variation					: true,
			is_taxonomy					: true,
			values						: []
		},
		Einheit					: {
			name						: 'Einheit',
			slug						: 'unit',
			position					: 1000,
			visibility					: true,
			variation					: true,
			is_taxonomy					: true,
			values						: []
		},
		Symbolnummer			: {
			name						: 'Symbolnummer',
			slug						: 'symbol-number',
			position					: 990,
			visibility					: true,
			variation					: true,
			is_taxonomy					: true,
			values						: []
		},
		Inhalt					: {
			name						: 'Inhalt',
			slug						: 'content',
			position					: 1,
			visibility					: true,
			variation					: true,
			is_taxonomy					: false,
			values						: []
		},
		Variante				: {
			name						: 'Variante',
			slug						: 'product_variation',
			position					: 0,
			visibility					: true,
			variation					: true,
			is_taxonomy					: false,
			values						: []
		}
	};
	
	function sanitizeName(string){
		//doing this in the functions php now EXCEPT " " to "-"
		return string.replace(/ /g,"-");//funny story to this: this isn't the "real" math average, it's the small (toLowerCase) version of a letter used in nordic languages. wordpress sanitizes this to an o or an oe
	}
	
	function getProductIDAttachmentIDInSteps(dataUsedToFetch, step, stepSize, fetchedData, callback){
		var sku_length			= dataUsedToFetch.sku.length;
		var image_code_length	= dataUsedToFetch.image_code.length;
			
		if(step < (sku_length + image_code_length)){
			
			var skus			= dataUsedToFetch.sku.slice(Math.max(step, 0), Math.min(step + stepSize, sku_length));
			var image_codes		= dataUsedToFetch.image_code.slice(Math.max(step - sku_length, 0), Math.min(Math.max(step - sku_length, 0) + (stepSize - skus.length)), image_code_length);
			
			getProductIDAttachmentID({sku: skus, image_code: image_codes}, function(response){
				fetchedData.sku			= jQuery.extend(fetchedData.sku, response.sku);
				fetchedData.image_code	= jQuery.extend(fetchedData.image_code, response.image_code);
				
				getProductIDAttachmentIDInSteps(dataUsedToFetch, step+stepSize, stepSize, fetchedData, callback);
			});
		}else{
			callback(fetchedData);
		}
	}

	function getProductIDAttachmentID(dataUsedToFetch, callback){
		jQuery.post(ajaxurl, {action: 'get_product_id_attachment_id', data: dataUsedToFetch}, callback);
	}

	function fillProduct(product_raw){
		var data = {
			/*action								: 'import_product',*/
			
			product_type						: product_raw.product_type,
			post_id								: product_raw.post_id,

			_tax_status							: 'taxable',
			_tax_class							: '',

			_purchase_note						: '',

			_weight								: '',
			_length								: '',
			_width								: '',
			_height								: '',
			
			_visibility							: 'visible',/* show everywhere */

			product_shipping_class				: -1,

			_stock_status						: 'instock',
			_backorders							: 'no',

			attribute_names						: [],
			attribute_values					: [],
			attribute_position					: [],
			attribute_visibility				: {},
			attribute_variation					: {},
			attribute_is_taxonomy				: {},
			
			categories							: [],
		};

		if("Artikel_Nummer_Produkt" in product_raw){
			data._sku = product_raw.Artikel_Nummer_Produkt;
		}else{
			alert("ERROR while importing: A product doesn't have a sku (DE: Artikelnummer)!");
			return false;
		}
		
		if(product_raw.product_type === 'simple'){
			if("Einzelpreis" in product_raw){
				if(product_raw.Einzelpreis.indexOf(" ") !== -1){
					data._regular_price = product_raw.Einzelpreis.split(" ")[1];
				}else{
					data._regular_price = product_raw.Einzelpreis;
				}
			}else{
				alert("ERROR while importing: " + product_raw.Artikel_Nummer_Produkt + " doesn't have a price set!");
				return false;
			}
			if("thumbnail_id" in product_raw){
				data.thumbnail_id = product_raw.thumbnail_id;
			}else{
				alert("ERROR while importing: " + product_raw.Artikel_Nummer_Produkt + " doesn't have a thumbnail set!");
				return false;
			}
			if("Artikelname_neu" in product_raw){
				data.post_title = product_raw.Artikelname_neu;
			}else{
				alert("ERROR while importing: " + product_raw.Artikel_Nummer_Produkt + " doesn't have a name set!");
				return false;
			}
		}else if(product_raw.product_type === 'variable'){
			/*if("variation_group" in product_raw){
				data.post_title = product_raw.variation_group;
			}else{
				alert("ERROR while importing: a variable product doesn't have a name set! (Report this to Nico Hauser, probably the error isn't caused by the imported file.)");
				return false;
			}*/
		}

		if("Breite" in product_raw){
			data._length = product_raw.Breite;
		}

		if("Höhe" in product_raw){
			data._height = product_raw.Höhe;
		}

		if("Stärke" in product_raw){
			data._width = product_raw.Stärke;
		}
		
		if("Thema" in product_raw){
			data.categories.push(product_raw.Thema);
		}
		
		if("bulk_discount" in product_raw){
			data.bulk_discount = product_raw.bulk_discount;
		}
		
		if("discounts" in product_raw){
			data.discounts = product_raw.discounts;
		}
		
		if("min_purchase_qty" in product_raw){
			data.min_purchase_qty = product_raw.min_purchase_qty;
		}

		//Attribute
		var j = 0;
		for(var attribute_name in product_raw.attributes){
			data.attribute_names.push( (product_raw.attributes[attribute_name].is_taxonomy) ? "pa_" + product_raw.attributes[attribute_name].slug : product_raw.attributes[attribute_name].name );
			
			data.attribute_values.push( product_raw.attributes[attribute_name].values.join("|") );
			
			data.attribute_position.push(product_raw.attributes[attribute_name].position);
			
			if(product_raw.attributes[attribute_name].visibility === true){
				data.attribute_visibility[j] = true;
			}
			
			if(product_raw.attributes[attribute_name].variation === true){
				data.attribute_variation[j] = true;
			}
			
			if(product_raw.attributes[attribute_name].is_taxonomy === true){
				data.attribute_is_taxonomy[j] = true;
			}
			
			j++;
		}

		if("products" in product_raw){
			//set default values

			data.variable_post_id						= {};
			data.variable_sku							= {};

			data.variable_regular_price					= {};
			//data.variable_sale_price					= [];

			data.upload_image_id						= {};

			data.variable_shipping_class				= {};
			data.variable_tax_class						= {};

			data.variation_menu_order					= {};

			data.variable_sale_price_dates_from			= {};
			data.variable_sale_price_dates_to			= {};

			data.variable_weight						= {};
			data.variable_length						= {};
			data.variable_width							= {};
			data.variable_height						= {};
			
			data.variable_bulk_discount					= {};
			
			data.variable_apply_reseller_discount		= {};

			data.variable_enabled						= {};

			data.variable_is_virtual					= {};
			data.variable_is_downloadable				= {};

			data.variable_manage_stock					= {};
			data.variable_stock							= {};
			data.variable_backorders					= {};
			data.variable_stock_status					= {};

			data.variable_description					= {};

			for(var i=0;i<product_raw.products.length;i++){

				if("post_id" in product_raw.products[i]){
					data.variable_post_id[i] = product_raw.products[i].post_id;
				}else{
					alert("ERROR while importing: A product doesn't have a post_id! (Report this to Nico Hauser, probably the error isn't caused by the imported file.)");
					return false;
				}

				if("Artikel_Nummer_Produkt" in product_raw.products[i]){
					data.variable_sku[i] = product_raw.products[i].Artikel_Nummer_Produkt;
				}else{
					alert("ERROR while importing: A product doesn't have a sku (DE: Artikelnummer)!");
					return false;
				}

				if("Einzelpreis" in product_raw.products[i]){
					data.variable_regular_price[i] = product_raw.products[i].Einzelpreis.split(" ")[1];
				}else{
					alert("ERROR while importing: " + product_raw.products[i].Artikel_Nummer_Produkt + " doesn't have a price set!");
					return false;
				}

				if("upload_image_id" in product_raw.products[i]){
					data.upload_image_id[i] = product_raw.products[i].upload_image_id;
					
					//if the parent product doesn't have an image yet, set this one
					if(! ("thumbnail_id" in data)){
						data.thumbnail_id				= product_raw.products[i].upload_image_id;
					}
					
				}else{
					alert("ERROR while importing: " + product_raw.products[i].Artikel_Nummer_Produkt + " doesn't have a image set!");
					return false;
				}

				data.variable_shipping_class[i]			= -1; //Default
				data.variable_tax_class[i]				= ''; //Default

				data.variation_menu_order[i]			= i;  //order by input, doesn't really matter

				if("Breite" in product_raw.products[i]){
					data.variable_length[i] 			= product_raw.products[i].Breite;
				}

				if("Höhe" in product_raw.products[i]){
					data.variable_height[i]				= product_raw.products[i].Höhe;
				}

				if("Stärke" in product_raw.products[i]){
					data.variable_width[i]				= product_raw.products[i].Stärke;
				}
				
				if("Thema" in product_raw.products[i]){
					if(data.categories.indexOf(product_raw.products[i].Thema) === -1){
						data.categories.push(product_raw.products[i].Thema);
					}
				}
				
				if("bulk_discount" in product_raw.products[i]){
					data.variable_bulk_discount[i] = product_raw.products[i].bulk_discount;
				}
				

				data.variable_enabled[i]				= true; //Default
				
				data.variable_backorders[i]				= false; //Default
				data.variable_stock_status[i]			= 'instock'; //Default
				
				if("Artikelname_neu" in product_raw.products[i]){
					data.variable_description[i]		= product_raw.products[i].Artikelname_neu;
					
					//if the parent product doesn't have an image yet, set this one
					if(! ("post_title" in data)){
						data.post_title					= product_raw.products[i].Artikelname_neu;
					}
				}

				//now the attributes
				for(attribute_name in product_raw.attributes){
					if(product_raw.attributes[attribute_name].is_taxonomy === true){
						if(! (('attribute_' + 'pa_' + product_raw.attributes[attribute_name].slug) in data)){
							data['attribute_' + 'pa_' + product_raw.attributes[attribute_name].slug] = {};
						}
						data['attribute_' + 'pa_' + product_raw.attributes[attribute_name].slug][i] = sanitizeName(product_raw.products[i][attribute_name]);
					}else{
						var sanitizedName = sanitizeName(product_raw.attributes[attribute_name].name);
						
						if(! (('attribute_' + sanitizedName) in data)){
							data['attribute_' + sanitizedName] = {};
						}
						data['attribute_' + sanitizedName][i] = product_raw.products[i][attribute_name];
					}
				}
			}
		}

		return data;
	}

	function readExcel(e) {
		var data = e.target.result;

		/* if binary string, read with type 'binary' */
		var workbook = XLSX.read(data, {type: 'binary'});

		var sheet_name_list = workbook.SheetNames;
		
		$("<progress id='import-progress' style='width:100%;'></progress>").insertAfter($("#log-ajax"));

		sheet_name_list.forEach(function(y) { /* iterate through sheets */

			var excel_products = XLSX.utils.sheet_to_json(workbook.Sheets[y]);

			var products = [];
			var products_merged = {};

			var dataUsedToFetch = {
				sku			: [],
				image_code	: []
			};

			var attribute_key, copy;
			var variation_group, column_name, i, j, k;

			for(i=0;i<excel_products.length;i++){
				//Merging variants with each other based on variation code AND trim the group
				excel_products[i].Produktgruppe_Shop = excel_products[i].Produktgruppe_Shop.trim();

				//Check if product should be imported or not
				if(excel_products[i].Shop_Produkt_Ja_Nein !== 1 && excel_products[i].Shop_Produkt_Ja_Nein !== "1"){
					continue;
				}
				
				//Check for discount keys
				for(var column in excel_products[i]){
					if(column.indexOf("_Rabattberechtigt") !== -1 && (column in discount_groups) === false){
						discount_groups[column] = column.replace("_Rabattberechtigt", "");
					}
				}

				//Process certain keys

				if("Artikel_Nummer_Produkt" in excel_products[i]){
					if(dataUsedToFetch.sku.indexOf(excel_products[i].Artikel_Nummer_Produkt) === -1){ //Multiple products can't have the same sku

						dataUsedToFetch.sku.push(excel_products[i].Artikel_Nummer_Produkt);

					}else{
						alert("ERROR while importing: Multiple products can't have the same sku (DE: Artikelnummer)!");
						return false;
					}
				}else{
					alert("ERROR while importing: A product doesn't have a sku (DE: Artikelnummer)!");
					return false;
				}

				if("Artikel_Bilder_Code" in excel_products[i]){

					if(dataUsedToFetch.image_code.indexOf(excel_products[i].Artikel_Bilder_Code) === -1){ //Multiple products can have the same image
						dataUsedToFetch.image_code.push(excel_products[i].Artikel_Bilder_Code);
					}

				}else{
					alert("ERROR while importing: " + excel_products[i].Artikel_Nummer_Produkt + " doesn't have a image set!");
					return false;
				}

				//Merge BOGEN + Stückzahl pro Einheit
				if("Stückzahl pro Einheit" in excel_products[i] && excel_products[i]["Stückzahl pro Einheit"] && "Einheit" in excel_products[i]){
					excel_products[i].Einheit = excel_products[i].Einheit + " (" + excel_products[i]["Stückzahl pro Einheit"] + " STK)";
				}
				
				//Discounts
				
				//Bulk discount
				var bulk_discount = [];
				
				for(column in excel_products[i]){
					if(column.indexOf("VP Staffel ") !== -1 && (column in discount_groups) === false && excel_products[i][column].trim() !== ""){
						
						var ppu = parseFloat(excel_products[i][column].replace("CHF", "").trim());
						var qty = parseInt(column.replace("VP Staffel ", "").trim(), 10);
						
						
						if(ppu > 0 && qty > 0){
							bulk_discount.push({
								qty: qty,
								ppu: ppu
							});
						}
					}
				}
				
				if(bulk_discount !== {}){
					excel_products[i].bulk_discount = bulk_discount;
				}
				
				if("Mindestbestellmenge" in excel_products[i]){
					excel_products[i].min_purchase_qty = parseInt(excel_products[i].Mindestbestellmenge, 10);
				}
				
				//Discount groups
				excel_products[i].discounts = [];

				if(excel_products[i].Produktgruppe_Shop !== undefined){
					//variation, but in object to sort it first

					//produktgruppe is also a 'sku' for the parent post
					if(dataUsedToFetch.sku.indexOf(excel_products[i].Produktgruppe_Shop) === -1){
						dataUsedToFetch.sku.push(excel_products[i].Produktgruppe_Shop);
					}
					
					if(excel_products[i].Produktgruppe_Shop in products_merged){
						products_merged[excel_products[i].Produktgruppe_Shop].push(excel_products[i]);
					}else{
						products_merged[excel_products[i].Produktgruppe_Shop] = [excel_products[i]];
					}
				}else{
					//simple product
					excel_products[i].product_type = 'simple';
					excel_products[i].attributes = [];
					for(attribute_key in attribute_keys){
						
						if(!attribute_keys.hasOwnProperty(attribute_key)){continue;}
						
						if(attribute_key in excel_products[i]){
							//copy attribute
							copy = jQuery.extend(true, {}, attribute_keys[attribute_key]);
							//for simple products there aren't variatios
							copy.variation = false;
							//only one value
							copy.values = [excel_products[i][attribute_key]];

							excel_products[i].attributes.push(copy);
						}
					}
					
					for(column_name in discount_groups){
						if(!discount_groups.hasOwnProperty(column_name)){continue;}
						
						if(column_name in excel_products[i] && (parseInt(excel_products[i][column_name].trim(), 10) === 1 )){
							excel_products[i].discounts.push(discount_groups[column_name]);
						}
					}
					
					products.push(excel_products[i]);
				}

			}

			//Done merging variations, continue with extracting attributes

			var common_attributes = {};

			for (variation_group in products_merged){

				if(!products_merged.hasOwnProperty(variation_group)){continue;}

				common_attributes[variation_group] = {};

				//get all common attribute keys
				for(j=0;j<products_merged[variation_group].length;j++){

					//for all defined attribute keys, check if they are set and return an error if the attribute isn't already in the commons array and j ≠ 1
					if(j === 0){
						//add defined attributes to common_attributes

						for(attribute_key in attribute_keys){
							if(!attribute_keys.hasOwnProperty(attribute_key)){continue;}

							if(attribute_key in products_merged[variation_group][j]){
								//product has attribute, push it onto common_attributes
								common_attributes[variation_group][attribute_key] = jQuery.extend(true, {}, attribute_keys[attribute_key]);
								common_attributes[variation_group][attribute_key].values.push(products_merged[variation_group][j][attribute_key]);
							}
						}
					}else{
						//verify that this product has the same attributes, return an error if that's not the case

						for(attribute_key in common_attributes[variation_group]){

							if(attribute_key in products_merged[variation_group][j]){
								//product has attribute, everything is fine, push value if it's not already present
								if(common_attributes[variation_group][attribute_key].values.indexOf(products_merged[variation_group][j][attribute_key]) === -1){
									common_attributes[variation_group][attribute_key].values.push(products_merged[variation_group][j][attribute_key]);
								}

							}else{
								//first product has attirbutes, this hasn't => error

								alert(
									"ERROR: " + products_merged[variation_group][j].Artikel_Nummer_Produkt +
									" doesn't have the attribute " + attribute_key +
									" but is in the same variation group as " + products_merged[variation_group][0].Artikel_Nummer_Produkt
								);

								return;
							}
						}
					}
				}
			}
			
			//verify discounts
			var common_discounts = {};
			
			for (variation_group in products_merged){
				
				if(!products_merged.hasOwnProperty(variation_group)){continue;}
				
				common_discounts[variation_group] = [];
				
				//get all common discount keys
				for(j=0;j<products_merged[variation_group].length;j++){

					//for all defined discount keys, check if they are set and return an error if the discount group isn't already in the commons array and j ≠ 1
					if(j === 0){
						//add defined discount_groups to common_discounts
						
						for(column_name in discount_groups){
							if(!discount_groups.hasOwnProperty(column_name)){continue;}
							
							if(column_name in products_merged[variation_group][j] && (parseInt(products_merged[variation_group][j][column_name].trim(), 10) === 1 )){
								//product has discount group, push it onto common_discounts
								common_discounts[variation_group].push(column_name);
							}
						}
					}else{
						//verify that this product has the same discount groups, return an error if that's not the case

						for(k=0;k<common_discounts[variation_group].length;k++){
							
							if(column_name in products_merged[variation_group][j] && (parseInt(products_merged[variation_group][j][column_name].trim(), 10) === 1 )){
								//everythings fine, this product is in the same discount group as the first one
							}else{
								//first product has a discount group that this hasn't => error

								alert(
									"ERROR: " + products_merged[variation_group][j].Artikel_Nummer_Produkt +
									" doesn't have the discount group " + column_name +
									" but is in the same variation group as " + products_merged[variation_group][0].Artikel_Nummer_Produkt
								);

								return;
							}
						}
					}
					
				}
				
				for(k=0;k<common_discounts[variation_group].length;k++){
					common_discounts[variation_group][k] = discount_groups[common_discounts[variation_group][k]];
				}
				
			}
			
			//verify min purchase qty
			
			var min_purchase_qtys = {};
			
			for (variation_group in products_merged){
				
				if(!products_merged.hasOwnProperty(variation_group)){continue;}
				
				min_purchase_qtys[variation_group] = 0;
				
				//get all common discount keys
				for(j=0;j<products_merged[variation_group].length;j++){
					
					if(j === 0){
						
						if(!("min_purchase_qty" in products_merged[variation_group][j])){
							break;
						}
						
						min_purchase_qtys[variation_group] = products_merged[variation_group][j].min_purchase_qty;
						
					}else{
						if(min_purchase_qtys[variation_group] !== products_merged[variation_group][j].min_purchase_qty){
							alert(
								"ERROR: " + products_merged[variation_group][j].Artikel_Nummer_Produkt +
								" doesn't have the discount group " + column_name +
								" but is in the same variation group as " + products_merged[variation_group][0].Artikel_Nummer_Produkt
							);
							
							return;
						}
					}
				}
			}
			

			//continue with merging the two array to obtain an 'ajaxable' array...

			for (variation_group in products_merged){

				if(!products_merged.hasOwnProperty(variation_group)){continue;}

				products.push({
					product_type					: 'variable',
					variation_group					: variation_group,
					Artikel_Nummer_Produkt			: variation_group,
					attributes						: common_attributes[variation_group],
					products						: products_merged[variation_group],
					discounts						: common_discounts[variation_group],
					min_purchase_qty				: min_purchase_qtys[variation_group]
				});

			}


			//Fetch post_ids and image_ids
			getProductIDAttachmentIDInSteps(dataUsedToFetch, 0, 500, {sku: {}, image_code: {}}, function(response){

				var i, j, sku, image_code, tmp;

				//check if all images are already in the database, return an error if not
				var missing_images = [];
				for(image_code in response.image_code){
					if(!response.image_code.hasOwnProperty(image_code)){
						missing_images.push(image_code);
					}

					if(response.image_code[image_code] === false){
						missing_images.push(image_code);
					}
				}

				if(missing_images.length !== 0){
					alert("ERROR while importing: The following images are missing, upload them prior to importing products: " + missing_images.join(", "));
					return false;
				}else{
					//all images are present, put them and the post ids into the products

					for(i=0;i<products.length;i++){
						if(products[i].product_type === 'simple'){
							for(sku in response.sku){
								if(products[i].Artikel_Nummer_Produkt === sku){
									products[i].post_id = response.sku[sku];
									break;
								}
							}
							for(image_code in response.image_code){
								if(products[i].Artikel_Bilder_Code === image_code){
									products[i].thumbnail_id = response.image_code[image_code];
									break;
								}
							}
						}else if(products[i].product_type === 'variable'){
							for(sku in response.sku){
								if(products[i].variation_group === sku){
									products[i].post_id = response.sku[sku];
									break;
								}
							}

							for(j=0;j<products[i].products.length;j++){
								for(sku in response.sku){
									if(products[i].products[j].Artikel_Nummer_Produkt === sku){
										products[i].products[j].post_id = response.sku[sku];
										break;
									}
								}
								for(image_code in response.image_code){
									if(products[i].products[j].Artikel_Bilder_Code === image_code){
										products[i].products[j].upload_image_id = response.image_code[image_code];
										break;
									}
								}
							}

						}
						
						tmp = fillProduct(products[i]);
						if(tmp === false){
							return tmp;
						}else{
							products[i] = tmp;
						}

					}

					$("#log-filename").append(" Done!");
					
					$("#import-progress").attr("value", "0");
					$("#import-progress").attr("max", products.length - 1);
					
					postProduct(products, 0, function(){
						
					});
				}

			});

			//

		});
	}
	
	function postProduct(products, index, callback){
		
		if(index >= products.length){
			callback();
			return;
		}
		
		$("#log-ajax").append("<p>["+(index+1)+"/"+products.length+", "+(Math.round(100*100*(index+1)/products.length)/100)+"%] Adding " + products[index]._sku + "...</p>");
		
		jQuery.post(ajaxurl, {action: 'import_product', product_data: JSON.stringify(products[index])} /*workaround max variables*/, function(response){
			
			$("#log-ajax p:last-child").append(" " + response.message);
			$("#import-progress").attr("value", index);
	
			$(window).scrollTop($(document).height());
			
			postProduct(products, index+1, callback);
		});
	}

	function handleDrop(e) {
		e.stopPropagation();
		e.preventDefault();

		var files = e.dataTransfer.files;
		var i,f;
		for (i = 0, f = files[i]; i !== files.length; ++i) {

			var reader = new FileReader();
			var name = f.name;

			reader.onload = readExcel;

			$("#log-filename").append("Reading and parsing " + name + " ...");

			reader.readAsBinaryString(f);
		}
	}

	$("#drop-excel").on("dragover", function(event) {
	    event.preventDefault();
	    event.stopPropagation();

	    $(this).addClass('dragging');
	});

	$("#drop-excel").on("dragleave", function(event) {
	    event.preventDefault();
	    event.stopPropagation();

	    $(this).removeClass('dragging');
	});

	document.getElementById("drop-excel").addEventListener('drop', handleDrop, false);

}(jQuery));
