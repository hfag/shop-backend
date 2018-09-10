// @codekit-prepend "js/flyder.jquery.js";

(function ($) {
	
	"use strict";
	
	var states = [
		'AG', 'AI', 'AR', 'BE', 'BL', 'BS', 'FR', 'GE', 'GL', 'GR', 'JU', 'LU', 'NE', 'NW', 'OW', 'SG', 'SH', 'SO', 'SZ', 'TI', 'TG', 'UR', 'VD', 'VS', 'ZG', 'ZH'
	];
		
	$('.message-slider .slides').slick({
		accessibility	: false,
		adaptiveHeight	: false,
		autoplay		: true,
		autoplaySpeed	: 6000,
		arrows			: true,
		dots			: true,
		draggable		: true,
		focusOnSelect	: false,
		speed			: 1000,
		swipe			: true,
		touchMove		: true,
		responsive: [
			{
				breakpoint: 1200,
				settings: {
					
				}
			},
			{
			breakpoint: 992,
				settings: {
					arrows: false
				}
			},
			{
				breakpoint: 768,
				settings: {
					arrows: false
				}
			},
			{
				breakpoint: 544,
				settings: {
					arrows: false
				}
			}
		]
	});
	
	/*$('.category-slider .slides').slick({
		accessibility	: false,
		adaptiveHeight	: false,
		autoplay		: true,
		autoplaySpeed	: 7500,
		arrows			: true,
		dots			: true,
		draggable		: true,
		focusOnSelect	: false,
		speed			: 1000,
		swipe			: true,
		touchMove		: true,
		slidesToShow: 4,
		slidesToScroll: 4,
		responsive: [
			{
				breakpoint: 1200,
				settings: {
					slidesToShow: 4,
					slidesToScroll: 4
				}
			},
			{
			breakpoint: 992,
				settings: {
					slidesToShow: 3,
					slidesToScroll: 3,
					arrows: false
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 2,
					arrows: false,
					dots: false
				}
			},
			{
				breakpoint: 544,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 2,
					arrows: false,
					dots: false
				}
			}
		]
	});*/
	
	/*$(".category-slider.flyder-horizontal").each(function(){
		
		var $cat	= $(this).closest(".category");
		var $nav	= $cat.find("nav.flyder-nav");
		
		$(this).flyder("init", {
			slidesToScroll				: 1,
			autoplay					: true,
			
			arrowLeft					: $nav.find(".arrow.left"),
			arrowRight					: $nav.find(".arrow.right"),
			hoverPauseEl				: $cat
		});
	});*/
	
	$('.variations-slider.flyder-horizontal').each(function(){
		
		var $var	= $(this).closest(".variation-slider");
		var $nav	= $var.find("nav.flyder-nav");
		
		$(this).flyder("init", {
			slidesToScroll				: 1,
			centerMode					: true,
			globalKeyboard				: true,
			
			arrowLeft					: $nav.find(".arrow.left"),
			arrowRight					: $nav.find(".arrow.right"),
			hoverPauseEl				: $var
		});
	});
	
	$("table.cart input[type='number'].qty").change(function(){
		
		var qty = 0, tmp = 0;
		
		$("input[type='number'].qty").each(function(){
			tmp = parseInt($(this).val(), 10);
			qty += isNaN(tmp) ? 0 : tmp;
		});
		
		$("[data-update-name='cart-qty']").text(qty);
	});
	
	var $form = $("form.cart");
	var common_attributes = [];
	
	var resellerDiscount = {};
	var discountCoeffs = {};
	
	$form.find("table.bulk-discount tbody tr").click(function(){
		var q = parseInt($(this).find("td:first").text());
		
		if(q !== ""){
			$form.find("input[name='quantity']").val(q).change();
		}
	});
	
	function recalcPrice(quantity, price){
		
		if(!isFormFull()){
			if(isFormEmpty()){
				quantity	= 0;
				price		= "-";
			}else{
				return;
			}
		}
		
		var currency			= $("meta[itemprop='priceCurrency']").attr("content");
		var discountPrice		= price;
		
		if(typeof quantity === "undefined" || parseInt(quantity) < 1){
			quantity = 1;
		}
		
		if(typeof price === "undefined"){
			price = 99999;
		}
		
		var variationID = $("input[type='hidden'].variation_id").length > 0 ? $("input[type='hidden'].variation_id").val() : $("input[type='hidden'][name='add-to-cart']").val();
		var maxDiscountQuantity = -1;
		if(variationID !== "" && variationID !== null && !isNaN(price)){
			if(resellerDiscount === 0){
		
				if(variationID in discountCoeffs){
					for(var i=0;i<discountCoeffs[variationID].length;i++){
						
						if(quantity >= parseInt(discountCoeffs[variationID][i].qty) && parseInt(discountCoeffs[variationID][i].qty) >= maxDiscountQuantity){
							maxDiscountQuantity = discountCoeffs[variationID][i].qty;
							discountPrice = discountCoeffs[variationID][i].ppu;
						}
					}
				}
			}else{
				discountPrice = (1.0 - (parseFloat(resellerDiscount)/100)) * price;
			}
			
		}
		//just put the currency in front, if other currency will be accepted this needs to be changed
		
		var ppp, sum;
		
		if(price !== discountPrice){
			ppp = "<del>" + currency + " " + ((isNaN(price) ? round2(price) : round2(parseFloat(price))) + "</del> " + currency + " " + ((isNaN(discountPrice) ? discountPrice : round2(parseFloat(discountPrice)))));
			sum = "<del>" + currency + " " + (isNaN(price) ? round2(price) : round2(parseFloat(parseFloat(quantity) * parseFloat(price)))) + "</del> " + currency + " " + (isNaN(discountPrice) ? discountPrice : round2(parseFloat(parseFloat(quantity) * round2(parseFloat(discountPrice)))) );
		}else{
			ppp = currency + " " + ((isNaN(price) ? price : round2(parseFloat(price))));
			sum = currency + " " + (isNaN(price) ? price : round2(parseFloat(parseInt(quantity) * round2(parseFloat(price)))));
		}
		
		$("[data-update='price-per-piece']").html(ppp);
		$("[data-update='price-sum']").html(sum);
		
		//and highlight the specific tr in the bulk discount table
		$form.find("table.bulk-discount tbody tr.active").removeClass("active");
		
		if(maxDiscountQuantity !== -1){
			$form.find("table.bulk-discount tbody tr").each(function(){
				if(parseInt($(this).find("td:first-child").text()) === parseInt(maxDiscountQuantity)){
					$(this).addClass("active");
				}
			});
		}
	}
	
	function recalcVariationValues(){
		$form.trigger("update_variation_values");
	}
	
	function resetForm(e){
		
		var select;
		
		if(e){
			e.preventDefault();
		}
		
		$(".variation:not(.hidden-xs-up)").each(function(){
			select = $(this).find("select");
			
			if(select.length > 0){
				select[0].selectedIndex = 0;
			}
			
		});
		
		recalcVariationValues();
	}
	
	$form.find('.reset_variations_custom').on("click", resetForm);
	
	function onBeforeGotoSlide(event, options, params){
		
		var $slide = $(this).find('[data-flyder-index='+params.slideIndex+']');
		
		common_attributes = $slide.data("select-attributes");
		
		resetForm();
		
		var select;
		
		for(var key in common_attributes){
			select = $('.variation:not(.hidden-xs-up) select[name="' + key + '"]');
			if(select.length > 0){
				select[0].selectedIndex = select.find('option[value="' + common_attributes[key] + '"]').index();
			}
			select.change();
		}
		
		//$form.change();
		
		recalcVariationValues();
		
		//recalcPrice($form.find("input[name='quantity']").val(), $form.find("meta[itemprop='price']").attr("content"));
		
	}
	
	function onFormUpdate(){
		
		$form.find(".variation:not(.hidden-xs-up) select").each(function(){
			/*if(!$(this).hasClass("user-updated") && $(this).find("option").length <= 2){
				$(this).val($(this).find("option:nth-child(2)").val());
			}else */
			
			if(this.selectedIndex === 0){
				//the first one is selected, highlight it
				$(this).addClass("not-selected");
				return;
			}
			$(this).removeClass("not-selected");
		});
		
		$form.find("[name]").each(function(){
			if($(this).is("select")){
				$("[data-update-name='"+$(this).attr("name")+"']").text($(this).find("option:selected").text()).triggerHandler("change"); //doesn't bubble
			}else{
				$("[data-update-name='"+$(this).attr("name")+"']").text($(this).val()).triggerHandler("change"); //doesn't bubble;
			}
		});
		
		recalcVariationValues();
		
	}
	
	function isFormEmpty(){
		var empty = true, val;
		$(".variation:not(.hidden-xs-up) select").each(function(){
			
			val = $(this).val();
			
			if(val.trim() !== "" && val !== null && typeof val !== undefined){
				empty = false;
				
				return false;//break $.each
			}
			
		});
		
		return empty;
	}
	
	function isFormFull(){
		var full = true, val;
		$(".variation:not(.hidden-xs-up) select").each(function(){
			
			val = $(this).val();
			
			if(val.trim() === "" || val === null || typeof val === undefined){
				full = false;
				
				return false;//break $.each
			}
			
		});
		
		return full;
	}
	
	function initForm(){
		//Override woocommerce function
		
		//Set new on image change function
		$.fn.wc_variations_image_update = function( variation ) {
			//Update variation selection
			if(variation === false){return;}
			
			//update bulk discount table
			$form.find("table.bulk-discount tbody tr").hide();
			$form.find("table.bulk-discount tbody tr[data-id='" + variation.variation_id + "']").show();
			
			$("meta[itemprop='price']").attr("content", variation.display_price);
			recalcPrice($form.find("input[name='quantity']").val(), variation.display_price);
			
			$('.variations-slider.flyder-horizontal').flyder("gotoSlide", {slideIndex: parseInt($(".slide img[src='"+variation.image.src+"']").parents(".slide").attr("data-flyder-index")), fireEvents: false});
		};
		
		//add event listener to the lsider
		$('.variations-slider.flyder-horizontal').on('beforeGotoSlide', onBeforeGotoSlide);
		
		
		//add event listener on quantity change
		$form.find("input[name='quantity']").on("change", function(){
			recalcPrice($(this).val(), $form.find("meta[itemprop='price']").attr("content"));
		});
		
		
		//add event listener on variation change
		$("input[type='hidden'].variation_id").on("change", function(){
			recalcPrice($form.find("input[name='quantity']").val(), $form.find("meta[itemprop='price']").attr("content"));
		});
		
		
		//add event listener for a general form change
		$form.bind("change", onFormUpdate);
		
		
		//reset everything on a reset
		$form.on('reset_data', function(){
			recalcPrice(0, 0);
			
			if(isFormEmpty()){
				$form.find("input[name='quantity']").val(1);
				$form.find("input.wccpf-field").val("");
			}
		});
		
		//select the first option if none other is available
		$form.find(".variations select").each(function(){
			if(!$(this).hasClass("updated") && $(this).find("option").length <= 2){
				$(this).val($(this).find("option:nth-child(2)").val()).change();
			}
		});
		
		//if there are no variations, remove the reset button
		if($form.find(".variation:not(.hidden-xs-up)").length === 0){
			$form.find(".variations.row .reset").remove();
			
			var select = $form.find("select")[0];
			
			if(select){
				$(select).change();
			}else{
				recalcPrice($form.find("input[name='quantity']").val(), $form.find("meta[itemprop='price']").attr("content"));
			}
		}
		
	}
	
	if($form.length > 0){
		
		resellerDiscount = $.parseJSON($("meta[itemprop='price']").attr("data-reseller"));
		discountCoeffs = $.parseJSON($("meta[itemprop='price']").attr("data-coeffs"));
		
		initForm();
		
	}
	
	if($(".images-pswp").length > 0){
		var photoSwipeSlides = [];
		var gallery = null;
		
		$(".images-pswp .row.thumbnails .image-preview").click(function(){
			
			if(gallery !== null){
				gallery.close();
			}
			
			photoSwipeSlides = [];
			
			$(this).parent(".row.thumbnails").find(".image-preview").each(function(){
				photoSwipeSlides.push({
					src		: $(this).attr("data-full-src"),
					w		: $(this).attr("data-width"),
					h		: $(this).attr("data-height"),
					msrc	: $(this).find("img").attr("src"),
					title	: $(this).attr("data-title")
				});
			});
			
			gallery = new window.PhotoSwipe($(".pswp").get(0), window.PhotoSwipeUI_Default, photoSwipeSlides, {
				index: $(this).index(),
				getThumbBoundsFn: function(index) {
				
				    // find thumbnail element
				    var thumbnail = $('.images-pswp .row.thumbnails .image-preview').get(index);
				
				    // get window scroll Y
				    var pageYScroll = window.pageYOffset || document.documentElement.scrollTop; 
				    // optionally get horizontal scroll
				
				    // get position of element relative to viewport
				    var rect = thumbnail.getBoundingClientRect(); 
				
				    // w = width
				    return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
				
				
				    // Good guide on how to get element coordinates:
				    // http://javascript.info/tutorial/coordinates
				},
				showHideOpacity	: true,
				bgOpacity		: 0.7,
				shareEl			: false,
				captionEl		: false,
			});
			
			gallery.init();
			
		});
	}
	
	$("input[type='range']").on('input', function(){
		$(this).siblings("h5").children("span[data-display='range']").text($(this).val());
	});
	
	$("input[type='range'][data-range-min]").on('input', function(){
		var el = $("#"+$(this).attr("data-range-min"));
		if(parseInt($(this).val()) < parseInt(el.val())){
			el.val($(this).val()).trigger("input").trigger("change");
		}
	});
	
	$("input[type='range'][data-range-max]").on('input', function(){
		var el = $("#"+$(this).attr("data-range-max"));
		if(parseInt($(this).val()) > parseInt(el.val())){
			el.val($(this).val()).trigger("input").trigger("change");
		}
	});
	
	var width, height;
	
	$(".custom-scrollbar").each(function(){
		width = parseInt($(this).val());
		height = parseInt($(this).val());
		
		$(this).width(width).height(height).perfectScrollbar();
	});
	
	$(".trigger-search-bar").click(function(){
		
		if((!$("body > nav.push-menu").hasClass("active")) && $("input[name='s']").length > 0){
			
			setTimeout(function(){
				$("input[name='s']").focus();
			}, 750);
			
		}else{
			if($("#search-window").toggleClass("active").hasClass("active")){
			
				$("#ajax-search").focus();
				
				$(document).unbind("keyup").keyup(function(e) {
					if (e.keyCode === 27)/*Escape*/{
						$(document).unbind("keyup");
						$("#search-window").toggleClass("active");
					}
				});
			}
		}
		
	});
	
	function bindChips(){
		$(".selected-taxonomies .chip .fa.fa-times").unbind("click").click(function(){
			var $chip = $(this).parent(".chip");
			
			var taxonomy_name = $chip.attr("data-taxonomy-name");
			var term_slug = $chip.attr("data-term-slug");
			
			$("input[name='taxonomies["+taxonomy_name+"][]'][value='"+term_slug+"']").prop('checked', false);
			
			$chip.addClass("remove").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(){
				$(this).remove();
			});
		});
	}
	bindChips();
	
	$(".scrollable-taxonomies.attributes input[type='checkbox']").change(function(){
		
		if($(this).attr("name").indexOf("taxonomies[") !== -1){
			
			
			var taxonomy_name = $(this).attr("name").split("taxonomies[")[1].split("][]")[0];
			var term_slug = $(this).val();
			
			if($(this).prop("checked") === true){
				$(".selected-taxonomies").append(
					"<div class='chip js-added' data-taxonomy-name='"+taxonomy_name+"' data-term-slug='"+term_slug+"'>"+
						"<i class='fa fa-times'></i>"+
						$(this).parent(".styled-input").parent("li").find("label > span").text()+
					"</div>"
				);
				
				bindChips();
				
			}else{
				$(".selected-taxonomies .chip[data-taxonomy-name='"+taxonomy_name+"'][data-term-slug='"+term_slug+"']").addClass("remove").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(){
					$(this).remove();
				});
			}
			
		}
	});
	
	var ajaxTimeout, addTimeout;
	
	$("#ajax-search").on("input", function(){
		clearTimeout(ajaxTimeout);
		ajaxTimeout = setTimeout(display_search_results, 500);
	});
	
	function display_search_results(){
		var val = $("#ajax-search").val();
		
		if(val === "" || val.length <= 2){return;}
		
		$("#search-window .ajax-search-spinner").show();
		
		$.post(
			window.ajaxurl, 
			{
				'action'		: 'product_search',
				's'				: val
			}, 
			function(response){
				
				$("#search-window .information").html(response.count);
				
				var $row = $("#search-window .row.products");
				
				$row.find(".entry").addClass("slideOut");
				clearTimeout(addTimeout);
				addTimeout = setTimeout(function(){
					$row.find(".entry.slideOut").remove();
					
					var windowWidth = $(window).width();
					
					$("#search-window .ajax-search-spinner").hide();
					
					var products = response.products;
					
					if(windowWidth < 544){
						insertProducts(products, $row, 1);
					}/*else if(windowWidth < 768){
						
					}*/else if(windowWidth < 992){
						insertProducts(products, $row, 2);
					}/*else if(windowWidth < 1200){
						
					}*/else{
						insertProducts(products, $row, 3);
					}
					
				}, 500);
				
				
			}
		);
	}
	
	function insertProducts(products, $row, columns){
		
		for(var i=0;i<products.length;i++){
			
			var x = (12/columns);
			
			$row.append(
				"<div class='col-xs-" + x + " col-sm-" + x + " col-md-" + x + " col-lg-" + x + " col-xl-" + x + " entry'>"+
					"<a href='"+products[i].url+"'>"+
						"<div class='row'>"+
							"<div class='col-xs-3'><img src='"+products[i].preview+"'></div>"+
							"<div class='col-xs-9'><h5>"+products[i].title + "</h5>" + (products[i].variations !== "" ? "(" + products[i].variations + ")" : "") +"</div>"+
						"</div>"+
					"</a>"+
				"</div>");
		}
		
	}
	
	function adjustSearchHeight(){
		$("#search-window .scrolling").height($(window).height() - parseInt($("#search-window > .container").css("margin-top"), 10) - ($("#search-window div.search").outerHeight(true)) - ($("#search-window p.information").outerHeight(true)) - 50 /*margin-bottom*/);
	}
	
	$(window).resize(function () {
		adjustSearchHeight();
	});
	
	adjustSearchHeight();
	
	$(".autocomplete-state").find("input").each(function(){
		new window.Awesomplete(this, {
			minChars		: 1,
			maxItems		: 10,
			list			: states,
			autoFirst		: true,
			filter			: window.Awesomplete.FILTER_STARTSWITH
		});
	});
	
	$("input[type='tel']").on("input", function(){
		var number = $(this).val(); //remove all non-numbers
		var formatted_number = "";
		
		if(number.length >= 1 && number.substring(0, 1) === "+"){
			if(number.substring(0, 3) === "+41"){
				number = number.substring(3, number.length);
			}else if(number.substring(0, 2) === "+4"){
				number = number.substring(2, number.length);
			}else{
				number = number.substring(1, number.length);
			}
		}else if(number.length >= 2 && number.substring(0, 2) === "00"){
			if(number.length === 2){
				number = number.substring(2, number.length);
			}else{
				number = number.substring(4, number.length);
			}
			
		}
		
		number = number.replace(/\D/g,'');
		if(number !== "" && number.substring(0, 1) !== "0"){
			number = "0" + number;
		}
															/*6+1 space*/				/*8+2 spaces*/
		formatted_number = number.replace(/(.{3})/,"$1 ").replace(/(.{7})/,"$1 ").replace(/(.{10})/,"$1 ");
		
		
		$(this).val(formatted_number.substring(0, Math.min(formatted_number.length, 13)).trim()/*.replace(/(.{3})/g,"$1 ")*/);
	});
	
	function detectPushMenuClose(event){
		if($(event.target).closest(".off-canvas-menu.push-menu").length === 0){
			//click outside the push menu
			togglePushMenu();
			
			$(document).unbind("click", detectPushMenuClose);
			
			return false;
		}
	}
	
	function togglePushMenu(event){
		if($("body > nav.push-menu").hasClass("active")){
			//disable it
			$("body > nav.push-menu").removeClass("active");
			$("body > div, body > .navbar, body > footer").removeClass("pushed");
		}else{
			//enable it
			$("body > nav.push-menu").addClass("active");
			$("body > div, body > .navbar, body > footer").addClass("pushed");
			
			//attach listener to body
			if(event){
				event.stopPropagation();
			}
			$(document).bind("click", detectPushMenuClose);
		}
	}
	$(".mobile-nav-menu-toggle").click(togglePushMenu);
	
	function closeOpenedRows(event){
		
		if(!$(event.target).is('tr.active') && $(event.target).closest("tr.active").length === 0){
			
			$(document).unbind("click touchstart", closeOpenedRows);
		
			$("table.full-collapse tr.active").removeClass("active").find(".collapse").collapse('hide');
			$("table.full-collapse .mobile-collapse.collapse").removeClass("collapse");
		}
	}
	
	if($(window).width() < 576){
		//collapsed table
		$("table.full-collapse .mobile-collapse-toggle").click(function(){
			
			var $tr = $(this).closest("tr");
			
			if($tr.hasClass("active")){
				return;
			}else{
				$tr.siblings(".active").removeClass("active").find(".collapse").collapse('hide');
				$tr.find(".mobile-collapse.collapse").removeClass("collapse");
				$tr.addClass("active").find(".mobile-collapse").collapse("show");
				
				$(document).unbind("click touchstart", closeOpenedRows);
				$(document).bind("click touchstart", closeOpenedRows);
				
				return false;
			}
		});
	}
	
	var loadingPage = false;
	var loadedPages = [];
	var currentPage = 1;
	
	function loadNextPage(){
		var page = currentPage;
		var location = "";
		
		if(loadingPage){return;}
		loadingPage = true;
		
		page++;
		if(window.location.pathname.indexOf("/page/") !== -1){
			location = window.location.pathname.replace(/\/page\/[0-9]+/, "/page/"+page);
		}else{
			location = window.location.pathname.endsWith("/") ? window.location.pathname + "page/"+page : window.location.pathname + "/page/"+page;
		}
		
		location += window.location.search;
		
		if(loadedPages.indexOf(page) === -1){
			
			$(".ajax-archive-spinner").animate({height: 40}, 300 );
			
			$.ajax({
				url: location,
				data: {},
				success: function(data){
					
					$(".ajax-archive-spinner").animate({height: 0}, 300 );
					
					if($("ul.products > li:first-child").hasClass("product-category")){
						//load categories only
						var $products = $(data).find("ul.products");
						$products.find("li:not(.product-category)").remove();
						
						$("ul.products").append($products.html());
						
						if($products.find("li").length > 0){
							currentPage++;
						}
						
					}else{
						//load all the things (products)
						$("ul.products").append($(data).find("ul.products").html());
						
						currentPage++;
					}
					
					loadedPages.push(page);
					loadingPage = false;
					//window.history.pushState("", "", location);
					
				},
				error: function(xhr){
					
					$(".ajax-archive-spinner").animate({height: 0}, 300 );
					
					if(xhr.status === 404) {
						loadedPages.push(page); // prevent future loading of this page
						loadingPage = false;
				    }
				},
				dataType: 'html'
			});
			
		}
	}
	
	if($("body").is(".archive.woocommerce")){
		
		if(window.location.pathname.indexOf("/page/") !== -1){
			currentPage = parseInt(window.location.pathname.split("/page/")[1].replace("/", ""));
		}
		
		//enable infinite scroll
		$(window).scroll(function() {
		   if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
			   //near bottom, load next page
			   loadNextPage();
		   }
		});
		
	}
	
	$(document).on("click", "[data-terms]", function(){
		$('#terms').prop('checked', $(this).attr("data-terms") === "true");
		$('#termModal').modal('hide');
	});
	
	function round2(f){
		return (Math.round(f * 100) / 100).toFixed(2);
	}

}(jQuery));
