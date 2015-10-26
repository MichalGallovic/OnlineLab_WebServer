<?php 

function stuLdapAuth($login, $pass){
	$username="uid=".$login.",ou=People,DC=stuba,DC=sk";		
	$ldaphost="ldap.stuba.sk";
	$ldapconn = ldap_connect($ldaphost);
	
	//nepodarilosa pripojit k serveru
	if(!$ldapconn)
		return false;
	$res = @ldap_bind( $ldapconn, $username, $pass);
	//authenitifikacia zlyhala
	if(!$res)
		return false;
	else
		return true;	
}

?>
