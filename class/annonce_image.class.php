<?php

class annonce_image extends bii_items {

	protected $id;
	protected $id_annonce;
	protected $attach_id;
	protected $photo;
	protected $alt;
	protected $width;
	protected $height;
	protected $month;
	protected $year;
	protected $etag;

	public static function feminin() {
		return true;
	}

	public function option_value() {
		return $this->photo();
	}

	static function filters_form_arguments($array_selected = array()) {
		?>
		<option class="nb" value="id" data-oldval="id" >Id</option>
		<option class="nb" value="id_annonce" data-oldval="id_annonce" >Id de l'annonce</option>
		<option class="nb" value="attach_id" data-oldval="attach_id" >Id du post attaché</option>
		<option class="nb" value="month" data-oldval="month" >Mois</option>
		<option class="nb" value="year" data-oldval="year" >Année</option>
		<option class="text" value="etag" data-oldval="etag" >Etag</option>
		<?php
	}

	static function getListeProprietes() {
		$array = array(
			"id" => "id",
			"id_annonce" => "Lien vers l'annonce",
			"attach_id" => "Lien vers le post",
			"photo" => "Photo",
			"alt" => "Texte alt",
			"etag" => "etag",
		);
		return $array;
	}

	public static function nom_classe_admin() {
		return "Image";
	}

	public static function display_pagination() {
		return true;
	}

	public static function display_filter() {
		return true;
	}

	public function width() {
		$width = $this->width;
		if ($width == "") {
			$url = $this->url_photo;
			$bloginfourl = get_bloginfo("url");
			$bloginfourl = str_replace("https", "http", $bloginfourl);
			if (strpos($url, "http") !== false) {
				$size = getimagesize($url);
			} else {
				$size = getimagesize($bloginfourl . $url);
			}
			$width = $size[0];
			$this->updateChamps($width, "width");
		}
		return $width;
	}

	public function height() {
		$height = $this->height;
		if ($height == "") {
			$url = $this->url_photo;
			$bloginfourl = get_bloginfo("url");
			$bloginfourl = str_replace("https", "http", $bloginfourl);
			if (strpos($url, "http") !== false) {
				$size = getimagesize($url);
			} else {
				$size = getimagesize($bloginfourl . $url);
			}
			$height = $size[1];
			$this->updateChamps($height, "height");
		}
		return $height;
	}

	public function forme() {
		$height = $this->height();
		$width = $this->width();
//		consoleLog("$width $height");
		$forme = "haute";
		if ($height == $width) {
			$forme = "carre";
		}
		if ($height < $width) {
			$forme = "allonge";
		}
		return $forme;
	}

	public function urlPhoto($resized = false, $w = null, $h = null, $zc = null) {
		$bloginfourl = get_bloginfo("url");
		$urlBase = "$bloginfourl/wp-content/plugins/biimmo/export/";
		if ($this->attach_id) {
			$year = $this->year();
			$month = $this->month();
			$urlBase = "$bloginfourl/wp-content/uploads/$year/$month/";
			$resized = false;
		}
		if (!$w && !$h && !$zc) {
			$resized = false;
		}
		if ($resized) {
			$urlBase .= "image.php?";
			$arr = ["w", "h", "zc"];
			$sep = "";
			foreach ($arr as $val) {
				if ($$val != null) {
					$urlBase .= "$sep$val=" . $$val;
					$sep = "&";
				}
			}
			$urlBase .= $sep . "src=";
		}
		return $this->photo();
	}

	public static function deleteFromAnnonce($id_annonce) {
		$where = "id_annonce = '$id_annonce'";
		static::deleteWhere($where);
	}

	public static function deleteFromReference($ref) {
		$where = "id_annonce in (SELECT id from annonce where reference = '$ref')";
		static::deleteWhere($where);
	}

	static function deleteWhere($where = "") {
		$liste = static::all_id($where);
		foreach ($liste as $id) {

			static::deleteStatic($id);
		}
	}

	public function year() {
		if ($this->year) {
			$year = $this->year;
		} else {
			$year = date('Y');
			$this->updateChamps($year, "year");
		}
		return $year;
	}

	public function month() {
		if ($this->month && $this->month != $this->year) {
			$month = $this->month;
		} else {
			$month = date('m');
			$this->updateChamps($month, "month");
		}
		return $month;
	}

	public function photo_name() {
		return str_replace("http://lemaistre.bbimmo.pro//photo/biens/", "", $this->photo);
	}

	public function addAttachement($id_post = 0) {
		$wp_upload_dir = wp_upload_dir();
		$year = $this->year();
		$month = $this->month();
		$dir = "/web/clients/lemdev/www.lemaistre-immo.fr/wp-content/uploads/img_bien/$year/$month/";
		$url_depl = $dir . $this->photo_name();
		if (!file_exists($dir)) {
			mkdir($dir,0755,true);
		}
		if (!file_exists($url_depl)) {
			$url = $this->photo;
			
			try {
				if (@copy($url, $url_depl)) {
//					echo "<br/>$url moved to $url_depl <br/>";
//					bii_custom_log("$url moved to $url_depl ", "Photos insérées");
				} else {
					$data = file_get_contents($url);
					file_put_contents($url_depl, $data);
//					echo "<br/>$url put to $url_depl <br/>";
					bii_custom_log("$url put to $url_depl ", "Photos insérées");
				}
			} catch (Exception $e) {
				$message = $e->getMessage();
				echo "<br/>Error $message<br/>";
			}
		}
		$filename = $url_depl;

		// The ID of the post this attachment is for.
		$parent_post_id = $id_post;

		// Check the type of file. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype(basename($filename), null);

		// Get the path to the upload directory.

		$guid = $wp_upload_dir['url'] . '/' . basename($filename);
		$where = "guid = '" . $guid . "'";
		if (!posts::nb($where)) {
			// Prepare an array of post data for the attachment.
			$attachment = array(
				'guid' => $guid,
				'post_mime_type' => $filetype['type'],
				'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
				'post_content' => '',
				'post_status' => 'inherit'
			);

			// Insert the attachment.
			$attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);


			// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			// Generate the metadata for the attachment, and update the database record.
			$attach_data = wp_generate_attachment_metadata($attach_id, $filename);
			wp_update_attachment_metadata($attach_id, $attach_data);
		} else {
			$attach_id = posts::fromGuid($guid, "id");
		}

		$this->updateChamps($attach_id, "attach_id");
		if ($parent_post_id) {
			set_post_thumbnail($parent_post_id, $attach_id);
		}
		return $attach_id;
	}

	public function id_annonce_ligneIA() {
		$id_annonce = $this->id_annonce;
		?>
		<td class="id_annonce">			
			<a class="btn btn-success" target="_blank" data-id="<?php echo $this->id; ?>" href="/wp-admin/admin.php?page=annonce_list&filter=id%24EQ%24<?= $id_annonce ?>" >
				<?= $id_annonce ?>
			</a>		
		</td>
		<?php
	}

	public function attach_id_ligneIA() {
		$attach_id = $this->attach_id;
		?>
		<td class="attach_id">			
			<a class="btn btn-success" target="_blank" data-id="<?php echo $this->id; ?>" href="/post.php?post=<?= $attach_id ?>&action=edit" >
				<?= $attach_id ?>
			</a>		
		</td>
		<?php
	}

	public function photo_ligneIA() {
		$url = $this->urlPhoto();
		$alt = $this->alt;
		?>
		<td class="photo">			
			<img src="<?= $url; ?>" height="50" width="50" alt="<?= $alt; ?>"
		</td>
		<?php
	}

	public function purge() {
		//echo $this->attach_id;
		if (false === wp_delete_attachment($this->attach_id, true)) {
			echo "impossible de supprimer $this->attach_id ";
		}
	}

	public static function listeEtag($where) {
		$liste = static::all_id($where);
		$etags = [];
		foreach ($liste as $id) {
			$item = new static($id);
			$etags[] = $item->etag;
		}
		return $etags;
	}

	public static function etagExists($etag) {
		return static::nb("etag = '$etag'");
	}

	public static function fromEtag($etag) {
		$list = static::all_id("etag = '$etag'");
		$item = new static($list[0]);
		return $item;
	}

	public static function deleteFromEtag($etag) {
		static::deleteWhere("etag = '$etag'");
	}

}
