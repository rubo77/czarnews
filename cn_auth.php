<?php
include("cn_config.php");
session_start();

### If there are no users in the database, include user admin to create first user ###
$q['info'] = mysql_query("SELECT * FROM $t_user ORDER BY user ASC", $link) or E("Couldn't select users:<br>" . mysql_error());
$num = mysql_num_rows($q['info']);
if($num == "0") {
include("cn_users.php");
exit;
}

// $op only exists when ?op= is passed in the request
if (isset($cncook) && ($op ?? "") != "logout") {
list ($usern, $passw) = explode(',', $cncook);
} elseif (isset($cncook) && ($op ?? "") == "logout") {
setcookie('cncook','',time()-28800,'/');
unset($_SESSION['usern'], $_SESSION['passw']);
}

if (empty($usern) && empty($passw) && isset($_SESSION['usern'], $_SESSION['passw'])) {
$usern = $_SESSION['usern'];
$passw = $_SESSION['passw'];
}

if (!empty($usern) && !empty($passw)) {
$usern_esc = mysql_real_escape_string($usern, $link);
$passw_esc = mysql_real_escape_string($passw, $link);
$q['useri'] = mysql_query("SELECT * FROM $t_user WHERE user='$usern_esc' && pass='$passw_esc'", $link);
if (mysql_num_rows($q['useri']) != 0) {
$useri = mysql_fetch_array($q['useri']);
if($useri['cookie'] == "0") { $logtime = 3600; } else { $logtime = $useri['cookie']*24*60*60; }
setcookie('cncook',"{$useri['user']},{$useri['pass']}",time()+$logtime,'/');
$time = strtotime("now");
mysql_query("UPDATE $t_user SET last_login='$time' WHERE id={$useri['id']}", $link);

$usern = $useri['user'];  	// Set $usern to Database info
$passw = $useri['pass'];	// Set $passw to Database info

$_SESSION['usern'] = $usern;	// Register variable usern to session
$_SESSION['passw'] = $passw;	// Register variable passw to session

} else {
// Wrong user/pass message
$msg = "Incorrect Username/Password";
$_SESSION['msg'] = $msg;
include("login.php");
exit;
}
} else {
// Include login screen - no user/pass specified
include("login.php");
exit;
}
?>
