<?php
// includes/configure.php
// nastavenia - podobny subor je aj v admine

// * DIR_FS_* = Filesystem directories (local/physical)
// * DIR_WS_* = Webserver directories (virtual/URL)  
define('HTTPS_SERVER', ''); // eg, https://localhost - should not be empty for productive servers
define('ENABLE_SSL', false); // secure webserver for checkout procedure?
define('HTTPS_COOKIE_DOMAIN', '');
define('HTTP_COOKIE_PATH', '');
define('HTTPS_COOKIE_PATH', '');
define('DIR_WS_HTTP_CATALOG', '');
define('DIR_WS_HTTPS_CATALOG', '');
define('DIR_WS_IMAGES', 'images/');
define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');
define('DIR_WS_INCLUDES', 'includes/');
define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/');
define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');


?>