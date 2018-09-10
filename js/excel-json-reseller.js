(function ($) {

	"use strict";
	
	var ajaxurl = window.ajaxurl;
	var XLSX = window.XLSX;
	
	function readExcel(e) {
		var data = e.target.result;
		
		var workbook = XLSX.read(data, {type: 'binary'});

		var sheet_name_list = workbook.SheetNames;
		
		sheet_name_list.forEach(function(y) { /* iterate through sheets */
			
			var excel_resellers	= XLSX.utils.sheet_to_json(workbook.Sheets[y]);
			var resellers		= [];
			
			var column_name, column, i;
			
			var mappings = {
				/*
				column_name : key	
				*/
				"Adresszeile_oben"	: {
					key					: "additional_line_above",
					required			: false
				},
				"Vorname"			: {
					key					: "first_name",
					required			: false
				},
				"Name"				: {
					key					: "last_name",
					required			: false
				},
				"Firma"				: {
					key					: "company",
					required			: false
				},
				"Strasse"			: {
					key					: "address_1",
					required			: true
				},
				"Firmenbezeichnung"	: {
					key					: "address_2",
					required			: false
				},
				"Postfach"			: {
					key					: "post_office_box",
					required			: false
				},
				"PLZ"				: {
					key					: "postcode",
					required			: true
				},
				"Ort"				: {
					key					: "city",
					required			: true
				},
				"Land"				: {
					key					: "country",
					required			: true
				},
				"Telefon_Nr"		: {
					key					: "phone",
					required			: false
				},
				"Homepage"			: {
					key					: "website",
					required			: false
				},
				"E-Mail"			: {
					key					: "email",
					required			: true
				},
				"Adress_Nr"			: {
					key					: "address_number",
					required			: true
				}
				
			};
			
			for(i=0;i<excel_resellers.length;i++){
				var reseller = {
					action						: "import_reseller",
					
					additional_line_above		: "",
					first_name					: "",
					last_name					: "",
					company						: "",
					address_1					: "",
					address_2					: "",
					post_office_box				: "",
					postcode					: "",
					city						: "",
					country						: "",
					phone						: "",
					website						: "",
					mail						: "",
					
					address_number				: "",
					
					display_name				: "",
					
					discounts					: {}
				};
				
				if(("Wiederverkaeufer" in excel_resellers[i]) === false || parseInt(excel_resellers[i].Wiederverkaeufer, 10) !== 1){
					continue;
				}
				
				for(column_name in mappings){
					if(!mappings.hasOwnProperty(column_name)){continue;}
					
					if(column_name in excel_resellers[i] && typeof excel_resellers[i][column_name] !== undefined &&  excel_resellers[i][column_name].trim() !== ""){
						reseller[mappings[column_name].key] = excel_resellers[i][column_name].trim();
					}else if(mappings[column_name].required){
						alert("Column " + column_name + " is not present on line " + i + " but is required!");
						return;
					}
				}
				
				//Generate display name
				if(reseller.company !== ""){
					reseller.display_name	= reseller.company;
					
				}else if(reseller.first_name !== "" && reseller.last_name !== ""){
					reseller.display_name	= reseller.last_name + " " + reseller.first_name;
					
				}else if(reseller.first_name !== ""){
					reseller.display_name	= reseller.first_name;
				}else if(reseller.last_name !== ""){
					reseller.display_name	= reseller.last_name;
				}else{
					alert("There isn't enough data about the reseller on line " + i + " to generate a display name! (first name OR last name OR company name are REQUIRED)");
					return;
				}
				
				//check for discounts
				for(column in excel_resellers[i]){
					if(!excel_resellers[i].hasOwnProperty(column)){continue;}
					
					if(column.indexOf("Rabatt_") !== -1){
						reseller.discounts[column.replace("Rabatt_", "").trim()] = parseInt(excel_resellers[i][column], 10);
					}
				}
				
				resellers.push(reseller);
				
			}
			
			$("#log-filename").append(" Done!");
		
			$("#import-progress").attr("value", "0");
			$("#import-progress").attr("max", resellers.length - 1);
			
			postReseller(resellers, 0, function(){
				
			});		
		});
	}
	
	function postReseller(resellers, index, callback){
		
		if(index >= resellers.length){
			callback();
			return;
		}
		
		$("#log-ajax").append("<p>["+(index+1)+"/"+resellers.length+", "+(Math.round(100*100*(index+1)/resellers.length)/100)+"%] Adding " + resellers[index].display_name + "...</p>");
		
		jQuery.post(ajaxurl, resellers[index], function(response){
			
			$("#log-ajax p:last-child").append(" " + response.message);
			$("#import-progress").attr("value", index);
	
			$(window).scrollTop($(document).height());
			
			postReseller(resellers, index+1, callback);
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