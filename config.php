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

function debugEcho($string){
	if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") {
		echo $string;
	}
}
function pre($item,$color="#000"){
	if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") {
		echo "<pre style='color:$color'>";
		var_dump($item);
		echo "</pre>";
	}
}

function consoleLog($string) {
	if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") {
		$string = addslashes($string);
		?><script>console.log('<?php echo $string; ?>');</script><?php
	}
}

function consoleDump($var) {
	if ($_SERVER["REMOTE_ADDR"] == "77.154.194.84") {
//	ob_start();
//	var_dump($var);
//	$string = ob_get_contents();
//	ob_end_clean();
		?><script>console.log('<?php var_dump($var); ?>');</script><?php
	}
}

function logQueryVars($afficherNull = false) {
	global $wp_query;
	foreach ($wp_query->query_vars as $key => $item) {
		if (!is_array($item)) {
			$$key = urldecode($item);
			if ($afficherNull) {
				consoleLog("$key => $item");
			} else {
				if ($item != "") {
					consoleLog("$key => $item");
				}
			}
		}
	}
}

function logRequestVars() {
	foreach ($_REQUEST as $key => $item) {
		if (!is_array($item)) {
			$$key = urldecode($item);
			consoleLog("$key => $item");
		}
	}
}

function logGETVars() {
	foreach ($_GET as $key => $item) {
		if (!is_array($item)) {
			$$key = urldecode($item);
			consoleLog("$key => $item");
		} else {
			$log = "$key => {";
			foreach ($item as $key2 => $val) {
				$log .= " $key2=>$val";
			}
			$log .= "}";
			consoleLog($log);
		}
	}
}

function headersOK($url) {
	error_log("URL : " . $url);
	$return = false;
	$headers = @get_headers($url, 1);

	error_log("HEADER : " . print_r($headers, true));
	if ($headers[0] == 'HTTP/1.1 200 OK') {
		$return = true;
	}

	return $return;
}

function isHTTP($url) {
	$return = false;
	if (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://') {
		$return = true;
	}
	return $return;
}

function startVoyelle($string) {
	$voyelle = false;
	$string = strtolower(remove_accents($string));
	$array_voyelles = array("a", "e", "i", "o", "u");
	if (in_array($string[0], $array_voyelles)) {
		$voyelle = true;
	}
	return $voyelle;
}

function stripAccents($string) {
	$string = htmlentities($string, ENT_NOQUOTES, 'utf-8');
	$string = preg_replace('#\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;#', '\1', $string);
	$string = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $string);
	$string = preg_replace('#\&[^;]+\;#', '', $string);
	return $string;
}

function stripAccentsLiens($string) {
	$string = mb_strtolower($string, 'UTF-8');
	$string = stripAccents($string);

	$search = array('@[ ]@i', '@[\']@i', '@[^a-zA-Z0-9_-]@');
	$replace = array('-', '-', '');

	$string = preg_replace($search, $replace, $string);
	$string = str_replace('--', '-', $string);
	$string = str_replace('--', '-', $string);

	return $string;
}

function stripAccentsToMaj($string) {
	$string = stripAccentsLiens($string);
	$string = str_replace('-', ' ', $string);
	$string = strtoupper($string);
	return $string;
}

function url_exists($url) {
	$file_headers = @get_headers($url);
	if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
		$exists = false;
	} else {
		$exists = true;
	}
	return $exists;
}
