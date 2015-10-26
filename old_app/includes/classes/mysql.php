<?php
// trieda na pristup k mysql funkciam
// lastedit 2010-02-20


class mysql {
  // parametre pripojenia k databaze
  var $host, $user, $password, $database;
  // premenne drziace connection a result
  var $connection, $result;
  // error reporting
  var $error_reporting;
  // encoding
  var $encoding;
  
  
  // KONSTRUKTOR
  //////////////
  
  function mysql($host, $user, $password, $database, $err = false, $encoding = DB_ENCODING) {
    $this->host = $host;
	$this->user = $user;
	$this->password = $password;
	$this->database = $database;
	
	$this->error_reporting = $err;
	$this->encoding = $encoding;
	
	if(!$this->open())
	  $this->error();
  }
  
  
  // FUNKCIE
  //////////
  
  // pripojenie k databaze
  function open() {
    $this->connection = @mysql_connect($this->host, $this->user, $this->password);
	if(!$this->connection)
	  return false;
	if(!@mysql_select_db($this->database, $this->connection))
	  return false;
	mysql_query("SET NAMES '".$this->encoding."' COLLATE '".$this->encoding."_general_ci'");
	return true;
  }
  
  // zvolenie databazy
  function select_db($name, $encoding = "") {
    if(!@mysql_select_db($name, $this->connection))
	  return false;
	
	mysql_query("SET NAMES '".($encoding ? $encoding : $this->encoding)."' COLLATE '".($encoding ? $encoding : $this->encoding)."_general_ci'");
	return true;
  }
    
  // odpojenie od databazy
  function close() {
    if(!@mysql_close($this->connection)) {
	  $this->error();
	  return false;
	}
	return true;
  }
  
  // klasicke mysql_query
  function query($sql) {
    $this->result = @mysql_query($sql, $this->connection);
	if(!$this->result) {
	  $this->error($sql);
	  return false;
	}
	return $this->result;
  }
  
  // unbuffered mysql_query
  function unbuffered_query($sql) {
    $this->result = @mysql_unbuffered_query($sql, $this->connection);
	if(!$this->result) {
	  $this->error();
	  return false;
	}
	return $this->result;
  }
  
  // klasicky mysql_fetch_array
  function fetch_array($result = "") {
    return @mysql_fetch_assoc($result ? $result : $this->result);
  }
  
  // klasicky mysql_fetch_array
  function fetch_row($result = "") {
    return @mysql_fetch_row($result ? $result : $this->result);
  }
  
  // klasicky mysql_num_rows
  function num_rows($result = "") {
    return @mysql_num_rows($result ? $result : $this->result);
  }
  
  // klasicky mysql_result
  function result($row, $field, $result = "") {
    return @mysql_result($result ? $result : $this->result, $row, $field);
  }
  
  // klasicky free_result
  function free_result($result = "") {
    return @mysql_free_result($result ? $result : $this->result);
  }
  
  // mysql_real_escape_string
  function escape($str) {
    return mysql_real_escape_string($str, $this->connection);
  }
  
  // mysql_insert_id
  function insert_id() {
    return mysql_insert_id($this->connection);
  }
  
  // mysql_affected_rows
  function affected_rows() {
    return mysql_affected_rows($this->connection);
  }
  
  // ak je zapnuty error_reporting, vyhodime mysql_error
  function error($sql = false) {
    if($this->error_reporting)
	  echo mysql_error().($sql ? "<br />".$sql : "")."<br />";
  }
  
  // pre hesla
  function create_pass($pass, $salt) {
  	return md5(md5($pass).$salt);
  }
  
  // klasicky mysql_data_seek
  function data_seek ($result = "", $row_number = 0) {
    return @mysql_data_seek ($result ? $result : $this->result, $row_number);
  }
  
}

?>