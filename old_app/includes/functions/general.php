<?php 

function get_menu_style(){
	global $mysql;
	
	$mysql->query("SELECT left_menu FROM ".TABLE_USER_SETTINGS."  WHERE  user_id = '".$_SESSION['user_id']."'  ");
	return $mysql->result(0,'left_menu');
}

function get_regulator($regId){
	global $mysql;
	
	$mysql->query("SELECT c.id,e.equipment_name, c.name,u.name as uname,u.surname,c.date,c.permissions ,c.body FROM  ".TABLE_COTROLLERS." c
				   INNER JOIN ".TABLE_ADMIN_USERS." u ON (c.user_id = u.id)
				   INNER JOIN ".TABLE_EQUIPMENT." e ON (c.equipment_id = e.id)  
				   WHERE c.id = ".$regId." ");
	
	return $mysql->fetch_array();
}

function add_report($user_id,$equipment_id, $output , $reg, $reg_settings, $ip, $experiment_settings){
	global $mysql;
	
	$reg_control = array("PID" => "PID", "NO" => "Otvorená slučka", "OWN" => "Vlastný regulátor");
	
	
	$result = $mysql->query("INSERT INTO ".TABLE_REPORTS." (user_id,equipment_id, output,regulator ,regulator_settings, ip,experiment_settings) 
						VALUES(".$user_id.",".$equipment_id.",'".$output."','".$reg_control[$mysql->escape($reg)]."' ,'".$mysql->escape($reg_settings)."','".$ip."','".$experiment_settings."') ");
	
	return $mysql->insert_id();	
}

function isReportOpenedByUser($user_id){
	global $mysql;
	
	$mysql->query("SELECT COUNT(*) as countRunningExp FROM ".TABLE_REPORTS." WHERE user_id = ".$user_id." AND exp_running = 1 ");
	$countRunningExp = $mysql->result(0,'countRunningExp');
	if($countRunningExp > 0)
		return true;
	else
		return false;	
}

function end_report($report_id){
	global $mysql;
	
		$mysql->query("UPDATE ".TABLE_REPORTS." SET exp_running = 0  WHERE id=".$report_id." ");
		
}

function start_report($report_id){
	global $mysql;
	$mysql->query("UPDATE ".TABLE_REPORTS." SET  report_date = NOW() WHERE id=".$report_id." ");
}


function set_report_data($report_id,$data,$consoleOutput = '',$report_simulation_time){
	global $mysql;
	
	$mysql->query("UPDATE ".TABLE_REPORTS." SET output = '".$data."',console= '".$mysql->escape($consoleOutput)."',report_simulation_time = '".$report_simulation_time."' WHERE id=".$report_id." ");
	
}

function validate_report($report_id){
	global $mysql;
	$mysql->query("SELECT * FROM ".TABLE_REPORTS." WHERE id = ".$report_id." ");
	$report = $mysql->fetch_array();
	
	var_dump($report);
	exit;
	
	if($report['output'] === '' or $report['console'] === '')
		return false;
	else
		return true;	
}

function get_current_reservation($plantId,$userId){
	global $mysql;
	
	$mysql->query("SELECT * FROM ".TABLE_RESERVATION." WHERE user_id = '". $userId ."' AND equipment = '".$plantId."' AND start < now() AND end > now() ORDER BY start ");
	return $mysql->fetch_array();	
}

function get_plant($plantId){
	global $mysql;
	
	$mysql->query("SELECT * FROM ".TABLE_EQUIPMENT." WHERE id = ". $plantId ." ");
	return $mysql->fetch_array();	
}

function get_plants(){
	global $mysql;
	$plants = array();
	
	$result = $mysql->query("SELECT * FROM ".TABLE_EQUIPMENT." ORDER BY id ");
	while($row = $mysql->fetch_array()){
	
		$plants[] = $row;
	}
	
	return $plants;
}

function get_modules_default_settings(){
	global $mysql;
	$modules = array();
	
	$mysql->query("SELECT * FROM ".TABLE_MODULES." WHERE 1 ");
	while($row = $mysql->fetch_array()){
		$modules[$row['modul']] = $row; 
	}
	
	return $modules;
}

function get_user_info($user_id){
	global $mysql;
	
	$mysql->query("SELECT * FROM ".TABLE_ADMIN_USERS." WHERE id = '". $mysql->escape($user_id) ."' ");
	return $mysql->fetch_array();
}

function create_pass_general($pass, $salt) {
  	return md5(md5($pass).$salt);
}

function get_user_by_email($email){
	global $mysql;
	
	$mysql->query("SELECT * FROM ".TABLE_ADMIN_USERS." WHERE email LIKE '". $mysql->escape($email) ."' ");
	return $mysql->fetch_array();
}

function getCategoryArray($include_only_arr = '', $parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $mysql;
	
	if(!is_array($category_tree_array)){
	  $category_tree_array = array();
	
	} 
  
    if ($include_itself) {
      $mysql->query("SELECT cd.categories_name FROM " . TABLE_CATEGORIES_DESCRIPTION . " cd 
	  				 WHERE cd.language_id = '1' 
					 	AND cd.id = '" . (int)$parent_id . "'");
      $category = tep_db_fetch_array($category_query);
      $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
	}

    $categories_query = $mysql->query("SELECT c.id, cd.categories_name, c.parent_id FROM " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
										WHERE c.id = cd.id and c.active = 1 
											AND cd.language_id = '1' 
											AND c.parent_id = '" . (int)$parent_id . "' 
											ORDER BY c.sort_order, cd.categories_name");
    
	while ($categories = $mysql->fetch_array($categories_query)) {
	  $new_category_array[$categories['id']][] = array('text' => $categories['categories_name']);
	  if(($exclude != $categories['id']) and (!$include_only_arr or @in_array($categories['id'], $include_only_arr))) {
	       $category_tree_array = getCategoryArray($include_only_arr, $categories['id'], $spacing, $exclude, $category_tree_array);
	  }
    }

    return $category_tree_array;
}


function getMainCategories($parent_id = '1'){
	global $mysql;
	$categories = array();
	
	$rsl = $mysql->query("SELECT c.id,cd.categories_name,cd.categories_name_search 
						  FROM ".TABLE_CATEGORIES." c INNER JOIN ".TABLE_CATEGORIES_DESCRIPTION." cd USING (id)
				 		  WHERE c.parent_id = '". (int)$parent_id ."' ");
	while($row = $mysql->fetch_array($rsl)){
		$categories[] = array('id' => $row['id'],'categories_name' => $row['categories_name'],'filename' => $row['categories_name_search'] );
	}				
	return $categories;
}

function get_languages( $language_id = ''){
	global $mysql;
	$languages = array();
	
	if($language_id !='')
		$where = 'languages_id ='.$language_id;
	else
		$where = '1';	
		
	$mysql->query('SELECT * FROM '.TABLE_LANGUAGES.' WHERE '.$where.' ');
	while($row = $mysql->fetch_array()){
		$languages[$row['languages_id']] = $row;
	}
	
	return $languages; 
}

function get_languages_code( $language_id = ''){
	global $mysql;
	$languages = array();
	
	if($language_id !='')
		$where = 'languages_id ='.$language_id;
	else
		$where = '1';	
		
	$mysql->query('SELECT * FROM '.TABLE_LANGUAGES.' WHERE '.$where.' ');
	while($row = $mysql->fetch_array()){
		$languages[$row['code']] = $row;
	}
	
	return $languages; 
}



function verify_user_role($user_id, $request_role) {
    global $mysql;
}



/**
 * Navracia zoznam roli. Podla jazykovej mutacie
 * 
 * @param type $jazyk
 * @return array
 */
function get_roles() {
    
    global $mysql;
    
    // vytiahneme si udaje z db
    $mysql->query("SELECT * FROM ".TABLE_ADMIN_USER_ROLES);
    
    while ($row = $mysql->fetch_array())
        $data[] = $row;
    
    return $data;
}


function get_section($sectionId = ''){
	global $mysql;
	
	if($sectionId == '')
		$sectionId = $_GET['section_id'];
		
	$mysql->query("SELECT * FROM ".TABLE_SECTIONS." WHERE id = '".$sectionId."' ");
	return $mysql->fetch_array();
}

function get_main_section($sectionId = ''){
	global $mysql;
	$main_section = array();
	
	if($sectionId == '')
		$sectionId = $_GET['section_id'];
	$mysql->query("SELECT * FROM ".TABLE_SECTIONS." WHERE id = '".$sectionId."' ");
	$section = $mysql->fetch_array();
	
	if($section['parent'] > 0){
		$mysql->query("SELECT * FROM ".TABLE_SECTIONS." WHERE id = '".$section['parent']."' ");
		$main_section =  $mysql->fetch_array();
	}
	else{
		$main_section = $section;
	}
	
	return $main_section;	
}

function vytvor_filename($filename) {

$nahrady = array('á' => 'a', 'ä' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e', 'ě' => 'e', 'í' => 'i', 'ľ' => 'l', 'ĺ' => 'l', 'ň' => 'n', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'ř' => 'r', 'ŕ' => 'r', 'š' => 's', 'ť' => 't', 'ú' => 'u', 'ý' => 'y', 'ž' => 'z', 'Á' => 'a', 'Č' => 'c', 'Ď' => 'd', 'É' => 'e', 'Í' => 'i', 'Ľ' => 'l', 'Ň' => 'n', 'Ó' => 'o', 'Š' => 's', 'Ť' => 't', 'Ú' => 'u', 'Ý' => 'y', 'Ž' => 'z');
$filename = strtolower(html_entity_decode($filename));

foreach($nahrady as $pismeno => $nahrada)
  $filename = str_replace($pismeno, $nahrada, $filename);

$filename = preg_replace("/[^a-z0-9]/", "-", $filename);

// nahradime viacero pomlciek jedinou
$filename = preg_replace("/--+/", "-", $filename);

// odstranime zaciatocne a koncove pomlcky
$filename = preg_replace(array("/^-+/", "/-+$/"), "", $filename);

return $filename;
}

 function generatePassword ($length = 8)
  {

    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);
  
    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
      $length = $maxlength;
    }
	
    // set up a counter for how many characters are in the password so far
    $i = 0; 
    
    // add random characters to $password until $length is reached
    while ($i < $length) { 

      // pick a random character from the possible ones
      $char = substr($possible, mt_rand(0, $maxlength-1), 1);
        
      // have we already used this character in $password?
      if (!strstr($password, $char)) { 
        // no, so it's OK to add it onto the end of whatever we've already got...
        $password .= $char;
        // ... and increase the counter by one
        $i++;
      }

    }

    // done!
    return $password;

  }
  
  function is_email_uniq($email){
	global $mysql;
	
	$sql = "SELECT login FROM ".TABLE_ADMIN_USERS." WHERE email LIKE '". mysql_real_escape_string($email) ."' ";
	$mysql->query($sql);
	$find =  $mysql->num_rows();
	if($find > 0)
		return false;
	else 
		return true;	
  }

// -----------------------------------------------------------------------------
// Dorobenie modulove pre spravu Uzivatelov, Realnych zariadeni a virtualizacie
// -----------------------------------------------------------------------------

?>