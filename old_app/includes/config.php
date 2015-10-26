<?php 

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

//DATABAZA PRIPOJENIE
define('DB_SERVER',             'localhost'); // eg, localhost - should not be empty for productive servers
define('DB_SERVER_USERNAME',    'homestead');
define('DB_SERVER_PASSWORD',    'secret');
define('DB_DATABASE',           'homestead');
define('DB_ENCODING',           'utf8');
define('USE_PCONNECT',          'false'); 
define('STORE_SESSIONS',        'xx'); // leave empty '' for default handler or set to 'mysql'

define('ROOT_PATH' , '/');
define('FULL_MODULES_PATH', ROOT_PATH.'includes/modules/');


//cesty z rootu k modulom
$g_modules_array = array('reservation' => array('access_key' => 'dashboard.php?section_id=3'),
						'experiment' => array('acces_key' => ''));

//mobilna aplikacia
define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXISTED', 2); 
?>
