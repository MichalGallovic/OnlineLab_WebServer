<?php 

//instalacny script
require_once('includes/config.php');
require_once( 'includes/classes/mysql.php');
$mysql = new mysql(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, true, DB_ENCODING);

$mysql->query("SELECT COUNT(*) as instal FROM information_schema.tables WHERE table_schema = '".DB_DATABASE."'  AND table_name = 'olm_install'");
$instal = $mysql->result(0,'instal');
if($instal > 0){
	header('Location: index.php ');
exit;
}

$mysql->query("
	CREATE TABLE IF NOT EXISTS `olm_install` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `install` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci
");

$mysql->query("CREATE TABLE IF NOT EXISTS `olm_account_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$mysql->query("INSERT INTO `olm_account_types` (`id`, `name`) VALUES
(1, 'local'),
(2, 'gmail'),
(3, 'is_stuba')");

$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_admin_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_type` int(11) NOT NULL DEFAULT '1',
  `login` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `hash` varchar(50) NOT NULL,
  `name` varchar(40) NOT NULL,
  `surname` varchar(40) NOT NULL,
  `language_code` varchar(5) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1250 AUTO_INCREMENT=1
");


$mysql->query("
INSERT INTO `olm_admin_users` (`id`, `account_type`, `login`, `email`, `pass`, `name`, `surname`, `language_code`, `active`, `deleted`) VALUES
(1, 1, 'admin', 'admin@stuba.sk', 'e1cac2d71f95d992ae7c1f56d390d41e', 'admin', '', 'sk', 1, 0)
");


$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_admin_user_chart_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `width` int(10) NOT NULL DEFAULT '800',
  `height` int(10) NOT NULL DEFAULT '320',
  `xTitleShow` tinyint(4) NOT NULL DEFAULT '1',
  `yTitleShow` tinyint(4) NOT NULL DEFAULT '1',
  `mainTitleShow` int(11) NOT NULL DEFAULT '1',
  `mainTitleText` varchar(150) NOT NULL ,
  `subTitleShow` int(11) NOT NULL DEFAULT '1',
  `subTitleText` varchar(150) NOT NULL ,
  `showLegend` int(11) NOT NULL DEFAULT '1',
  `showMenu` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
");


$mysql->query("
INSERT INTO `olm_admin_user_chart_settings` (`id`, `user_id`, `width`, `height`, `xTitleShow`, `yTitleShow`, `mainTitleShow`, `mainTitleText`, `subTitleShow`, `subTitleText`, `showLegend`, `showMenu`) VALUES
(1, 1, 800, 320, 1, 1, 1, 'Hlavný nadpis', 1, 'Podnadpis', 1, 1)
");



$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_admin_user_dashboard_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `admin_modules` varchar(800) NOT NULL COMMENT 'osobne nastavanie modulov',
  `show_dashboard_settings_help` tinyint(4) NOT NULL DEFAULT '1',
  `profile_1` varchar(800) NOT NULL,
  `profile_2` varchar(800) NOT NULL,
  `profile_3` varchar(800) NOT NULL,
  `selected_profile` tinyint(4) NOT NULL DEFAULT '1',
  `left_menu` int(11) NOT NULL DEFAULT '1' COMMENT '1-klasicke lave menu,2-zuzene menu',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8
");


$mysql->query('
INSERT INTO `olm_admin_user_dashboard_settings` (`id`, `user_id`, `admin_modules`, `show_dashboard_settings_help`, `profile_1`, `profile_2`, `profile_3`, `selected_profile`) VALUES
(1, 1, \'{"livechart":{"id":"1","modul":"livechart","left_":"0","top":"0","width":"800","height":"320","zindex":"1","modul_title_constant":"MODUL_LIVECHART_TITLE","widget":"0","show":"1"},"reservation":{"id":"3","modul":"reservation","left_":"0","top":"325","width":"400","height":"146","zindex":"1","modul_title_constant":"MODUL_RESERVATION_TITLE","widget":"1","show":"1"},"experiment":{"id":"5","modul":"experiment","left_":"406","top":"325","width":"394","height":"125","zindex":"1","modul_title_constant":"MODUL_EXPERIMENT_TITLE","widget":"1","show":"1"},"experimentinterface":{"id":"6","modul":"experimentinterface","left_":"805","top":"0","width":"400","height":"0","zindex":"1","modul_title_constant":"EXPERIMENT_INTERFACE_TITLE","widget":"1","show":"0"}}\', 1, \'{"livechart":{"id":"1","modul":"livechart","left_":"0","top":"0","width":"800","height":"320","zindex":"1","modul_title_constant":"MODUL_LIVECHART_TITLE","widget":"0","show":"1"},"reservation":{"id":"3","modul":"reservation","left_":"0","top":"325","width":"400","height":"146","zindex":"1","modul_title_constant":"MODUL_RESERVATION_TITLE","widget":"1","show":"1"},"experiment":{"id":"5","modul":"experiment","left_":"406","top":"325","width":"394","height":"125","zindex":"1","modul_title_constant":"MODUL_EXPERIMENT_TITLE","widget":"1","show":"1"},"experimentinterface":{"id":"6","modul":"experimentinterface","left_":"805","top":"0","width":"400","height":"0","zindex":"1","modul_title_constant":"EXPERIMENT_INTERFACE_TITLE","widget":"1","show":0}}\', \'{"livechart":{"id":"1","modul":"livechart","left_":"0","top":"0","width":"800","height":"320","zindex":"1","modul_title_constant":"MODUL_LIVECHART_TITLE","widget":"0","show":"1"},"reservation":{"id":"3","modul":"reservation","left_":"0","top":"325","width":"400","height":"146","zindex":"1","modul_title_constant":"MODUL_RESERVATION_TITLE","widget":"1","show":"1"},"experiment":{"id":"5","modul":"experiment","left_":"406","top":"325","width":"394","height":"125","zindex":"1","modul_title_constant":"MODUL_EXPERIMENT_TITLE","widget":"1","show":"1"},"experimentinterface":{"id":"6","modul":"experimentinterface","left_":"805","top":"0","width":"400","height":"0","zindex":"1","modul_title_constant":"EXPERIMENT_INTERFACE_TITLE","widget":"1","show":"0"}}\', \'{"livechart":{"id":"1","modul":"livechart","left_":"0","top":"0","width":"800","height":"320","zindex":"1","modul_title_constant":"MODUL_LIVECHART_TITLE","widget":"0","show":"1"},"reservation":{"id":"3","modul":"reservation","left_":"0","top":"325","width":"400","height":"146","zindex":"1","modul_title_constant":"MODUL_RESERVATION_TITLE","widget":"1","show":"1"},"experiment":{"id":"5","modul":"experiment","left_":"406","top":"325","width":"394","height":"125","zindex":"1","modul_title_constant":"MODUL_EXPERIMENT_TITLE","widget":"1","show":"1"},"experimentinterface":{"id":"6","modul":"experimentinterface","left_":"805","top":"0","width":"400","height":"0","zindex":"1","modul_title_constant":"EXPERIMENT_INTERFACE_TITLE","widget":"1","show":"0"}}\', 3);
');

$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_controllers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `permissions` int(11) NOT NULL DEFAULT '0',
  `body` text COLLATE utf8_slovak_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci
");

$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_controllers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `permissions` int(11) NOT NULL DEFAULT '0',
  `body` text COLLATE utf8_slovak_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci
");

$mysql->query("
INSERT INTO `olm_controllers` (`id`, `user_id`, `equipment_id`, `name`, `permissions`, `body`, `date`) VALUES
(8, 1, 3, 'Termo - prioritny', 0, 'y1=u1', NOW());
");

$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_dashboard_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modul` varchar(150) NOT NULL,
  `left_` int(11) NOT NULL,
  `top` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `zindex` int(5) NOT NULL DEFAULT '1',
  `modul_title_constant` varchar(150) NOT NULL,
  `widget` tinyint(11) NOT NULL DEFAULT '1',
  `show` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `modul` (`modul`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8
");

$mysql->query("
INSERT INTO `olm_dashboard_modules` (`id`, `modul`, `left_`, `top`, `width`, `height`, `zindex`, `modul_title_constant`, `widget`, `show`) VALUES
(1, 'livechart', 0, 0, 800, 320, 1, 'MODUL_LIVECHART_TITLE', 0, 1),
(3, 'reservation', 0, 325, 400, 146, 1, 'MODUL_RESERVATION_TITLE', 1, 1),
(5, 'experiment', 406, 325, 394, 125, 1, 'MODUL_EXPERIMENT_TITLE', 1, 1),
(6, 'experimentinterface', 805, 0, 400, 0, 1, 'EXPERIMENT_INTERFACE_TITLE', 1, 0);
");

$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_equipments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `equipment_name` varchar(150) NOT NULL,
  `ip` varchar(150) NOT NULL,
  `color` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8
");

$mysql->query("
INSERT INTO `olm_equipments` (`id`, `equipment_name`, `ip`, `color`) VALUES
(1, 'hydro', '127.0.0.1', '#ADD8E6'),
(3, 'termo', '147.175.115.252', '#fd240e');
");

$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_languages` (
  `languages_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `image` varchar(50) NOT NULL,
  PRIMARY KEY (`languages_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
");

$mysql->query("
INSERT INTO `olm_languages` (`languages_id`, `name`, `code`, `image`) VALUES
(1, 'anglický jazyk', 'en', 'en_flag.png'),
(4, 'slovenský jazyk', 'sk', 'sk_flag.png');
");


$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `output` text COLLATE utf8_slovak_ci NOT NULL,
  `console` text COLLATE utf8_slovak_ci NOT NULL,
  `regulator` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `regulator_settings` varchar(150) COLLATE utf8_slovak_ci NOT NULL,
  `ip` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
  `report_date` datetime NOT NULL,
  `report_simulation_time` decimal(16,2) NOT NULL DEFAULT '0.00',
  `experiment_settings` varchar(250) COLLATE utf8_slovak_ci NOT NULL,
  `exp_running` tinyint(4) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8_slovak_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;
");

$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_reports_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `console_box` int(11) NOT NULL DEFAULT '1',
  `input_experiment_settings_box` int(11) NOT NULL DEFAULT '1',
  `personal_notes_box` int(11) NOT NULL DEFAULT '1',
  `chart_height` int(100) NOT NULL DEFAULT '320',
  `chart_width` int(10) NOT NULL DEFAULT '800',
  `chart_x_title` tinyint(4) NOT NULL DEFAULT '1',
  `chart_y_title` tinyint(4) NOT NULL DEFAULT '1',
  `chat_main_title` tinyint(4) NOT NULL DEFAULT '1',
  `chart_main_title_text` varchar(150) COLLATE utf8_slovak_ci NOT NULL DEFAULT 'Hlavný nadpis',
  `chart_subtitle` tinyint(4) NOT NULL DEFAULT '1',
  `chart_subtitle_text` varchar(150) COLLATE utf8_slovak_ci NOT NULL DEFAULT 'Podnadpis',
  `chart_legend` tinyint(4) NOT NULL DEFAULT '1',
  `chart_menu` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;
");

/*$mysql->query("
INSERT INTO `olm_reports_settings` (`id`, `user_id`, `console_box`, `input_experiment_settings_box`, `personal_notes_box`, `chart_height`, `chart_width`, `chart_x_title`, `chart_y_title`, `chat_main_title`, `chart_main_title_text`, `chart_subtitle`, `chart_subtitle_text`, `chart_legend`, `chart_menu`) VALUES
(1, 1, 1, 1, 1, 320, 800, 1, 1, 1, 'Hlavný nadpis 	', 1, 'Podnadpis', 1, 1);
");*/

$mysql->query("
CREATE TABLE IF NOT EXISTS `olm_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modul` varchar(150) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL,
  `title_class` varchar(50) NOT NULL,
  `icon` varchar(150) NOT NULL,
  `active` int(3) NOT NULL,
  `sect_order` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

");


$mysql->query("
INSERT INTO `olm_sections` (`id`, `modul`, `parent`, `title`, `title_class`, `icon`, `active`, `sect_order`) VALUES
(1, '', 0, 'PRESONAL_SETTINGS', '', '', 0, 2),
(3, 'reservation', 0, 'ROOM_RESERVATIONS', 'reservation_ico', 'calendar-ico2.png', 1, 3),
(5, 'home', 0, 'DASHBOARD', 'dashboard_ico', 'home-ico.png', 1, 1),
(7, '', 0, 'EQUIPMENT', '', '', 0, 7),
(9, 'experiment', 0, 'EXPERIMETNS', '', '', 0, 5),
(11, '', 5, 'DASHBOARD_LAYOUT', '', '', 1, 2),
(13, '', 5, 'DASHBOARD_HOME', '', 'home-ico.png', 1, 1),
(14, 'report', 0, 'REPORTS', 'report_ico', 'report-icon.png', 1, 4),
(15, 'controller', 0, 'CONTROLLERS', 'controller_ico', 'controllers.png', 1, 6),
(16, 'profile', 0, 'USER_PROFILE', 'profile_ico', 'profile.png', 1, 7);

");

$mysql->query("
CREATE TABLE IF NOT EXISTS `own_ctrl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `plant_id` int(11) NOT NULL,
  `name` varchar(20) COLLATE utf8_slovak_ci NOT NULL,
  `public` int(11) NOT NULL,
  `body` varchar(750) COLLATE utf8_slovak_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci ;
");

$mysql->query("

CREATE TABLE IF NOT EXISTS `olm_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `equipment` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `body` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL,
  `color` varchar(150) NOT NULL,
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_id` (`reservation_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 

");
header('Location: index.php ');
exit;

?>