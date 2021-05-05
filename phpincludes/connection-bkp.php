<?php
/*	$db_host	= "localhost";
	$db_name	= "westwoodpune";
	$db_user 	= "root";
	$db_password    = "";*/
	
	$db_host	= "localhost";
	$db_name	= "wes_westwood";
	$db_user 	= "wes_westwood";
	$db_password    = "W3@two1#6Us";
	
	$link=mysql_connect($db_host,$db_user,$db_password) or die(mysql_error());
	mysql_select_db($db_name) or die(mysql_error());
?>