<?php 
  //vsetky konfiguracne nastaveni (pripojenie k DB, tempkaty, konstany..)
  require_once('includes/application_top.php');
  
  //phpinfo();
  //var_dump($_SERVER);
  //exit();
  
  
  // zmena 11.9.2006 - ak nie je prihlaseny, smerujeme rovno na login bez spravy, inak rovno do adminu
 if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
  }else{
  	//header('Location: '.ROOT_PATH.'5/pracovna-plocha/ ');
	header('Location: '.ROOT_PATH.'dashboard.php?section_id=13');
    exit();	
  }

 
?>  