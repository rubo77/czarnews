<?php 
$pagetitle = "Keyword Admin";
include("cn_auth.php");
include("cn_head.php");

// optional request parameters
if(!isset($go)) { $go = ""; }
if(!isset($id)) { $id = ""; }
// keyword row being edited; empty defaults when adding a new one
$ev = array("id"=>"", "word"=>"", "type"=>"", "replaced"=>"");

### If user is not a "Keyword Admin" or an "Ultimate Admin", do not grant access
if($useri['words'] != "on" && $useri['admin'] != "on") {
print E("You are not allowed to edit keywords");
}

if(isset($op)) {
if($op == "add") {
if($go == "true") {
### Add a new keyword into DB
$time = strtotime("now");
$content = addslashes($content);
if(!isset($cat)) { $cat = 0; }
if(empty($word) || empty($replace)) { print E("You have not filled in a keyword and/or a replacement for it"); }
$q['add'] = mysql_query("INSERT INTO $t_words VALUES ('', '$word', '$type', '$replace')", $link) or E("Could not insert keyword:<br>" . mysql_error());
echo S("New keyword has been added");
exit;
}
### Set variables for adding
$button_txt = "Add Keyword";

} elseif($op == "edit") {
if($go == "true") {
### Save changes into DB
$word = addslashes($word);
$replace = addslashes($replace);
$q['update'] = mysql_query("UPDATE $t_words SET word='$word', type='$type', replaced='$replace' WHERE id = '$id'", $link) or E("Could not update keyword:<br>" . mysql_error());
echo S("Keyword has been edited");
exit;
}
### Set variables for editing
$button_txt = "Save Keyword";
$q['edit'] = mysql_query("SELECT * FROM $t_words WHERE id = '$id'", $link) or E("Couldn't retieve keyword info:<br>" . mysql_error());
$ev = mysql_fetch_array($q['edit'], MYSQL_ASSOC);

} elseif($op == "del") {
if($go == "true") {
$q['del2'] = mysql_query("DELETE FROM $t_words WHERE id = '$id'", $link) or E("Couldn't delete keyword:<br>" . mysql_error());
echo S("Keyword has been deleted");
exit;
}
### Delete keyword record
$q['del'] = mysql_query("SELECT word FROM $t_words WHERE id = '$id'", $link) or E("Couldn't select keyword:<br>" . mysql_error());
$dv = mysql_fetch_array($q['del'], MYSQL_ASSOC);
?>
<form method="post" action="<?php  echo $_SERVER['PHP_SELF']; ?>" name="theform">
Are you sure you want to delete "<b><?=$dv['word']?></b>"?<br><br>
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
Keyword:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="word" size="25" class="input" value="<?=stripslashes($ev['word'])?>">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>">
Replacement:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<br>
<select name="type">
<option value="text"<?php  if($ev['type'] == "text" || $op == "add") { print " SELECTED"; } ?>>Text</option>
<option value="link"<?php  if($ev['type'] == "link") { print " SELECTED"; } ?>>Hyperink URL</option>
<option value="picture"<?php  if($ev['type'] == "picture") { print " SELECTED"; } ?>>Picture URL</option>
<option value="html"<?php  if($ev['type'] == "html") { print " SELECTED"; } ?>>HTML Code</option>
</select>
<textarea cols="40" rows="3" name="replace" class="input"><?=stripslashes($ev['replaced'])?></textarea>
</td></tr>
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
// Set limits for multiple pages
if(!isset($pg)) { $pg = 1; }
// Number of keyword items to display per page
$pgset = "25";
$lims = ($pg-1)*$pgset;

$q['info'] = mysql_query("SELECT * FROM $t_words ORDER BY word ASC LIMIT $lims, $pgset", $link) or E("Couldn't select keyword:<br>" . mysql_error());
$q['count'] = mysql_query("SELECT id FROM $t_words", $link) or E("Couldn't count keywords:<br>" . mysql_error());
$wordnum = mysql_num_rows($q['count']);
print "<b>$wordnum</b> Keyword(s) Found<br><br><a href=\"?op=add\">[ Add Keyword ]</a> or click on a keyword below to edit";
if($wordnum == "0") {
print "<br><br>No keywords found in database";
}
?>
<table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
<?php 
$i=$lims+1;
while ($r = mysql_fetch_array($q['info'], MYSQL_ASSOC)) {
if($r['type'] == "link") { $r['replaced'] = "<a href=\"{$r['replaced']}\" target=\"_blank\">{$r['word']}</a>";
} elseif($r['type'] == "picture") { $r['replaced'] = "<img src=\"{$r['replaced']}\" alt=\"{$r['word']}\">";
} elseif($r['type'] != "html") { $r['replaced'] = cutstr($r['replaced']); }
?>
<tr><td nowrap><?=$i?>)&nbsp;</td>
<td bgcolor="#EEEEEE" width="40%">&nbsp;<a href="?op=edit&id=<?=$r['id']?>"><b><?php  echo cutstr($r['word']); ?></b></a></td>
<td bgcolor="#EEEEEE" width="40%">&nbsp;<?php  echo $r['replaced']; ?>&nbsp;</td>
<td nowrap><a href="?op=del&id=<?=$r['id']?>">[Delete]</a></td>
</tr>
<?php 
$i++;
}
?>
</table><br>
<div align="center">
<?php 
### Page numbering code
$totalpages = 0;
if ($wordnum > $pgset) {
if ($pg != "1") {
$pgn = $pg-1;
print "<a href=\"?pg=$pgn\">";
print "&lt;&lt; Prev";
print "</a>&nbsp;&nbsp;";
}

$totalpages = ceil($wordnum / $pgset);
for ($loop = 1; ;$loop++) {
if ($loop > $totalpages) {
break;
}
if ($loop == $pg) {
print "<b>$loop</b>";
print "&nbsp;&nbsp;";
}
else {
print "<b><a href=\"{$_SERVER['PHP_SELF']}?pg=$loop\">";
print $loop;
print "</a></b>";
print "&nbsp;&nbsp;";
}
}
}

if ($pg < $totalpages) {
$pgn = $pg+1;
print "<a href=\"?pg=$pgn\">";
print "Next &gt;&gt;";
print "</a>";
}
?>
</div>
<?php 
}
include("cn_foot.php");
?>