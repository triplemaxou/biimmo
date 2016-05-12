jQuery(function () {
	checkSEO();
});
function checkSEO() {
	if (jQuery("title").length) {
		var title = jQuery("title").html();
		var color = "green";
		if(title.length > 70){
			color = "orange";
		}
		bii_CL("TITLE : " + title,'color: '+color+'; font-weight: bold;');
	} else {
		bii_CL_error("NO title");
	}
	if (jQuery("h1").length) {
		var h1text = "";
		var i = 0;
		var colst = "";
		var colen = "";
		jQuery("h1").each(function () {
			h1text += colst + jQuery(this).text() + colen;
			++i;

		});
		if (i == 1) {
			bii_CL("H1 : " + h1text,'color: green; font-weight: bold;');
		}else{
			bii_CL_error("H1 : " + h1text);
		}
	} else {
		bii_CL_error('NO h1');
	}
	if (jQuery("meta[name='description']").length) {
		var description = jQuery("meta[name='description']").attr("content");
		var color = "green";
		if(description.length > 300){
			color = "orange";
		}
		bii_CL("DESCRIPTION : " + description,'color: '+color+'; font-weight: bold;');
	} else {
		bii_CL_error("NO metadescription");
	}
	if (jQuery("meta[name='og:description']").length) {
		var description = jQuery("meta[name='og:description']").attr("content");
		var color = "green";
		if(description.length > 300){
			color = "orange";
		}
		bii_CL("OG:DESCRIPTION : " + description,'color: '+color+'; font-weight: bold;');
	} else {
		bii_CL_error("NO meta og:description");
	}
}
jQuery("img").each(function () {
	if ((typeof jQuery(this).attr("alt") === "undefined") || (jQuery(this).attr("alt") == "")) {
		//Alt par défaut
		var nomArray = jQuery(this).attr("src").split("/");
		jQuery(this).attr("alt", nomArray[nomArray.length - 1]);
	}

	if (typeof jQuery(this).attr("height") === "undefined" && jQuery(this).height() != 0) {
		//height par défaut
		var height = parseInt(jQuery(this).height())
			+ parseInt(jQuery(this).css("padding-top").replace("px", ""))
			+ parseInt(jQuery(this).css("padding-bottom").replace("px", ""));
		jQuery(this).attr("height", height);
	}
	if (typeof jQuery(this).attr("width") === "undefined" && jQuery(this).width() != 0) {
		//width par défaut
		var width = parseInt(jQuery(this).width())
			+ parseInt(jQuery(this).css("padding-left").replace("px", ""))
			+ parseInt(jQuery(this).css("padding-right").replace("px", ""));
		jQuery(this).attr("width", width);
	}
});
