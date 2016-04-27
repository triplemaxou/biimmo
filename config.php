<?php

function bii_listeClass() {
	$list = [
		"rpdo",
		"template",
		"global_class",
		"bii_items",
		"agence",
		"annonce",
		"annonce_image",
		"negociateur",
		"posts",
		"terms",
		"term_taxonomy",
		"postmeta",
		"users",
		"usermeta",
		"bddcommune_items",
		"villes_france",
		"registred_dates",
	];
	return $list;
}

function bii_includeClass() {
	$liste = bii_listeClass();
	$pdpf = plugin_dir_path(__FILE__);
	foreach ($liste as $item) {
		require_once($pdpf . "/class/$item.class.php");
	}
}

bii_includeClass();

function bii_insertBDD() {

	$list = ["agence", "annonce", "annonce_image", "negociateur"];
	foreach ($list as $item) {
		$item::autoTable(true);
	}
}

//bii_insertBDD();

add_action("bii_options_submit", function() {
	logRequestVars();
},10);
add_action("bii_options_title", function() {
	?>
	<li role="presentation" class="hide-relative hide-publier active" data-relative="pl-passerelle"><i class="fa fa-arrow-right"></i><i class="fa fa-database"></i> Passerelle</li>
	<?php
}, 1);
add_action("bii_options", function() {
	?>
	
	<div class="col-xxs-12 pl-passerelle bii_option ">
		<button class="btn btn-primary import" id="import-1" data-from="0" data-to="330"><i class="fa fa-arrow-right"></i><i class="fa fa-database"></i> Importer les données 0 à 330 <i class="fa fa-spinner hidden"></i></button>
	<button class="btn btn-primary import" id="import-2" data-from="330" data-to="660"><i class="fa fa-arrow-right"></i><i class="fa fa-database"></i> Importer les données 331 à 660 <i class="fa fa-spinner hidden"></i></button>
	<button class="btn btn-primary import" id="import-3" data-from="660" data-to="990"><i class="fa fa-arrow-right"></i><i class="fa fa-database"></i> Importer les données 661 à 990 <i class="fa fa-spinner hidden"></i></button>
	<button class="btn btn-primary vidercache" id="vidercache" ><i class="fa fa-refresh"></i> Vider le cache</button>
	<p>
		<span class="expl-import hidden">
			Veuillez patienter, cette opération peut prendre 10 minutes.
		</span>
		<span class="ok-import hidden">
			L'import est terminé
		</span>
		<?php ?>
	</p>
	</div>
	<?php
}, 1);
/*
 * 
 * Fonctions utilitaires
 * 
 */

function setFilter(&$limit = "") {
	$filter = "";
	if (isset($_REQUEST["filter"])) {
		$filterbrut = $_REQUEST["filter"];

		$expl1 = explode('$AND$', $filterbrut);
		foreach ($expl1 as $item) {
			$expl = explode("$", $item);
			$champ_filter = $expl[0];
			$operator = $expl[1];
			$value_filter = '"' . $expl[2] . '"';

			if ($operator == "EQ") {
				$operator = "=";
			}
			if ($operator == "NOT") {
				$operator = "NOT IN (";
				$value_filter .= ")";
			}
			if ($operator == "IN") {
				$operator = "IN (;
			$value_filter .= )";
			}
			if ($operator == "LT") {
				$operator = "<";
			}
			if ($operator == "GT") {
				$operator = ">";
			}
			if ($operator == "LIKE") {
				$operator = "LIKE ";
				$value_filter = '"%' . $expl[2] . '%"';
				$value_filter .= "";
			}
			if ($operator == "BEGINWITH") {
				$operator = "LIKE ";
				$value_filter = '"' . $expl[2] . '%"';
				$value_filter .= "";
			}
			if ($operator == "ENDWITH") {
				$operator = "LIKE ";
				$value_filter = '"%' . $expl[2] . '"';
				$value_filter .= "";
			}

			$filter .= " and $champ_filter $operator $value_filter";
		}
	}
	if (isset($_REQUEST["limit"])) {
		$limit.= " limit " . $_REQUEST["limit"];
	}
	return $filter;
}

function autoRemplissageFilter() {
	$filter = array();
	if (isset($_REQUEST["filter"])) {
		$filterbrut = $_REQUEST["filter"];

		$expl1 = explode('$AND$', $filterbrut);
		foreach ($expl1 as $item) {
			$expl = explode("$", $item);
			$champ_filter = $expl[0];
			$operator = $expl[1];
			$value_filter = $expl[2];
			$filter[] = array(
				"champ_filter" => $champ_filter,
				"operator" => $operator,
				"value_filter" => $value_filter,
			);
		}
	}

	return $filter;
}
