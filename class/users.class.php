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
    
    public static function add($login, $email, $tel, $nom, $prenom) {
        $id = 0;
		if (!static::exists($email)) {
			$id = static::insertDefault($login, $email, $tel, $nom, $prenom);
			return $id;
		} else {
			$id = static::from_email($email);
            static::update($id, $nom, $prenom, $tel);
		}
        return $id;
    }
    
    public static function exists($email) {

		$bool = (bool) static::nb("user_email = '$email'");
		return $bool;
	}
    
    public static function insertDefault($login, $email, $tel = '', $nom = '', $prenom = '') {
        
        $displayName = explode('@', $email);
        if ($login == "newsletter") {
            $uid = static::getNextId();
            $login = "newsletter_".$displayName[0].$uid;
        }
        
        // Generate the password and create the user
        $password = wp_generate_password( 8, false );
        
        $userdata = array(
            'user_login'    => $login,
            'user_email'    => $email,
            'user_pass'     => $password,
            'first_name'    => $prenom,
            'last_name'     => $nom,
        );
		
		$user_id = wp_insert_user( $userdata );
        
        if ( ! is_wp_error( $user_id ) ) {
            
            $user = new users($user_id);
            $user->updateChamps(2, 'user_status', ' ID = '.$user_id);
            usermeta::add($user_id, "mobile_number", $tel);
            
            // Email the user
            static::sendMailRegister($email, $login, $password);
            static::mailNewUser($email, $login, $nom, $prenom, $tel);
            return $user_id;
        } else {
            bii_custom_log($user_id->get_error_message());
            return false;
        }
	}
    
    public static function mailNewUser($email, $login, $nom, $prenom, $tel) {
        $subject = utf8_decode(get_bloginfo("name") . " Nouvel utilisateur");
        $message = "Mail : $email\n\r";
        $message .= "Login : $login\n\r";
        $message .= "Nom : $nom\n\r";
        $message .= "Prénom : $prenom\n\r";
        $message .= "Tel : $tel\n\r";
        
        mail("web@groupejador.fr", $subject, utf8_decode($message));
    }
    
    public static function update($user_id, $nom, $prenom, $tel) {
        
        if (!empty($nom)) {
            usermeta::add($user_id, "last_name", $nom);
        }
        if (!empty($prenom)) {
            usermeta::add($user_id, "first_name", $prenom);
        }
        if (!empty($tel)) {
            usermeta::add($user_id, "mobile_number", $tel);
        }
        
    }
    
    public static function from_email($email) {
		$liste = static::all_id("user_email = '$email' ");
		return $liste[0];
	}

    public static function sendMailRegister($email, $login, $password) {
        
        ob_start();
		annonce::headermail();
		?>
        <h4 style="font-size: 18px;height:38px;text-align:center;">Bienvenue</h4>
        <div style='min-height:200px;margin:20px 0;'>
            Suite à l'enregistrement de votre formulaire de recherche, nous vous confirmons votre inscription.<br />
            Vous recevrez régulièrement une liste d'annonces par email correspondant à votre recherche.<br /><br />
            Si vous désirez vous connecter sur notre site, veuillez utiliser les identifiants suivants :<br /><br />
            Login : <?= $login ?><br />
            Mot de passe : <?= $password ?>
        </div>
        <?php
		annonce::footermail();
		$email_body = ob_get_contents();
		ob_end_clean();
        
        $to_email = $email;
        //$to_email = "m.duvalet@groupejador.fr";
        $from_email = "contact@lemaistre-immo.com";
        $email_subject = "Inscription sur " . get_bloginfo("name");

        $header = 'Content-type: text/html; charset=utf-8' . "\r\n";

        $header .= 'From: ' . get_bloginfo("name") . " <" . $from_email . "> \r\n";

        wp_mail($to_email, $email_subject, $email_body, $header);
        
    }
    
	public function get_rsItems() {
		$liste = usermeta::multiple_from_id_key($this->id(), "requete_sauvegardee", "DESC");
		
		$listeRS = [];
		if ((bool) $liste) {
			foreach ($liste as $id) {
				$item = new usermeta($id);
				$listeRS[] = $item;
			}
		}
		
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
	public function getBiensSearched($limit = 10) {
		$liste_biens = [];
		$rs_list = $this->get_rsItems();
        //bii_custom_log("Form search ".var_export($rs_list, true));
		foreach ($rs_list as $rsItem) {
			$liste_biens = array_unique(array_merge($liste_biens, $rsItem->liste_biens()));
            //bii_custom_log("Liste de bien ".var_export($liste_biens, true));
            if (count($liste_biens) >= $limit) {
                //bii_custom_log("Nombre de bien ".count($liste_biens));
                break;
            }
		}
		
		return array_slice ($liste_biens, 0, $limit, true);
	}
    
    /**
     * envoi un mail à tous les utilisateurs de la base.
     */
	public static function sendmailToAll() {
		$users = static::all_id();
        //pre(var_export($users, true) , "blue");
		foreach ($users as $user_id) {
            if ($user_id == 84) {
                //pre($user_id);
                $user = new static($user_id);
                $user->sendmail();
            }
		}
		registred_dates::insertorupdate("date_envoi_mail");
	}

    /**
     * Envoi un mail si des bien correspondent à leurs recherhces enregistré
     */
	public function sendmail() {
		$liste = $this->getBiensSearched();
        //bii_custom_log("Liste FINAL de bien ".var_export($liste, true));
		if (count($liste)) {
			$to_email = $this->user_email;
            //$to_email = "web@groupejador.fr";
			$from_email = "contact@lemaistre-immo.com";
			$email_subject = "Votre alerte mail " . get_bloginfo("name");
            
            $uid = uniqid('unregister_newsletter', true);
            usermeta::add($this->ID, 'unregister_newsletter_key', $uid);

			$email_body = annonce::mailFromListe($liste, $uid);

			$header = 'Content-type: text/html; charset=utf-8' . "\r\n";

			$header .= 'From: ' . get_bloginfo("name") . " <" . $from_email . "> \r\n";

			wp_mail($to_email, $email_subject, $email_body, $header);
			//pre($to_email,"red");
			//debugEcho($email_body);
		}
	}

    /**
     * 
     * @param string $uid
     */
    public static function unregister_newsletter($uid) {
        $args = array(
            'meta_query' => array(
                array(
                    'key'   => 'unregister_newsletter_key',
                    'value' => $uid,
                )
            )
        );
        $user = get_users($args);
        if (count($user) > 0) {
            $user_id = $user[0]->ID;
            delete_user_meta($user_id, 'unregister_newsletter_key');
            delete_user_meta($user_id, 'requete_sauvegardee');
        }
    }
}
