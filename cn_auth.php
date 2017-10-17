<?
include("cn_config.php");

### If there are no users in the database, include user admin to create first user ###
$q[info] = mysql_query("SELECT * FROM $t_user ORDER BY user ASC", $link) or E("Couldn't select users:<br>" . mysql_error());
$num = mysql_num_rows($q[info]);
if($num == "0") {
include("cn_users.php");
exit;
}

if (isset($cncook) && $op != "logout") {
list ($usern, $passw) = split ('[,]', $cncook);
} elseif (isset($cncook) && $op == "logout") {
setcookie('cncook','',time()-28800,'/');
}

if (!empty($usern) && !empty($passw) || session_is_registered($usern) && session_is_registered($passw)) {
$q[useri] = mysql_query("SELECT * FROM $t_user WHERE user='$usern' && pass='$passw'", $link);
if (mysql_num_rows($q[useri]) != 0) {
$useri = mysql_fetch_array($q[useri]);
if($useri[cookie] == "0") { $logtime = 3600; } else { $logtime = $useri[cookie]*24*60*60; }
setcookie('cncook',"$useri[user],$useri[pass]",time()+$logtime,'/');
$time = strtotime("now");
mysql_query("UPDATE $t_user SET last_login='$time' WHERE id=$useri[id]", $link);
session_start();

$usern = $useri[user];  	// Set $usern to Database info
$passw = $useri[pass];	// Set $passw to Database info

session_register('usern');			// Register variable usern to session
session_register('passw');		// Register variable passw to session

} else {
// Wrong user/pass message
$msg = "Incorrect Username/Password";
session_register('msg');
include("login.php");
exit;
}
} else {
// Include login screen - no user/pass specified
include("login.php");
exit;
}
?>