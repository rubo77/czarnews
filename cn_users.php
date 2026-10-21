<?php 
$pagetitle = "User Admin";
include_once("cn_auth.php");
include_once("cn_head.php");

// optional request parameters
if(!isset($go)) { $go = ""; }
if(!isset($id)) { $id = ""; }
if(!isset($demo)) { $demo = "off"; }
// user row being edited; empty defaults when adding a new one
$ev = array("id"=>"", "user"=>"", "pass"=>"", "email"=>"", "categories"=>"", "admin"=>"", "news"=>"", "users"=>"", "cats"=>"", "config"=>"", "words"=>"");

### If user is not a "User Admin" or an "Ultimate Admin", do not grant access
if(isset($useri) && $useri['users'] != "on" && $useri['admin'] != "on") {
print E("You are not allowed to edit users");
}

$q['count'] = mysql_query("SELECT * FROM $t_user", $link) or E("Couldn't select users:<br>" . mysql_error());
$usernum = mysql_num_rows($q['count']);

if(isset($op)) {
### Check user permissions
if(isset($id)) {
$q['chk'] = mysql_query("SELECT * FROM $t_user WHERE id = '$id'", $link) or E("Couldn't validate user access:<br>" . mysql_error());
$ev = mysql_fetch_array($q['chk'], MYSQL_ASSOC);
if($useri['admin'] == "off" && $ev['admin'] == "on") {
print E("You do not have permission to edit admin accounts");
}
}

if($op == "add") {

if($go == "true") {
if($demo == "on") {
print E("You do not have permission to edit users in the demo");
}
### Check required fields
if(empty($user) || empty($pass) || empty($email)) { print E("A username, password and email are all required to create a user"); }
### If no users exist, make first user admin
if($usernum == "0") { $rank = "1"; $cats = "all"; }
if($allcats == "sel") { if($cats == "") { print E("You did not select any categories for this user"); } } else { $cats = "all"; }
### Assemble the category array into one string for database entry ###
if(is_array($cats)) { $cats = implode(", ", $cats); }
### Add a new user into DB
$time = strtotime("now");
$q['add'] = mysql_query ("INSERT INTO $t_user (id, user, pass, email, created, last_login, cookie, categories, admin, news, users, cats, config, words) VALUES ('', '$user', '$pass', '$email', '$time', '$time', '1', '$cats', '$admin', '$news', '$users', '$categories', '$config', '$words')", $link) or E("Could not insert user:<br>" . mysql_error());
echo S("New user has been added");
exit;
}
### Set variables for adding
$button_txt = "Add User";
} elseif($op == "edit") {

if($go == "true") {
if($demo == "on") {
print E("You do not have permission to edit users in the demo");
}
### Save changes into DB
if($allcats == "sel") { if($cats == "") { print E("You did not select any categories for this user"); } } else { $cats = "all"; }
### Assemble the category array into one string for database entry ###
if(is_array($cats)) { $cats = implode(", ", $cats); }
$q['update'] = mysql_query ("UPDATE $t_user SET user='$user', pass='$pass', email='$email', categories='$cats', admin='$admin', news='$news', users='$users', cats='$categories', config='$config', words='$words' WHERE id = '$id'", $link) or E("Could not update user:<br>" . mysql_error());
echo S("User has been edited");
exit;
}
### Set variables for editing
$button_txt = "Save User";
$q['edit'] = mysql_query("SELECT * FROM $t_user WHERE id = '$id'", $link) or E("Couldn't retieve user info:<br>" . mysql_error());
$ev = mysql_fetch_array($q['edit'], MYSQL_ASSOC);
} elseif($op == "del") {

if($id == $useri['id']) {
print E("You cannot delete yourself");
}
if($go == "true") {
if($demo == "on") {
print E("You do not have permission to edit users in the demo");
}
$q['del2'] = mysql_query("DELETE FROM $t_user WHERE id = '$id'", $link) or E("Couldn't delete user:<br>" . mysql_error());
echo S("User has been deleted");
exit;
}
### Delete user record
$q['del'] = mysql_query("SELECT user FROM $t_user WHERE id = '$id'", $link) or E("Couldn't select user:<br>" . mysql_error());
$dv = mysql_fetch_array($q['del'], MYSQL_ASSOC);
?>
<form method="post" action="<?php  echo $_SERVER['PHP_SELF']; ?>" name="theform">
Are you sure you want to delete "<b><?=$dv['user']?></b>"?<br><br>
<input type="hidden" name="op" value="<?=$op?>">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="go" value="true">
<input type="submit" name="submit" value="Yes" class="input">&nbsp;&nbsp;<input type="button" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>'" value="No" class="input">
<?php 
exit;
}
?>

<form method="post" action="<?php  echo $_SERVER['PHP_SELF']; ?>" name="theform">
<table  width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
<tr><td bgcolor="<?php  print $MenuBg2; ?>">
Username:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="user" size="25" class="input" value="<?=$ev['user']?>">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>">
Password:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="password" name="pass" size="25" class="input" value="<?=$ev['pass']?>">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>">
Email:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="email" size="25" class="input" value="<?=$ev['email']?>">
</td></tr>
<?php 
if($ev['id'] != $useri['id']) {
if($usernum == "0") { ?>
<input type="hidden" name="cats" value="all">
<?php 
} else {

if($useri['cats'] == "on" || $useri['admin'] == "on") {
$q['cats'] = mysql_query("SELECT * FROM $t_cats", $link);
$num = mysql_num_rows($q['cats']);
?>
<tr><td bgcolor="<?php  print $MenuBg2; ?>">
Categories:
</td><td bgcolor="<?php  print $MenuBg1; ?>">

<input type="radio" name="allcats" value="all" id="allcats"<?php  if($ev['categories'] == "all" || $op == "add") { print " CHECKED"; } ?>> <label for="allcats">User can post under ALL categories</label><br>
<input type="radio" name="allcats" value="sel" id="selcats"<?php  if($ev['categories'] != "all" && $op != "add") { print " CHECKED"; } ?>> <label for="selcats">Use selected categories below</label><br>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr>
		<td>
<select name="cats[]" multiple size=5>
<?php 
/*
Commented code below is buggy; It will be implimented at a later date
/*
/* if($useri[categories] == "all") { */
### Assemble the categories field into an array for selection ###
$cats = explode(", ", $ev['categories']); 
while($cv = mysql_fetch_array($q['cats'], MYSQL_ASSOC)) {
print "<option value=\"{$cv['id']}\"";
foreach($cats as $u) {
if($cv['id'] == $u) { print " selected"; } } print ">" . stripslashes($cv['name']) . "\n";
}
/* } else {
### Assemble the USER'S categories field into an array for selection ###
$listcats = explode(", ", $useri[categories]);
$cats = explode(", ", $ev[categories]); 
foreach($listcats as $cv) {
$q['cat'] = mysql_query("SELECT * FROM $t_cats WHERE id = '$cv'", $link) or E("Couldn't select category with id:<br>" . mysql_error());
$cva = mysql_fetch_array($q['cat']);
print "<option value=\"$cv\"";
foreach($cats as $u) {
if($cv == $u) { print " selected"; } } print ">" . stripslashes($cva[name]) . "\n";
}
} */
?>
</select>
		</td>
		<td>
Use Ctrl-Click and Shift-Click to select or deselect multiple categories
		</td>
	</tr>
</table>

</td></tr>
<?php  } else {
if($ev['categories'] == "all") { ?>
<input type="hidden" name="allcats" value="all">
<?php  } else { ?>
<input type="hidden" name="allcats" value="sel">
<?php  } ?>
<input type="hidden" name="cats" value="<?php  print "{$ev['categories']}"; ?>">
<?php  } ?>
<?php  } if($usernum == "0") { ?>
<input type="hidden" name="news" value="on">
<input type="hidden" name="users" value="on">
<input type="hidden" name="categories" value="on">
<input type="hidden" name="config" value="on">
<?php  } else { ?>
<tr><td bgcolor="<?php  print $MenuBg2; ?>">
Permissions:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<?php  if($useri['admin'] == "on") { ?>
<input type="radio" name="admin" value="on" id="adminon"<?php  if($ev['admin'] == "on") { print " CHECKED"; } ?>> <label for="adminon">Ultimate Admin (unlimited access)</label><br>
<?php  } ?>
<input type="radio" name="admin" value="off" id="adminoff"<?php  if($ev['admin'] == "off" || $useri['admin'] == "off" || $op == "add") { print " CHECKED"; } ?>> <label for="adminoff">Use selected permissions below</label><br>
<?php  if($useri['news'] == "on" || $useri['admin'] == "on") { ?>
<input type="checkbox" name="news" id="news"<?php  if($ev['news'] == "on" || $op == "add") { print " CHECKED"; } ?>> <label for="news">News Admin (can edit/delete all posts - if unchecked, user can only edit/delete their own posts)</label><br><?php  } else { ?><input type="hidden" name="news" value="off"><?php  } ?>
<?php  if($useri['users'] == "on" || $useri['admin'] == "on") { ?>
<input type="checkbox" name="users" id="users"<?php  if($ev['users'] == "on") { print " CHECKED"; } ?>> <label for="users">User Admin (can add/edit/delete all users except admins)</label><br><?php  } else { ?><input type="hidden" name="users" value="off"><?php  } ?>
<?php  if($useri['cats'] == "on" || $useri['admin'] == "on") { ?>
<input type="checkbox" name="categories" id="cats"<?php  if($ev['cats'] == "on") { print " CHECKED"; } ?>> <label for="cats">Categories (can add/edit/delete all categories)</label><br><?php  } else { ?><input type="hidden" name="categories" value="off"><?php  } ?>
<?php  if($useri['config'] == "on" || $useri['admin'] == "on") { ?>
<input type="checkbox" name="config" id="config"<?php  if($ev['config'] == "on") { print " CHECKED"; } ?>> <label for="config">Config (can edit news configuration & output)</label><br><?php  } else { ?><input type="hidden" name="config" value="off"><?php  } ?>
<?php  if($useri['words'] == "on" || $useri['admin'] == "on") { ?>
<input type="checkbox" name="words" id="words"<?php  if($ev['words'] == "on") { print " CHECKED"; } ?>> <label for="words">Keywords (can edit word filter and keywords)</label><br>
</td></tr><?php  } else { ?><input type="hidden" name="words" value="off"><?php  } ?>
<?php 
}
} else {
if($ev['categories'] == "all") { ?>
<input type="hidden" name="allcats" value="all">
<?php  } else { ?>
<input type="hidden" name="allcats" value="sel">
<?php  } ?>
<input type="hidden" name="cats" value="<?php  print "{$ev['categories']}"; ?>">
<input type="hidden" name="admin" value="<?=$ev['admin']?>">
<input type="hidden" name="news" value="<?=$ev['news']?>">
<input type="hidden" name="users" value="<?=$ev['users']?>">
<input type="hidden" name="categories" value="<?=$ev['cats']?>">
<input type="hidden" name="config" value="<?=$ev['config']?>">
<?php  } ?>
<tr><td bgcolor="<?php  print $MenuBg2; ?>">
&nbsp;
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="hidden" name="op" value="<?=$op?>">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="go" value="true">
<input type="submit" name="submit" value="<?=$button_txt?>" class="input">&nbsp;&nbsp;
<input type="button" name="cancel" value="Cancel" class="input" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>'">
</td></tr>
</table><br>

<?php 
} else {

$q['info'] = mysql_query("SELECT * FROM $t_user ORDER BY user ASC", $link) or E("Couldn't select users:<br>" . mysql_error());
$num = mysql_num_rows($q['info']);
print "<b>$num</b> User(s) Found<br><br><a href=\"?op=add\">[ Add User ]</a> or click on a user below to edit";
if($num == "0") {
print "<br><br>No users found in database";
}
?>
<table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
<?php 
$i=1;
while ($r = mysql_fetch_array($q['info'], MYSQL_ASSOC)) {
?>
<tr><td nowrap><?=$i?>)&nbsp;</td>
<td bgcolor="#EEEEEE" width="80%">&nbsp;<a href="?op=edit&id=<?=$r['id']?>"><b><?php  echo $r['user']; ?></b></a></td>
<td width="20%" nowrap><?php  if($r['id'] != $useri['id']) { ?>&nbsp;<a href="?op=del&id=<?=$r['id']?>">[Delete]</a>&nbsp;<?php  } else { ?>&nbsp;<?php  } ?></td>
</tr>
<?php 
$i++;
}
?>
</table><br>

<?php 
}
include("cn_foot.php");
?>