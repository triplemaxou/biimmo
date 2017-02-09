<?php

class users extends global_class {

	protected $ID;
	protected $user_login;
	protected $user_pass;
	protected $user_nicename;
	protected $user_email;
	protected $user_url;
	protected $user_registred;
	protected $user_activation_key;
	protected $user_status;
	protected $display_name;

	public function user_email() {
		return $this->user_email;
	}

	public static function identifiant() {
		return "ID";
	}

	public function get_rsItems() {
		$liste = usermeta::multiple_from_id_key($this->id(), "requete_sauvegardee");
		//pre($liste,'red');
		$listeRS = [];
		if ((bool) $liste) {
			foreach ($liste as $id) {
				$item = new usermeta($id);
				$listeRS[] = $item;
			}
		}
		//pre($listeRS,"#A4B0CA");
		return $listeRS;
	}

	public static function users_alert() {
		$users = static::all_id();
		$alerts = [];
		foreach ($users as $user_id) {
			$user = new static($user_id);
			$alerts[$user_id] = $user->get_rsItems();
		}
		return $alerts;
	}

	public static function users_search() {
		$users = static::all_id();
		$alerts = [];
		foreach ($users as $user_id) {
			$user = new static($user_id);
			$alerts[$user_id] = $user->getBiensSearched();
		}
		return $alerts;
	}

    /**
     * ressort une liste de en fonctions des form de recherche enregistré
     * @return array
     */
	public function getBiensSearched() {
		$liste_biens = [];
		$rs_list = $this->get_rsItems();
		foreach ($rs_list as $rsItem) {
			$liste_biens = array_merge($liste_biens, $rsItem->liste_biens());
		}
		
		return $liste_biens;
	}
    
    /**
     * envoi un mail à tous les utilisateurs de la base.
     */
	public static function sendmailToAll() {
		$users = static::all_id();
        pre(count($users) . ' users', "blue");
		foreach ($users as $user_id) {
            if ($user_id == 1) {
                var_dump($user_id);
                $user = new static($user_id);
                $user->sendmail();
            }
		}
//		registred_dates::insertorupdate("date_envoi_mail");
	}

    /**
     * Envoi un mail si des bien correspondent à leurs recherhces enregistré
     */
	public function sendmail() {
		$liste = $this->getBiensSearched();
		//pre($liste, 'yellow');
		if (count($liste)) {
			$to_email = $this->user_email;
			$from_email = "contact@lemaistre-immo.com";
			$email_subject = "Votre alerte mail sur " . get_bloginfo("name");


			$email_body = annonce::mailFromListe($liste,10);

			$header = 'Content-type: text/html; charset=utf-8' . "\r\n";

			$header .= 'From: ' . get_bloginfo("name") . " <" . $from_email . "> \r\n";

//			wp_mail($to_email, $email_subject, $email_body, $header);
			pre($to_email,"red");
			debugEcho($email_body);
		}
	}

}
