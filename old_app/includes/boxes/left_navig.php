<?php 

function has_descendatns($sectionId){
	global $mysql;
	$descendant = array();
	$mysql->query("SELECT * FROM ".TABLE_SECTIONS." WHERE parent = '".$sectionId."' AND active = 1 ORDER BY sect_order ");
	
	if($mysql->num_rows() < 1)
		return false;
	
	while($row = $mysql->fetch_array()){
		$descendant[] = $row;
	}
		
	return $descendant;
}

function get_descendatns($sectionId){
	global $mysql;
	$descendant = array();
	$mysql->query("SELECT * FROM ".TABLE_SECTIONS." WHERE parent = '".$sectionId."' AND active = 1 ORDER BY sect_order ");
	
	if($mysql->num_rows() < 1)
		return false;
	
	while($row = $mysql->fetch_array()){
		$descendant[] = $row['id'];
	}
		
	return $descendant;
}



$tmp->define(array("lnavig" => "boxes/left_navig.htm"));
$tmp->assign(array("ROOT_PATH" => ROOT_PATH, "LOGGED_USER" => "", "OPTIONS_TITLE" => OPTIONS, "MENU_COLLAPSE" => MENU_COLLAPSE));

//najvyssia uroven
$sectionsQuery = $mysql->query("SELECT * FROM ".TABLE_SECTIONS." WHERE parent = 0 AND active = 1 ORDER BY sect_order ");
while($section = $mysql->fetch_array($sectionsQuery)){
	
	//sub uroven
	//ak je kategoria co ma subkateogire a zaroven  je vybrata
	//alebo ak je vybrata subakteogria kateogrie
	$isSelectedSubSection = false;
	$isSelectedSectionWithSubsection = false;
	if($descendantsId = get_descendatns($section['id'])){
		if(in_array($g_selectedSectionId,$descendantsId))
			$isSelectedSubSection = true;
	}
	
	
	if(($descendants =  has_descendatns($section['id']) and  $g_selectedSectionId == $section['id']) or $isSelectedSubSection  ){
			$isSelectedSectionWithSubsection = true;
			foreach($descendants as  $descendant){
				$tmp->assign(array(
								   "SUB_SECT_ID" => $descendant['id'],
								   "SUB_SECT_TITLE" => constant($descendant['title']),
								   "SUB_SECT_SELECETED" => ($g_selectedSectionId == $descendant['id']) ? 'selected' : '' ));
				
				$tmp->parse("SUB_SEC_ROW",".sub_section_row");		
			}
			$tmp->parse("SUB_SEC_BLOCK",".sub_section_block");	
		
	}
	
	$tmp->assign(array( "SECT_ID" => $section['id'],
						"SECT_TITLE" => constant($section['title']),
						"SEC_ICON" => $section['icon'],
						"SEC_NODUL" => $section['modul'],
						"SECT_SELECETED" => ($g_selectedSectionId == $section['id'] or ($isSelectedSubSection) ) ? 'selected' : '',
						"SECT_BORDER_BOTTOM" => (($isSelectedSubSection or $isSelectedSectionWithSubsection)  ? 'border-bottom:1px transparent solid;' : '') )
				);
				
	
	$tmp->parse("SEC_ROW",".section_row");
	$tmp->parse("SEC_ROW2",".section_row2");
	$tmp->clear_dynamic("sub_section_block");
	$tmp->clear_dynamic("sub_section_row");
}


if($g_menuStyle == 1){
	$tmp->assign(array("MENU_CLASSIC_SHOW" => '',"MENU_COLLPASE_SHOW" => 'nodisplay'));
	
}else{
	$tmp->assign(array("MENU_CLASSIC_SHOW" => 'nodisplay',"MENU_COLLPASE_SHOW" => ''));
}


$tmp->parse("LEFT_NAVIG",".lnavig"); 
$fetch_box = $tmp->fetch("LEFT_NAVIG");

?>