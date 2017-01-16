<?php
if (!function_exists("debugEcho")) {

	function debugEcho($string) {
		if (bii_canshow_debug()) {
			echo $string;
		}
	}

	function pre($item, $color = "#000") {
		if (bii_canshow_debug()) {
			echo "<pre style='color:$color'>";
			var_dump($item);
			echo "</pre>";
		}
	}

	function consoleLog($string) {
		if (bii_canshow_debug()) {
			$string = addslashes($string);
			?><script>console.log('<?php echo $string; ?>');</script><?php
		}
	}

	function consoleDump($var) {
		if (bii_canshow_debug()) {
			?><script>console.log('<?php serialize($var); ?>');</script><?php
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

	function logSESSIONVars() {
		foreach ($_SESSION as $key => $item) {
			if (!is_array($item)) {
				$$key = urldecode($item);
				pre("$key => $item");
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

	function startVoyelle($string, $y_is_a_voyelle = true) {
		$voyelle = false;
		$string = strtolower(remove_accents($string));
		$array_voyelles = array("a", "e", "i", "o", "u");
		if ($y_is_a_voyelle) {
			$array_voyelles[] = "y";
		}
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

	function bii_write_log($log) {
		if (WP_DEBUG_LOG) {
			if (is_array($log) || is_object($log)) {
				error_log(print_r($log, true));
			} else {
				error_log($log);
			}
		}
	}

	function bii_makebutton($option, $pluriel = false, $feminin = false, $invert = false) {
		$array_switch = ["désactivé", "activé"];
//	$array_switch = ["désactivé", "activé"];
		$gotoval = 1;
		$value = get_option($option);
		if ($value == 1) {
			$gotoval = 0;
			$facheck = "fa-check-square-o";
		}
		if ($invert) {
			if ($value == 1) {
				$value = 0;
			} else {
				$value = 1;
			}
		}
		$facheck = "fa-square-o";
		if ($value == 1) {
			$facheck = "fa-check-square-o";
		}

		$valtexte = $array_switch[$value];
		if ($feminin) {
			$valtexte.="e";
		}
		if ($pluriel) {
			$valtexte.="s";
		}
		$button = "<button data-newval='$gotoval' data-option='$option' class='bii_upval btn btn-info'><i class='fa $facheck'></i> $valtexte</button>";
		return $button;
	}

	function bii_makeinput($option, $type = "text", $class = "", $options = [], $echo = true) {
		if (!get_option($option)) {
			update_option($option, 0);
		}
		$value = stripcslashes(get_option($option));
		$class .= " form-control";
		if ($type == "textarea") {
			$return = "<textarea class='$class' id='$option' name='$option'>$value</textarea>";
		} else if ($type == "select") {
//			pre($value);
			$return = "<select class='$class' id='$option' name='$option'>";			
			foreach ($options as $optid => $name) {				
				$selected = "";
				if (stripcslashes($optid) == $value) {
					$selected = "selected='selected'";
//					pre($optid);
				}				
				$return.= "<option value='$optid' $selected>$name</option>";
			}
			$return .= "</select>";
		} else if ($type == "wpeditor") {
			$echo = false;
			$return = "";
			wp_editor($value, $value);
		} elseif ($type == "image") {
			$return = "";
			$return .= "<div class='form-inline'>"
				. "<div class='previsualisation'>

						<img id='image-preview' width='100' height='100' src='$value' alt='image' />

					</div>"
				. "<label for='$option'>" . __('Photo 1') . "</label><br />"
				. "<div class='item $class form-group'>"
				. "<input id='$option' type='text' name='$option' class='form-control' value='$value' />"
				. "<input id='upload_$option' class='input-upload $option form-control'  type='button' value='Parcourir' />"
				. "</div>"
				. "</div>"
				. "<div class='spacer'></div>"
				. "<script>"
				. "jQuery('#upload_$option').click(function(e) {
						var custom_uploader;
						e.preventDefault();
						if (custom_uploader) {
							custom_uploader.open();
							return;
						}
						custom_uploader = wp.media.frames.file_frame = wp.media({
							title: 'Choose Image',
							button: {
								text: 'Choose Image'
							},
							multiple: false
						});
						custom_uploader.on('select', function () {
							attachment = custom_uploader.state().get('selection').first().toJSON();
							jQuery('#$option').val(attachment.url);
							jQuery('#$option').trigger('keyup');
						});
						custom_uploader.open();"
				. "});jQuery('#$option').on('keyup', function () {
						console.log('keyup');
						var src = jQuery(this).val();
						var image = \"<img id='image-preview' width='100' height='100' src='\" + src + \"' alt='image' />\";
						jQuery('.previsualisation').html(image);
						jQuery('#image-preview').error(function () {
							jQuery(this).attr({
								'src': '$value'
							});
						});
					});"
				. "</script>";
		} else {
			$return = "<input type='$type' class='$class' id='$option' name='$option' value='$value' />";
		}
		if ($echo) {
			echo $return;
		}
		return $return;
	}

	function bii_makestuffbox($option, $name, $type = "text", $class_stuffbox = "", $options = [], $class_input = "") {
		if (!$class_stuffbox || $type == "wpeditor") {
			$class_stuffbox = "col-xxs-12";
		}
		?>
		<div id="<?= $option ?>_div" class="stuffbox <?= $class_stuffbox; ?> ">
			<h3><label for="<?= $option ?>"><?= $name ?></label></h3>
			<div class="inside">
				<?php bii_makeinput($option, $type, $class_input, $options); ?>
			</div>
		</div>
		<?php
	}

	function divisionbootstrap($nb) {
		if ($nb == 0) {
			return "hidden";
		}
		if (strpos($nb, "/")) {
			$expl = explode("/", $nb);
			$nb = $expl[0] / $expl[1];
		}
		$r = (int) 12 / $nb;
		return $r;
	}

	function bootstrap_builder($nb_cols_md = 3, $nb_cols_sm = 2, $nb_cols_xs = 1, $nb_cols_xxs = 1, $nb_cols_lg = 4, $use_visual_composer = true) {
		$douziemes = [
//			"xxs"=>divisionbootstrap($nb_cols_xxs),
			"xs" => divisionbootstrap($nb_cols_xs),
			"sm" => divisionbootstrap($nb_cols_sm),
			"md" => divisionbootstrap($nb_cols_md),
			"lg" => divisionbootstrap($nb_cols_lg),
		];
		$class = "";
		$nbprec = 0;
		$prefix = "";
		if ($use_visual_composer) {
			$prefix = "vc_";
		}
		foreach ($douziemes as $screen => $nb) {
			if ($nb == "hidden") {
				$class .= " $prefix$nb-$screen";
			} elseif ($nb != $nbprec) {
				$class .= " prefixcol-$screen-$nb";
			}
			$nbprec = $nb;
		}
		bii_write_log($class);
		return $class;
	}

	function bii_cvnbst($nombre) {
		$nb1 = Array('un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf');

		$nb2 = Array('vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt');

		# Décomposition du chiffre
		# Séparation du nombre entier et des décimales
		if (preg_match("/\b,\b/i", $nombre)) {
			$nombre = explode(',', $nombre);
		} else {
			$nombre = explode('.', $nombre);
		}
		$nmb = $nombre[0];

		# Décomposition du nombre entier par tranche de 3 nombre (centaine, dizaine, unitaire)
		$i = 0;
		while (strlen($nmb) > 0) {
			$nbtmp[$i] = substr($nmb, -3);
			if (strlen($nmb) > 3) {
				$nmb = substr($nmb, 0, strlen($nmb) - 3);
			} else {
				$nmb = '';
			}
			$i++;
		}
		$nblet = '';
		## Taitement du côté entier
		for ($i = 1; $i >= 0; $i--) {
			if (strlen(trim($nbtmp[$i])) == 3) {
				$ntmp = substr($nbtmp[$i], 1);

				if (substr($nbtmp[$i], 0, 1) <> 1 && substr($nbtmp[$i], 0, 1) <> 0) {
					$nblet.=$nb1[substr($nbtmp[$i], 0, 1) - 1];
					if ($ntmp <> 0) {
						$nblet.=' cent ';
					} else {
						$nblet.=' cents ';
					}
				} elseif (substr($nbtmp[$i], 0, 1) <> 0) {
					$nblet.='cent ';
				}
			} else {
				$ntmp = $nbtmp[$i];
			}

			if ($ntmp > 0 && $ntmp < 20) {
				if (!($i == 1 && $nbtmp[$i] == 1)) {
					$nblet.=$nb1[$ntmp - 1] . ' ';
				}
			}

			if ($ntmp >= 20 && $ntmp < 60) {
				switch (substr($ntmp, 1, 1)) {
					case 1 : $sep = ' et ';
						break;
					case 0 : $sep = '';
						break;
					default: $sep = '-';
				}
				$nblet.=$nb2[substr($ntmp, 0, 1) - 2] . $sep . $nb1[substr($ntmp, 1, 1) - 1] . ' ';
			}

			if ($ntmp >= 60 && $ntmp < 80) {
				$nblet.=$nb2[4];
				switch (substr($ntmp, 1, 1)) {
					case 1 : $sep = ' et ';
						break;
					case 0 : $sep = '';
						break;
					default: $sep = '-';
				}

				if (substr($ntmp, 0, 1) <> 7) {
					$nblet.=$sep . $nb1[substr($ntmp, 1, 1) - 1] . ' ';
				} else {
					if (substr($ntmp, 1, 1) + 9 == 9)
						$sep = '-';
					$nblet.=$sep . $nb1[substr($ntmp, 1, 1) + 9] . ' ';
				}
			}

			if ($ntmp >= 80 && $ntmp < 100) {
				$nblet.=$nb2[6];
				switch (substr($ntmp, 1, 1)) {
					case 1 : $sep = ' et ';
						break;
					case 0 : $sep = '';
						break;
					default: $sep = '-';
				}

				//if(substr($ntmp,1,1)<>0){
				if (substr($ntmp, 0, 1) <> 9) {
					$nblet.=$sep . $nb1[substr($ntmp, 1, 1) - 1];
					if (substr($ntmp, 1, 1) == 0)
						$nblet.='s';
				}else {
					if (substr($ntmp, 1, 1) == 0)
						$sep = '-';
					$nblet.=$sep . $nb1[substr($ntmp, 1, 1) + 9];
				}
				$nblet.=' ';
				//}elseif(substr($ntmp,0,1)<>9){
				//    $nblet.='s ';
				//}else{
				//    $nblet.=' ';
				//}
			}

			if ($i == 1 && $nbtmp[$i] <> 0) {
				if ($nbtmp[$i] > 1) {
					$nblet.='milles ';
				} else {
					$nblet.='mille ';
				}
			}
		}

		if ($nombre[0] > 1)
			$nblet.='euros ';
		if ($nombre[0] == 1)
			$nblet.='euro ';

		## Traitement du côté décimale
		if ($nombre[0] > 0 && $nombre[1] > 0)
			$nblet.=' et ';
		$ntmp = substr($nombre[1], 0, 2);
		if (!empty($ntmp)) {
			if ($ntmp > 0 && $ntmp < 20) {
				$nblet.=$nb1[$ntmp - 1] . ' ';
			}

			if ($ntmp >= 20 && $ntmp < 60) {
				switch (substr($ntmp, 1, 1)) {
					case 1 : $sep = ' et ';
						break;
					case 0 : $sep = '';
						break;
					default: $sep = '-';
				}
				$nblet.=$nb2[substr($ntmp, 0, 1) - 2] . $sep . $nb1[substr($ntmp, 1, 1) - 1] . ' ';
			}

			if ($ntmp >= 60 && $ntmp < 80) {
				$nblet.=$nb2[4];
				switch (substr($ntmp, 1, 1)) {
					case 1 : $sep = ' et ';
						break;
					case 0 : $sep = '';
						break;
					default: $sep = '-';
				}

				if (substr($ntmp, 0, 1) <> 7) {
					$nblet.=$sep . $nb1[substr($ntmp, 1, 1) - 1] . ' ';
				} else {
					if (substr($ntmp, 1, 1) + 9 == 9)
						$sep = '-';
					$nblet.=$sep . $nb1[substr($ntmp, 1, 1) + 9] . ' ';
				}
			}

			if ($ntmp >= 80 && $ntmp < 100) {
				$nblet.=$nb2[6];
				switch (substr($ntmp, 1, 1)) {
					case 0 : $sep = '';
						break;
					default: $sep = '-';
				}

				if (substr($ntmp, 0, 1) <> 9) {
					$nblet.=$sep . $nb1[substr($ntmp, 1, 1) - 1];
					if (substr($ntmp, 1, 1) == 0)
						$nblet.='s';
				}else {
					if (substr($ntmp, 1, 1) == 0)
						$sep = '-';
					$nblet.=$sep . $nb1[substr($ntmp, 1, 1) + 9];
				}
				$nblet.=' ';
			}

			if ($ntmp <> 0 && !empty($ntmp)) {
				if ($ntmp > 1) {
					$nblet.='cents ';
				} else {
					$nblet.='cent ';
				}
			}
		}

		return $nblet;
	}

//Take me to the place where you go, where nobody knows
	function is_it_night_or_day($time = null, $decalagehoraire = 2) {

		if (!$time) {
			$time = time();
		}
		$heure = date("G");
		$heure += $decalagehoraire;
		$heure = $heure % 24;
//	echo $heure;
		if ($heure < 6 || $heure > 22) {
			return "night";
		} else {
			return "day";
		}
	}
	
	function bii_custom_log($log, $addprefix = "", $all = true) {
        if ($all || bii_canshow_debug()) {
            $url = bii_debug_logs_custom_path;
            $res = fopen($url, "a+");		
            if ($res !== false) {			
                if (is_array($log) || is_object($log)) {
                    $log = print_r($log, true);
                }
                $date = date("d/m/Y H:i:s");
                $prefix = "[Bii_cl v" . bii_debug_version . " $date $addprefix] ";
                $suffix = "\n";
                $log = $prefix . $log.$suffix;
                fwrite($res, $log);
                fclose($res);
    //			pre($log,"green");
            }else{
                bii_write_log($log);
    //			pre($log,"red");
            }
        }
	}

}