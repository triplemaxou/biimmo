<?php ?>
<h1>Plugin Biimmo</h1>

<button class="btn btn-primary import" id="import-1" data-from="0" data-to="330"><i class="fa fa-arrow-right"></i><i class="fa fa-database"></i> Importer les données 0 à 330 <i class="fa fa-spinner hidden"></i></button>
<button class="btn btn-primary import" id="import-2" data-from="330" data-to="660"><i class="fa fa-arrow-right"></i><i class="fa fa-database"></i> Importer les données 331 à 660 <i class="fa fa-spinner hidden"></i></button>
<button class="btn btn-primary import" id="import-3" data-from="660" data-to="990"><i class="fa fa-arrow-right"></i><i class="fa fa-database"></i> Importer les données 661 à 990 <i class="fa fa-spinner hidden"></i></button>

<p>
	<span class="expl-import hidden">
		Veuillez patienter, cette opération peut prendre 10 minutes.
	</span>
	<span class="ok-import hidden">
		L'import est terminé
	</span>
	<?php
		$annonce = new annonce(246);
		$status_terms = get_the_terms( $annonce->id_post(),"property-status" );
		pre($status_terms);
//		pre($annonce->biens_similaires());
//		$list = users::users_search();
//		pre($list,"blue");
//		$list = users::sendmailToAll();
	?>
</p>

<script>
	jQuery(function ($) {
		console.log("ok");
		$("body").on("click", "#dezip", function () {
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
		$("body").on("click", ".import", function () {
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
				}
			});
		});
	});
</script>
