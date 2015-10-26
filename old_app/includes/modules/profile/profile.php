<?php 
# Nadefinujeme si minimalne pravo na spustanie niektorych vlastnosti modulu
# 1 - administrator
# 2 - ucitel
# 3 - student/vyvojar
# 4 - student
define("RULE", 2);
$ziskal = FALSE;

require_once('config.php');

//nacitanie jazyka, ktory sa handluje v application_top.php
require_once('languages/'.$_SESSION['language'].'.php');

$profile_tpl = new rFastTemplate(MODUL_PATH);
$profile_tpl->define(array("profile" => "profile.htm"));

$profile_tpl->assign(array("ROOT_PATH"      => ROOT_PATH,
                            "USR_LOGIN"     => USR_LOGIN,
                            "USR_I"         => USR_I,
                            "USR_ROLE"      => USR_ROLE,
                            "USR_NAME"      => USR_NAME,
                            "USR_SURNAME"   => USR_SURNAME,
                            "USR_LANG"      => USR_LANG,
                            "USR_NEW_PASSWORD"  => USR_NEW_PASSWORD,
                            "USR_NEW_PASSWORD_R"    => USR_NEW_PASSWORD_R,
                            "USR_NEW_PASSWORD_I"    => USR_NEW_PASSWORD_I,
                            "USR_SAVE"      => USR_SAVE));

// natiahneme si profil aktualne prihlaseneho uzivatela
$uzivatel = get_user_info($_SESSION['user_id']);

// overime, ci aktualne prihlaseny uzivatel ma prava na zobrazenie a spustanie
// tohto modulu
if ($ziskal = $uzivatel['role'] <= RULE ? TRUE : FALSE)
        
// Ak mame zobrazit profil niekoho ineho, tak zistime, ci nam bolo jeho
// id-cko predane formou URL. Ak nie, tak zobrazime profil aktualne prihlaseneho
if (isset($_GET['uid']))
    $user = get_user_info($_GET['uid']);
else $user = $uzivatel;

$profile_tpl->assign(array("USER_NAME"      => $user['name'],
                            "USER_SURNAME"  => $user['surname'],
                            "USER_LOGIN"    => $user['login'],
                            "USER_EMAIL"    => $user['email'],
                            "USER_ROLE_TITLE_ADMIN"     => USER_ROLE_TITLE_ADMIN,
                            "USER_ROLE_TITLE_TEACHER"   => USER_ROLE_TITLE_TEACHER,
                            "USER_ROLE_TITLE_DEVELOPER" => USER_ROLE_TITLE_DEVELOPER,
                            "USER_ROLE_TITLE_STUDENT"   => USER_ROLE_TITLE_STUDENT));

// prava
$prava = get_roles();
$optiony = "";
foreach($prava as $piece){
    
    switch ($piece['nazov']) {
        case "USER_ROLE_TITLE_ADMIN":
            if ($user['role'] == $piece['id'])
                $optiony .= "<option selected='selected' value=".$piece['id'].">".USER_ROLE_TITLE_ADMIN."</option>";
            else
                $optiony .= "<option value=".$piece['id'].">".USER_ROLE_TITLE_ADMIN."</option>";
            break;
        
        case "USER_ROLE_TITLE_TEACHER":
            if ($user['role'] == $piece['id'])
                $optiony .= "<option selected='selected' value=".$piece['id'].">".USER_ROLE_TITLE_TEACHER."</option>";
            else
                $optiony .= "<option value=".$piece['id'].">".USER_ROLE_TITLE_TEACHER."</option>";
            break;
        
        case "USER_ROLE_TITLE_DEVELOPER":
            if ($user['role'] == $piece['id'])
                $optiony .= "<option selected='selected' value=".$piece['id'].">".USER_ROLE_TITLE_DEVELOPER."</option>";
            else
                $optiony .= "<option value=".$piece['id'].">".USER_ROLE_TITLE_DEVELOPER."</option>";
            break;
        
        case "USER_ROLE_TITLE_STUDENT":
            if ($user['role'] == $piece['id'])
                $optiony .= "<option selected='selected' value=".$piece['id'].">".USER_ROLE_TITLE_STUDENT."</option>";
            else
                $optiony .= "<option value=".$piece['id'].">".USER_ROLE_TITLE_STUDENT."</option>";
            break;
    }
}

$profile_tpl->assign(array('RULE_ACCESS' => $optiony));

//langugaes
$langugaes = get_languages();
foreach($langugaes as $lang){
	$profile_tpl->assign(array('LANG_NAME' => $lang['name'],
                                    'LANG_CODE' => $lang['code'],
                                    'LANG_SELECTED' => $user['language_code'] == $lang['code'] ? 'selected="selected"' : ''
                                    ));
        $profile_tpl->parse("LANG_ROW",".language_row");
}

$profile_tpl->parse("PROFILE", ".profile");
$fetch_module = $profile_tpl->fetch("PROFILE");

function get_profile(){
	global $fetch_module;
	return $fetch_module; 
}

?>