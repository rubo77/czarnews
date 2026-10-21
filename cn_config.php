<?php
require_once(__DIR__."/../includes/php7_fix.inc.php");
##########################################
### 
### CzarNews v1.12
### Made by: Czaries  [czaries@czaries.net]
### http://www.czaries.net/scripts/
### for more scripts and updates.
###
### Current Version, do not alter or script will not install right
$cnver = "1.12";
##########################################

// $tpath ist in news.php im czarnews ordner definiert
// empty when CzarNews runs directly from its own directory
if(!isset($tpath)) { $tpath = ""; }
if(file_exists($tpath . "cn_dbdefs.php")) {
// Include MySQL Definitions
require($tpath . "cn_dbdefs.php");
} else {
include ($tpath . "install.php");
exit;
}

// Directory the images are in
$imgdir = "images/";

// Alternating table row background colors used by the admin templates;
// no values were ever configured for this installation, keep them empty
// so the table renders with the default background.
$MenuBg1 = "";
$MenuBg2 = "";

// Make the default database connection
$link = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Unable to connect to MySQL server.<br>" . mysql_error()); 
mysql_select_db($dbname, $link) or die ("Could Not Connect to Selected Database.<br>" . mysql_error());

// Table names
$t_news = "cn_news";
$t_user = "cn_users";
$t_cats = "cn_cats";
$t_conf = "cn_config";
$t_coms = "cn_comments";
$t_words = "cn_words";


##############################################
### Useful Functions and other configurations.           ###
### There is no need to edit anything below this line. ###
##############################################

// Current server time, in milliseconds - do not alter
$now = strtotime('now');

// Retrieve Script Configuration Settings
$q['sets'] = mysql_query("SELECT * FROM $t_conf", $link) or E("Couldn't retrieve configuration settings:<br>" . mysql_error());
$set = mysql_fetch_array($q['sets'], MYSQL_ASSOC);

if($cnver > $set['version']) {
include ($tpath . "install.php");
exit;
}

// User functions
if (isset($cncook) && ($op ?? "") != "logout") {
 list ($usern, $passw) = explode(',', $cncook);
 $q['useri'] = mysql_query("SELECT * FROM $t_user WHERE user='$usern' && pass='$passw'", $link);
 if (mysql_num_rows($q['useri']) != 0) {
  $useri = mysql_fetch_array($q['useri'], MYSQL_ASSOC);
 } else {
  setcookie('cncook','',time()-28800,'/');
 }
} elseif (isset($cncook) && ($op ?? "") == "logout") {
setcookie('cncook','',time()-28800,'/');
}
// Asseble user cetegories into an array
$ucats = explode(", ", isset($useri['categories'])?$useri['categories']:""); 

### Timezones Array ###
$tzones = Array(
			"-12" => "GMT - 12 Hours",
			"-11" => "GMT - 11 Hours",
			"-10" => "GMT - 10 Hours",
			"-9" => "GMT - 9 Hours",
			"-8" => "GMT - 8 Hours",
			"-7" => "GMT - 7 Hours",
			"-6" => "GMT - 6 Hours",
			"-5" => "GMT - 5 Hours",
			"-4" => "GMT - 4 Hours",
			"-3.5" => "GMT - 3.5 Hours",
			"-3" => "GMT - 3 Hours",
			"-2" => "GMT - 2 Hours",
			"-1" => "GMT - 1 Hour",
			"0" => "GMT",
			"1" => "GMT + 1 Hour",
			"2" => "GMT + 2 Hours",
			"3" => "GMT + 3 Hours",
			"3.5" => "GMT + 3.5 Hours",
			"4" => "GMT + 4 Hours",
			"4.5" => "GMT + 4.5 Hours",
			"5" => "GMT + 5 Hours",
			"5.5" => "GMT + 5.5 Hours",
			"6" => "GMT + 6 Hours",
			"6.5" => "GMT + 6.5 Hours",
			"7" => "GMT + 7 Hours",
			"8" => "GMT + 8 Hours",
			"9" => "GMT + 9 Hours",
			"9.5" => "GMT + 9.5 Hours",
			"10" => "GMT + 10 Hours",
			"11" => "GMT + 11 Hours",
			"12" => "GMT + 12 Hours",
			"13" => "GMT + 13 Hours"
			);

### Success Message ###
function S($msg) {
?>
<center>
<table border="0" cellpadding="1" cellspacing="1" width="500" bgcolor="#FFFFFF" class="menu"><tr>
<td class="head">S U C C E S S</td>
</tr><tr>
<td class="value">
<blockquote><br>

<?php  print $msg; ?>

<br><br>
</blockquote>
</td>
</tr><tr>
<td class="head" colspan="2"><center>
<input type="button" value=" Okay " onClick="javascript:location.href('<?php  print $_SERVER['PHP_SELF']; ?>')">&nbsp;&nbsp;
<input type="button" value=" Main " onClick="javascript:location.href('index.php')">
</center></td></tr>
</table>
</center>
<br><br>
<?php 
include("cn_foot.php");
exit;
 }

### Error Message ###
function E($msg) {
?>
<center>
<table border="0" cellpadding="1" cellspacing="1" width="500" bgcolor="#FFFFFF" class="menu"><tr>
<td class="head">E R R O R</td>
</tr><tr>
<td class="value">
<blockquote><br>

<?php  print $msg; ?>

<br><br>
</blockquote>
</td>
</tr><tr>
<td class="head" colspan="2"><center>
<input type="button" value=" &lt; Back" onClick="javascript:history.go(-1)">&nbsp;&nbsp;
<input type="button" value=" Main " onClick="javascript:location.href('index.php')">
</center></td></tr>
</table>
</center>
<br><br>
<?php 
include("cn_foot.php");
exit;
 }


### Get info from a user record with their ID ###
function getinfo($id,$field="user") {
GLOBAL $link, $t_user;

$q['uinfo'] = mysql_query("SELECT $field FROM $t_user WHERE id='$id'", $link);
$inf = mysql_fetch_array($q['uinfo'], MYSQL_ASSOC);
return "$inf[$field]";
}

### Drop-down category select box ###
function catBox($boxname="cat",$cat="",$jump="no",$mode="admin") {
global $t_cats, $link, $useri;

$q['box'] = mysql_query("SELECT * FROM $t_cats ORDER BY name ASC", $link) or E("Couldn't select categories:<br>" . mysql_error());
$catnum = mysql_num_rows($q['box']);
if($catnum == "0" && $mode == "admin") {
print "[ No categories ]&nbsp;&nbsp;&nbsp;(You can still post without them)";
} else {
if($jump == "yes") {
?>
<script language="javascript">
<!-- 
function gone() {
location=document.theform.<?=$boxname?>.options[document.theform.<?=$boxname?>.selectedIndex].value
}
//-->
</script><?php  } ?>
<?php  if($jump == "yes") { ?><form name="theform" action="" method="post"><?php  } ?>	
<select name="<?=$boxname?>"<?php  if($jump == "yes") { ?> onChange="gone()" <?php  } ?>>
<?php 
if($useri['categories'] == "all" || $mode != "admin") {
// Print "View All" selection for view all categories [v1.12]
if($mode != "admin") { print "<option value=\"{$_SERVER['PHP_SELF']}?\">View All</option>\n"; }
while($cv = mysql_fetch_array($q['box'], MYSQL_ASSOC)) {
?>
<option value="<?php  if($jump == "yes") { ?><?=$_SERVER['PHP_SELF']?>?<?=$boxname?>=<?php  } ?><?php  print $cv['id']; ?>"<?php if($cat==$cv['id']){print " SELECTED";}?>><?php  print stripslashes($cv['name']); ?></option>
<?php  if($jump == "yes") { ?></form><?php  } ?>
<?php  }
} else {
### Assemble the categories field into an array for selection ###
$cats = explode(", ", $useri['categories']);
foreach($cats as $cv) {
$q['cat'] = mysql_query("SELECT * FROM $t_cats WHERE id = '$cv'", $link) or E("Couldn't select category with id:<br>" . mysql_error());
$cva = mysql_fetch_array($q['cat'], MYSQL_ASSOC);
?>
<option value="<?php  print $cv; ?>"<?php if($cat==$cv){print " SELECTED";}?>><?php  print stripslashes($cva['name']); ?>
<?php 
}
}
?>
</select>
<?php  if($jump == "yes") { ?>
<input type="button" name="go" value="Go" onClick="gone()">
<?php 
}
}
}

// String shortener
function cutstr($string,$endlength="30",$end="...") {
$strlen = strlen($string);
if ($strlen > $endlength) {
$trim = $endlength-$strlen;
$string = substr("$string", 0, $trim); 
$string .= $end;
}
return $string;
}

### Timezone Formatting ###
/*
$current should be the timezone on the
current machine, $target is the timezone
you want to calculate.  Both should be in
hours.  For example, GMT -05:00 should
be -5.  GMT +12:00 should be 12.  You can
use the returned timestamp with the date
function.

ex: echo date('r', zonechange(-5, 10));
would echo the date for a +10:00
timezone on a machine in -05:00.
*/

function zonechange($current, $target, $date="") {
$current = -1 * $current;
if($date == "") {
$zonedate = mktime(date('G'), date('i'), date('s'), date('n'), date('j'), date('Y')) + (($current + $target) * 3600);
} else {
$zonedate = mktime(date('G', $date), date('i', $date), date('s', $date), date('n', $date), date('j', $date), date('Y', $date)) + (($current + $target) * 3600);
}
return $zonedate;
}

### Search & Replace with highlighted same text (case-insensitive) ###
function highlight($x,$var) {
   if ($var != "") {
       $xtemp = "";
       $i=0;
       while($i<strlen($x)){
           if((($i + strlen($var)) <= strlen($x)) && (strcasecmp($var, substr($x, $i, strlen($var))) == 0)) {
                   $xtemp .= "<span style=\"background-color:yellow; color: black\">" . substr($x, $i , strlen($var)) . "</span>"; // replace search with HTML
                   $i += strlen($var);
           }
           else {
               $xtemp .= $x[$i];
               $i++;
           }
       }
       $x = $xtemp;
   }
   return $x;
}
?>
