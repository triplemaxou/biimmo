jQuery(function ($) {
	$(".synchro-photo").click(function () {
		var $fa = $(this).find(".fa-refresh");
		$(this).addClass("btn-info").removeClass("btn-default");
		$fa.addClass("fa-spin");
		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_synchronize_photos'
			},
			dataType: 'html',
			success: function (reponse) {
				$fa.removeClass("fa-spin");
				$(this).addClass("btn-default").removeClass("btn-info");
			}
		});
	});

	$("#chooseinstance").on("change", function () {
		var val = $(this).val();
		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_change_instance',
				'newinstance': val
			},
			dataType: 'html',
			success: function (reponse) {
//				alert("ok");
				location.reload();
			}
		});
	});

	$(".bii_upval").on("click", function (e) {
		e.preventDefault();
		var val = $(this).attr("data-newval");
		var option = $(this).attr("data-option");
		var html = $(this).html();
		var fa = $(this).find(".fa");
		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_change_wp_option',
				'option': option,
				'newval': val
			},
			dataType: 'html',
			success: function (reponse) {
//				alert(reponse);
				location.reload();
			},
			error:function(){
				alert("erreur");
			}
		});
	});

	$(".publier").on("click", function (e) {
		e.preventDefault();
		$("#poststuff").submit();
	});

	$(".hide-relative").on("click", function () {
		$(".hide-relative").removeClass("active");
		$(".bii_option").addClass("hidden");
		$(this).addClass("active");
		var dr = $(this).attr("data-relative");
		$("." + dr).removeClass('hidden');
		if ($(this).hasClass("hide-publier")) {
			$(".publier").addClass("hidden");
		} else {
			$(".publier").removeClass("hidden");
		}
	});

	$(".update-nag ").addClass("hidden");

	$(".formlevels .add-level").on("click", function (e) {
		e.preventDefault();
		//bii_add_new_level
		var index = $("#product_level_count").val() * 1 + 1;
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {action: 'bii_add_new_level', index: index, post_id: $("#project_post_id").val()},
			success: function (newlevel) {
				$(".container-levels").prepend(newlevel);
				$(".container-levels .otherform:first-of-type").hide();
				$("#product_level_count").val(index);
				$(".remove-level").show();
				$(".container-levels .otherform:first-of-type").show(700);
			}
		});
	});
	$(".formlevels .remove-level").on("click", function (e) {
		e.preventDefault();
		var index = $("#product_level_count").val() * 1 - 1;
		$("#product_level_count").val(index);
		$(".container-levels .otherform:first-of-type").hide(500, function () {
			$(this).remove();
		});

		if (index == 1) {
			$(".remove-level").hide();
		}
	});

	console.log("ok");
	$("body").on("click", "#dezip", function (e) {
		e.preventDefault();
		$.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_dezip'
			},
			dataType: 'html',
			success: function (reponse) {
				$("#dezip").removeClass("btn-primary").addClass("btn-success");
			}
		});
	});
	$("body").on("click", ".import", function (e) {
		e.preventDefault();
		var $this = $(this);
		$this.find(".fa-spinner").removeClass("hidden").addClass("fa-pulse");
		$(".expl-import").removeClass("hidden");
		$(".ok-import").addClass("hidden");

		$.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_import',
				'from': $this.attr("data-from"),
				'to': $this.attr("data-to")
			},
			dataType: 'html',
			success: function (reponse) {
				$(".expl-import").addClass("hidden");
				$(".ok-import").removeClass("hidden");
				$this.removeClass("btn-primary").addClass("btn-success");
				$this.find(".fa-spinner").removeClass("fa-pulse");
				if ($this.attr("id") == "import-1") {
					$("#import-2").trigger("click");
				}
				if ($this.attr("id") == "import-2") {
					$("#import-3").trigger("click");
				}
				if ($this.attr("id") == "import-3") {
					$("#vidercache").trigger("click");

				}
			}
		});

	});

	$("body").on("click", "#vidercache", function (e) {
		e.preventDefault();
		alert("Le cache va maintenant se vider");
		var href = $("#wp-admin-bar-purge-all a").attr("href");
		window.location = href;
	});
	$("body").on("click", ".reload", function (e) {
		e.preventDefault();
		$.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_ajax_reload',
			},
			dataType: 'html',
			success: function (reponse) {
//				alert("ok");
				alert("Le cache va maintenant se vider");
				var href = $("#wp-admin-bar-purge-all a").attr("href");
				window.location = href;
			}
		});
	});
});