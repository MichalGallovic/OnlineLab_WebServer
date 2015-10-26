<?php
/*
  $Id: sessions.php,v 1.9 2003/06/23 01:20:05 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  function tep_session_start() {
    //session_name('RSHOPadmin');
	//ini_set("session.cookie_domain", HTTP_COOKIE_DOMAIN);
	//ini_set("session.cookie_lifetime", 0);
	//session_set_cookie_params(0, '/', HTTP_COOKIE_DOMAIN);
	return session_start();
  }
  /*
  function tep_session_register($variable) {
    return session_register($variable);
  }
  */
  function tep_session_is_registered($variable) {
    return isset($_SESSION[$variable]);
  }

  function tep_session_unregister($variable) {
    unset($_SESSION[$variable]);
	return true;
  }

  function tep_session_id($sessid = '') {
    /*if ($sessid != '') {
      return session_id($sessid);
    } else {
      return session_id();
    }*/
	return session_id();
  }

  function tep_session_name($name = '') {
    /*if ($name != '') {
      return session_name($name);
    } else {
      return session_name();
    }*/
	return session_name();
  }

  function tep_session_close() {
    if (function_exists('session_close')) {
      return session_close();
    }
  }

  function tep_session_destroy() {
    return session_destroy();
  }

  function tep_session_save_path($path = '') {
    if ($path != '') {
      return session_save_path($path);
    } else {
      return session_save_path();
    }
  }
?>