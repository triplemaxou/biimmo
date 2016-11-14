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
}, 10);
add_action("bii_options_title", function() {
	?>
	<li role="presentation" class="hide-relative hide-publier active" data-relative="pl-passerelle" title="Date de la dernière tentative de passerelle : <?= date("d/m/Y H:i:s", get_option("bii_last_paserelle_try")); ?>"><i class="fa fa-arrow-right"></i><i class="fa fa-database"></i> Passerelle</li>
	<?php
}, 1);
add_action("bii_options", function() {
	$listereload = annonce::liste_reload();
	$count = count($listereload);
	$s = "s";
	if ($count == 1) {
		$s = "";
	}
	$fromtolist = [
		[0, 200],
		[200, 400],
		[400, 600],
		[600, 800],
		[800, 1000],
	];
	$i = 1;
	?>	
	<div class="col-xxs-12 pl-passerelle bii_option ">
		<?php
		foreach ($fromtolist as $fromto) {
			$from = $fromto[0];
			$to = $fromto[1];
			$from_ = $from . "_";
			?>
			<button class="btn btn-primary import" id="import-<?= $i; ?>" data-from="<?= $from; ?>" data-to="<?= $to; ?>" title="Date de la dernière passerelle : <?= date("d/m/Y H:i:s", get_option("bii_last_paserelle_$from_$to")); ?>" >
				<i class="fa fa-arrow-right"></i><i class="fa fa-database"></i> Importer les données <?= $from; ?> à <?= $to; ?> <i class="fa fa-spinner hidden"></i>
			</button>
			<?php
			++$i;
		}
		?>
		<button class="btn btn-primary vidercache" id="vidercache" title="Date de la dernière passerelle : <?= date("d/m/Y H:i:s", get_option("bii_last_paserelle")); ?>">
			<i class="fa fa-refresh"></i> Vider le cache
		</button>
		<?php if ($count) { ?>
			<button class="btn btn-warning reload">Il y a <?= $count; ?> bien<?= $s; ?> sans photos</button>
		<?php } ?>
			<button class="btn btn-info count-doublons">Voir le nombre de doublons <i class="fa fa-spinner hidden"></i></button>
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

function bii_SC_nb_annonces($args = [], $content = null) {
	$where = "1=1";
	if (isset($args["where"])) {
		$where = $args["where"];
	}
	return annonce::nb_annonces($where);
}

add_shortcode("bii_nb_biens", "bii_SC_nb_annonces");

function bii_stripabvr($content) {
	$toreplace = [
		"arr.cuis.",
		"chbres ",
		"chbres.",
		"chs,",
		"gde ",
		"Gde ",
		"gd ",
		"Gd ",
		"cuis.",
		"poss.",
		"Poss.",
		"amén.",
		"corpo.",
		"ch.",
		"Chauff.",
		"ind.",
		"cuisine am.",
		"chbr ",
		"ds ",
		"ds ",
		"APPT ",
		"PROCH ",
		"SDD ",
	];
	$replace = [
		"arrière cuisine",
		"chambres ",
		"chambres",
		"chambres,",
		"grande ",
		"Grande ",
		"grand ",
		"Grand ",
		"cuisine",
		"possibilité",
		"Possibilité",
		"aménagée",
		"copropriété",
		"chambre",
		"Chauffage",
		"individuel",
		"cuisine américaine",
		"chambre ",
		"dans ",
		"Dans ",
		"APPARTEMENT ",
		"PROCHE ",
		"salle de douche ",
	];
	$ret = str_replace($toreplace, $replace, $content);
	return $ret;
}

add_filter("bii_immo_stripabvr", "bii_stripabvr", 10, 1);
