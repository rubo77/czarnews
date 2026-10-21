<?php
$dbconnect=false;
$db="czarnews";

// register_globals:
// 'tpath' is the include root for CzarNews - accepting it from the
// request would allow remote file inclusion (CVE-2005-0859/2006-3685).
foreach($_REQUEST as $r_k=>$r_v){
	if($r_k === 'tpath') continue;
	$$r_k=$r_v;
}
#if(!function_exists('session_register')){
        function st_session_register($var){
                global $$var;
		#echo "setting $var:".$$var."<br>";
                if(isset($$var)) $_SESSION[$var]=$$var;
                $_SESSION['st_reg'][]=$var;
        }
#}
function st_session_start(){
	if(!empty($_SESSION['st_reg'])){
		foreach($_SESSION['st_reg'] as $var){
			global $$var;
			#echo $var."=".$$var."<br>";
			$$var=$_SESSION[$var];
		}
	}
}
function st_session_close(){
	if(!empty($_SESSION['st_reg'])){
		foreach($_SESSION['st_reg'] as $var){
			global $$var;
			#echo $var."=".$$var."<br>";
			$_SESSION[$var]=$$var;
		}
	}
}
// Bugfix PHP 5.4:
if(empty($_SERVER["HTTP_USER_AGENT"])) $_SERVER["HTTP_USER_AGENT"]="";
$HTTP_USER_AGENT=$_SERVER["HTTP_USER_AGENT"];
if(empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) $_SERVER["HTTP_ACCEPT_LANGUAGE"]="en";
$HTTP_ACCEPT_LANGUAGE=$_SERVER["HTTP_ACCEPT_LANGUAGE"];
if(empty($_SERVER["HTTP_HOST"])) $_SERVER["HTTP_HOST"]="spacetrace.org";
$HTTP_HOST=$_SERVER["HTTP_HOST"];
if(empty($_SERVER["SERVER_NAME"])) $_SERVER["SERVER_NAME"]="spacetrace.org";
$SERVER_NAME=$_SERVER["SERVER_NAME"];
if(empty($_SERVER['PHP_SELF'])) $_SERVER['PHP_SELF']=$_SERVER["SCRIPT_NAME"]; // nginx adaption
$PHP_SELF=$_SERVER['PHP_SELF'];

include(__DIR__."/fix_mysql.inc.php");
