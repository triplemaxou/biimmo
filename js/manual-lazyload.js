/* Events Lazyload */
var zonebottomplus = 10;
jQuery(function () {
	//events
	try {		
		jQuery("img.lazyquick-lg").lazyload({
			event: "loadquick-lg"
		});
		jQuery("img.lazyquick-md").lazyload({
			event: "loadquick-md"
		});
		jQuery("img.lazyquick-sm").lazyload({
			event: "loadquick-sm"
		});
		jQuery("img.lazyquick-xs").lazyload({
			event: "loadquick-xs"
		});
		jQuery("img.lazyquick-xxs").lazyload({
			event: "loadquick-xxs"
		});
		jQuery("img.lazyquick").lazyload({
			event: "loadquick"
		});
	} catch (e) {
		console.log("lazyload n'existe pas");
		jQuery("img[data-original]").each(function () {
			var dor = jQuery(this).attr("data-original");
			jQuery(this).attr("src", dor);
		});
	}
	loadLazyquick();
	jQuery(window).scroll(function () {
		loadLazyquick();
	});
	jQuery(window).resize(function () {
		loadLazyquick();
	});

});

function loadLazyquick() {
	var size = getWindowSize();
	var zone = zoneFenetre();
	var ytop = zone.ytop;
	var ybottom = zone.ybottom;
	jQuery(".lazyquick:not(.loaded)").each(function () {
		loadquick(jQuery(this), size, ytop, ybottom);
	});
	jQuery(".lazyquick-" + size + ":not(.loaded)").each(function () {
		loadquick(jQuery(this), size, ytop, ybottom);
	});
	jQuery(".lazyquick-" + size + ":not(.loaded)").each(function () {
		loadquick(jQuery(this), size, ytop, ybottom);
	});
	jQuery("#carte-home:not(.loaded)").trigger("lazyquick").addClass("loaded");
}

function loadquick($element, size, zonetop, zonebottom) {
	var top = $element.offset().top;
	var bottom = top + $element.height();
	if (bottom >= zonetop && top <= zonebottom) {
		$element.trigger("loadquick");
		$element.trigger("loadquick-" + size);
		$element.addClass("loaded");
		console.log("loadquick-" + size);
	}
}


function zoneFenetre() {
	var ytop = jQuery(window).scrollTop() * 1;
	var ybottom = jQuery(window).height() * 1 + ytop + zonebottomplus;
	var ret = {'ytop': ytop, 'ybottom': ybottom};
//	console.log(ret);
	return ret;
}



function getWindowSize() {
	var windowsize = "";
	if (window.matchMedia("(max-width: 479px").matches) {
		windowsize = "xxs";
	} else if (window.matchMedia("(max-width: 767px").matches) {
		windowsize = "xs";
	} else if (window.matchMedia("(max-width: 985px").matches) {
		windowsize = "sm";
	} else if (window.matchMedia("(max-width: 1050px").matches) {
		windowsize = "md";
	} else {
		windowsize = "lg";
	}

//	console.log(windowsize);

	return windowsize;
}
	