<?php 
require_once 'includes/application_top.php';

//vynulovanie coocie
setcookie('siteAuth');

//vynulovanie sessions
unset($_SESSION['user_id']);
unset($_SESSION['username']);

//session_regenerate_id();
//session_destroy();


header('Location: login.php?loggedout=true ');
exit();

?>