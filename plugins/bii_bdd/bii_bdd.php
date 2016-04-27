<?php
/*
  Plugin Name: BiiBDD
  Description: Ajoute des classes de base de données
  Version: 1.0
  Author: Biilink Agency
  Author URI: http://biilink.com/
  License: GPL2
 */
define('bii_bdd_version', '1.0');

add_action("bii_informations", function() {
	?>
	<tr><td>La base de données des communes est  </td><td><?= bii_makebutton("bii_use_bddcommunes", 0, 1); ?></td></tr>
	<tr><td>La base de données spécifique au plugin est  </td><td><?= bii_makebutton("bii_use_bddplugin", 0, 1); ?></td></tr>
	<?php
});
add_action("bii_options_submit", function() {
	$tableaucheck = [
		"bii_host_bddcommunes", "bii_user_bddcommunes", "bii_name_bddcommunes", "bii_pwd_bddcommunes",
		"bii_host_bddplugin", "bii_user_bddplugin", "bii_name_bddplugin", "bii_pwd_bddplugin",
	];
	foreach ($tableaucheck as $itemtocheck) {
		if (isset($_POST[$itemtocheck])) {
			update_option($itemtocheck, $_POST[$itemtocheck]);
		}
	}
}, 5);
if (get_option("bii_use_bddplugin") || get_option("bii_use_bddcommunes")) {
	add_action("bii_options_title", function() {
		?>
		<li role="presentation" class="hide-relative " data-relative="pl-bdd"><i class="fa fa-database"></i> Bases de données</li>
		<?php
	});
	add_action("bii_options", function() {
		?>
		<div class="col-xxs-12 pl-bdd bii_option hidden">
			<?php
			if (get_option("bii_use_bddcommunes")) {
				bii_makestuffbox("bii_host_bddcommunes", "Host BDD Communes", "text", "col-xxs-12 col-sm-6 col-md-3");
				bii_makestuffbox("bii_user_bddcommunes", "User BDD Communes", "text", "col-xxs-12 col-sm-6 col-md-3");
				bii_makestuffbox("bii_name_bddcommunes", "Name BDD Communes", "text", "col-xxs-12 col-sm-6 col-md-3");
				bii_makestuffbox("bii_pwd_bddcommunes", "Pwd BDD Communes", "password", "col-xxs-12 col-sm-6 col-md-3");
			}
			if (get_option("bii_use_bddplugin")) {
				bii_makestuffbox("bii_host_bddplugin", "Host BDD Plugin", "text", "col-xxs-12 col-sm-6 col-md-3");
				bii_makestuffbox("bii_user_bddplugin", "User BDD Plugin", "text", "col-xxs-12 col-sm-6 col-md-3");
				bii_makestuffbox("bii_name_bddplugin", "Name BDD Plugin", "text", "col-xxs-12 col-sm-6 col-md-3");
				bii_makestuffbox("bii_pwd_bddplugin", "Pwd BDD Plugin", "password", "col-xxs-12 col-sm-6 col-md-3");
			}
			?>
		</div>
		<?php
	});
}