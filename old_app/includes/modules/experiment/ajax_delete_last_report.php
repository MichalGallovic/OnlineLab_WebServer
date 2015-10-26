<?php
session_start();

require_once('../../config.php');
require_once('../../db_tables.php');
require_once('../../classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);
require_once('../../functions/general.php');


$mysql->query("DELETE FROM ".TABLE_REPORTS." WHERE id=".$_SESSION['currentReportId']." ")



?>