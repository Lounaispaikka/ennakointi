<?php

//Lougis server path definitions
define('PATH_SERVER', dirname(__FILE__).'/');
define('PATH_PHP', '/usr/share/php/');
define('PATH_PEAR', PATH_PHP.'DB/');
define('PATH_PEAR_HTTP', PATH_PHP.'HTTP/');
define('PATH_DATA', realpath(PATH_SERVER.'../data/').'/');
define('PATH_CHARTS', PATH_DATA.'charts/');
define('PATH_UPFILES', PATH_DATA.'upload_files/');
define('PATH_SCRIPTS', realpath(PATH_SERVER.'../scripts/').'/');

if ( strpos('development', PATH_SERVER) != false ) {
	define('PATH_CACHE', '/cache/e-verkosto/production/');
} else {
	define('PATH_CACHE', '/cache/e-verkosto/development/');
}

//Lougis server settings
define('LOUGIS_PASSWORD_SALT', md5('lounaispaikka'));

date_default_timezone_set('Europe/Helsinki');

require_once(PATH_SERVER.'dataobject/dataobject_autoload.php');
require_once(PATH_SERVER.'utility/LouGIS/debug_functions.php');
?>