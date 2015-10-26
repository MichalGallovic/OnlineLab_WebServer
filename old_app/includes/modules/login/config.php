<?php 


/*DATABES SETTINGS */
$dbServer           = DB_SERVER;
$dbName             = DB_DATABASE;
$dbServerUsername   = DB_SERVER_USERNAME;
$dbServerPassword   = DB_SERVER_PASSWORD;
$dbEncoding         = DB_ENCODING;


/* BASIC SETTINGS */
define('LOGIN_MODUL_NAME','login');//nazov modulu v adresarovej strukture
define('AUTOLOGIN', TRUE); // set true for display autologin checkbox
define('SECRET_AREA_PAGE', 'dashboard.php');//name of script where you  get after successfull login
define('COOKIE_NAME_AUTOLOGIN' , 'siteAuth');//cookie name for autologin
$cookieTime = (3600 * 24 * 10); 
define('COOKIE_NAME_AUTOLOGIN_TIME' , $cookieTime);// cookie hoding time => 10 days

/* SESSIONS KEYS NEEDED FOR AUTHENTICATION */
$sessionUsernameKey = 'username';
$sessionUseridKey = 'user_id';
$sessionUserHashKey = 'hash';

/*name of fileds in database for login, username,password and uniqe hash*/
$auth_table_fields = array('login' => 'login',
                                        'password' => 'pass',
                                        'user_id' => 'id',
                                        'hash' => 'hash');

/*GOOGLE AUTH */
define('GOOGLE_AUTH', true);// set true for display google auth option
/*STY LDAP AUTH*/
define('STU_LDAP_AUTH',true);// set true for display stu ldap auth option

?>