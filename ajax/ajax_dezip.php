<?php

ini_set('display_errors', '1');
require_once("/web/clients/heuzimmo/www.heuze-immo.fr/wp-content/plugins/biimmo/config.php");
$message = bii_items::dezip();

echo $message;
