<?
$pagetitle = "News Admin";
include("cn_auth.php");
include("cn_head.php");

if(isset($op)) {
if($op == "add") {

if($go == "true") {
### Add a new news into DB
$time = strtotime("now");
$content = addslashes($content);
if(!isset($cat)) { $cat = 0; }
if(empty($subject) || empty($content)) { print E("You have not filled in a subject and/or content for your news post"); }
if(!in_array($cat, $ucats) && $useri[categories] != "all") { print E("You are not allowed to add news posts out of your category permisions"); }
$q[add] = mysql_query ("INSERT INTO $t_news (id, author, cat, subject, content, content2, sumstory, date, source, sourceurl) VALUES ('', '$useri[id]', '$cat', '$subject', '$content', '$content2', '$sumstory', '$time', '$source', '$sourceurl')", $link) or E("Could not insert news:<br>" . mysql_error());

echo S("New news has been added");
exit;
}
### Set variables for adding
$button_txt = "Add News";
} elseif($op == "edit") {
$q[edit] = mysql_query("SELECT * FROM $t_news WHERE id = '$id'", $link) or E("Couldn't retieve news info:<br>" . mysql_error());
$ev = mysql_fetch_array($q[edit], MYSQL_ASSOC);
if(!in_array($ev[cat], $ucats) && $useri[categories] != "all") { print E("You are not allowed to edit news posts out of your category permisions"); }
if($useri[news] != "on" && $useri[id] != $ev[id]) { print E("You are only allowed to edit your own news items"); }

if($go == "true") {
// Check if subject and content is filled-in 
if(empty($subject) || empty($content)) { print E("You have not filled in a subject and/or content for your news post"); }
### Save changes into DB
$news = addslashes($news);
$context = addslashes($context);
$q[update] = mysql_query ("UPDATE $t_news SET cat='$cat', subject='$subject', content='$content', content2='$content2', sumstory='$sumstory', source='$source', sourceurl='$sourceurl' WHERE id = '$id'", $link) or E("Could not update news:<br>" . mysql_error());
echo S("News has been edited");
exit;
}
### Set variables for editing
$button_txt = "Save News";
} elseif($op == "del") {

### Fetch info about record
$q[del] = mysql_query("SELECT * FROM $t_news WHERE id = '$id'", $link) or E("Couldn't select news:<br>" . mysql_error());
$dv = mysql_fetch_array($q[del], MYSQL_ASSOC);
if(!in_array($dv[cat], $ucats) && $useri[categories] != "all") { print E("You are not allowed to delete news posts out of your category permisions"); }
if($useri[news] != "on" && $useri[id] != $dv[id]) { print E("You are only allowed to delete your own news items"); }
if($go == "true") {
### Delete news record
$q[del] = mysql_query("DELETE FROM $t_news WHERE id = '$id'", $link) or E("Couldn't delete news:<br>" . mysql_error());
$q[del2] = mysql_query("DELETE FROM $t_coms WHERE news_id = '$id'", $link) or E("Couldn't delete comments:<br>" . mysql_error());
echo S("News item has been deleted");
exit;
}
?>
<form method="post" action="<? echo $PHP_SELF; ?>" name="theform">
Are you sure you want to delete "<b><?=$dv[subject]?></b>"?<br><br>
<input type="hidden" name="op" value="<?=$op?>">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="go" value="true">
<input type="submit" name="submit" value="Yes" class="input">&nbsp;&nbsp;<input type="button" onClick="javascript:location.href='<?=$PHP_SELF?>'" value="No" class="input">
<?
exit;
### Do an action to multiple 
} elseif($op == "multi") {
if(count($item) <= "0") { print E("You must check at least one item"); }
if($mode == "move") {
if($go == "true") {
foreach($item as $id => $t) {
$q[upd] = mysql_query("SELECT * FROM $t_news WHERE id = '$id'", $link) or E("Couldn't select news:<br>" . mysql_error());
$ev = mysql_fetch_array($q[upd], MYSQL_ASSOC);
// Check the category against user permissions
if(!in_array($ev[cat], $ucats) && $useri[categories] != "all") {
$movs .= "$t <b>[NOT MOVED - INSUFFICIENT CATEGORY PERMISSIONS]</b><br>\n";
} elseif($useri[news] != "on" && $useri[id] != $ev[id]) {
$movs .= "$t <b>[NOT MOVED - INSUFFICIENT USER PERMISSIONS]</b><br>\n";
} else {
$q[del2] = mysql_query("UPDATE $t_news SET cat = '$cat' WHERE id = '$id'", $link) or E("Couldn't move news:<br>" . mysql_error());
$movs .= "$t<br>\n";
}
}
### Delete news record
echo S("The following news items have been moved:<br><br>" . $movs);
exit;
}
?>
<form method="post" action="<? echo $PHP_SELF; ?>" name="theform">
Which category do you want to move these items to?<br><br>

<table  width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
<tr><td bgcolor="<? print $MenuBg2; ?>">
Category:
</td><td bgcolor="<? print $MenuBg1; ?>">
<? print catBox("cat","$ev[cat]"); ?>
</td></tr>
<tr><td colspan="2">
<?
foreach($item as $id => $t) {
print "<b>$t</b><br><input type=\"hidden\" name=\"item[$id]\" value=\"$t\">\n";
}
?>
</td></tr>
</table>
<br>
<input type="hidden" name="op" value="<?=$op?>">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="go" value="true">
<input type="submit" name="submit" value="Move Items" class="input">&nbsp;&nbsp;<input type="button" onClick="javascript:location.href='<?=$PHP_SELF?>'" value="Cancel" class="input">
<?
exit;
} elseif($mode == "del") {
if($go == "true") {
foreach($item as $id => $t) {
$q[del] = mysql_query("SELECT * FROM $t_news WHERE id = '$id'", $link) or E("Couldn't select news:<br>" . mysql_error());
$dv = mysql_fetch_array($q[del], MYSQL_ASSOC);
// Check the category against user permissions
if(!in_array($dv[cat], $ucats) && $useri[categories] != "all") {
$dels .= "$t <b>[NOT DELETED - INSUFFICIENT CATEGORY PERMISSIONS]</b><br>\n";
} elseif($useri[news] != "on" && $useri[id] != $ev[id]) {
$dels .= "$t <b>[NOT DELETED - INSUFFICIENT USER PERMISSIONS]</b><br>\n";
} else {
$q[del] = mysql_query("DELETE FROM $t_news WHERE id = '$id'", $link) or E("Couldn't delete news:<br>" . mysql_error());
$q[del2] = mysql_query("DELETE FROM $t_coms WHERE news_id = '$id'", $link) or E("Couldn't delete comments:<br>" . mysql_error());
$dels .= "$t<br>\n";
}
}
### Delete news record
echo S("The following news items have been deleted:<br><br>" . $dels);
exit;
}
?>
<form method="post" action="<? echo $PHP_SELF; ?>" name="theform">
Are you sure you want to delete these items?<br><br>
<?
foreach($item as $id => $t) {
print "<b>$t</b><br><input type=\"hidden\" name=\"item[$id]\" value=\"$t\">\n";
}
?>
<br>
<input type="hidden" name="op" value="<?=$op?>">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="go" value="true">
<input type="submit" name="submit" value="Yes" class="input">&nbsp;&nbsp;<input type="button" onClick="javascript:location.href='<?=$PHP_SELF?>'" value="No" class="input">
<?
exit;
}
}
?>

<form method="post" action="<? echo $PHP_SELF; ?>" name="theform">
<table  width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
<tr><td bgcolor="<? print $MenuBg2; ?>">
Author:
</td><td bgcolor="<? print $MenuBg1; ?>">
<b><? if(empty($ev[author])) { print $useri[user]; } else { print getinfo($ev[author]); } ?></b>
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>">
Category:
</td><td bgcolor="<? print $MenuBg1; ?>">
<? print catBox("cat","$ev[cat]"); ?>
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>">
Subject:
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="text" name="subject" size="35" class="input" value="<?=stripslashes($ev[subject])?>">
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>">
Content:
</td><td bgcolor="<? print $MenuBg1; ?>">
<textarea cols="40" rows="6" name="content" class="input"><?=stripslashes($ev[content])?></textarea>
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>">
Entire Story:
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="checkbox" name="sumstory" id="sumstory" onClick="openBox(1);"<? if($ev[sumstory] == "on") { print " CHECKED"; } ?>> <label for="sumstory">Use summary/entire story feature</label><br>
<div id="box1" style="display: <? if($ev[sumstory] == "on") { print "block"; } else { print "none"; } ?>">
<textarea cols="40" rows="6" name="content2" class="input"><?=stripslashes($ev[content2])?></textarea>
</div>
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>">
&nbsp;
</td><td bgcolor="<? print $MenuBg1; ?>">
If you got your news from somewhere else, you can cite the source below.  Otherwise, leave both fields blank.
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>" nowrap>
Source Name:
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="text" name="source" size="25" class="input" value="<?=$ev[source]?>">
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>">
Source URL:
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="text" name="sourceurl" size="35" class="input" value="<?=$ev[sourceurl]?>">
</td></tr>
<tr><td bgcolor="<? print $MenuBg2; ?>">
&nbsp;
</td><td bgcolor="<? print $MenuBg1; ?>">
<input type="hidden" name="op" value="<?=$op?>">
<input type="hidden" name="id" value="<?=$id?>">
<input type="hidden" name="go" value="true">
<input type="submit" name="submit" value="<?=$button_txt?>" class="input">&nbsp;&nbsp;
<input type="button" name="cancel" value="Cancel" class="input" onClick="javascript:location.href='<?=$PHP_SELF?>'">
</td></tr>
</table><br>

<?
} else {
// Set limits for multiple pages
if(!isset($pg)) { $pg = 1; }
// Number of news items to display per page
$pgset = "25";
$lims = ($pg-1)*$pgset;

if($useri[news] != "on") {
$t_news .= " WHERE author = '$useri[id]'";
}

$q[info] = mysql_query("SELECT * FROM $t_news ORDER BY date DESC LIMIT $lims, $pgset", $link) or E( "Couldn't select news:<br>" . mysql_error());
// Count news posts that current user has access to
$newsnum = 0;
$q[count] = mysql_query("SELECT id FROM $t_news", $link) or E( "Couldn't count news:<br>" . mysql_error());
while ($num = mysql_fetch_array($q[count], MYSQL_ASSOC)) {
if(in_array($num[cat], $ucats) || $useri[categories] == "all") { $newsnum++; }
}
print "<b>$newsnum</b> News Article(s) Found<br><br><a href=\"?op=add\">[ Add News ]</a> or click on a news article below to edit";
if($newsnum == "0") {
print "<br><br>No news found in database";
}
?>
<table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
<form method="post" action="<? echo $PHP_SELF; ?>" name="theform">
<?
$i=$lims+1;
while ($r = mysql_fetch_array($q[info], MYSQL_ASSOC)) {
if(in_array($r[cat], $ucats) || $useri[categories] == "all") {
$q[cinfo] = mysql_query("SELECT name FROM $t_cats WHERE id='$r[cat]'", $link);
$inf = mysql_fetch_array($q[cinfo], MYSQL_ASSOC);
?>
<tr><td bgcolor="#EEEEEE"><input type="checkbox" name="item[<?=$r[id]?>]" value="<?=cutstr($r[subject])?>"></td><td bgcolor="#EEEEEE" width="60%">&nbsp;<a href="?op=edit&id=<?=$r[id]?>"><b><? echo cutstr($r[subject]); ?></b></a></td><td bgcolor="#EEEEEE">&nbsp;<?=$inf[name]?>&nbsp;</td><td bgcolor="#EEEEEE" width="20%" align="right" nowrap>&nbsp;<? echo date("M d, Y",$r[date]); ?>&nbsp;</td><td><a href="?op=del&id=<?=$r[id]?>">[Delete]</a></td></tr>
<?
$i++;
}
}
?>
</table>
<? if($newsnum != "0") { ?>
<table border="0" cellpadding="0" cellspacing="2">
<tr><td>
With Selected:&nbsp;&nbsp;
</td><td>
<input type="hidden" name="op" value="multi">
<select name="mode">
<option value="move">Move</option>
<option value="del">Delete</option>
</select>
</td><td>
<input type="submit" name="submit" value="Submit" class="input">
</td></tr>
</table>
<? } ?>
<div align="center">
<?
### Page numbering code
if ($newsnum > $pgset) {
if ($pg != "1") {
$pgn = $pg-1;
print "<a href=\"?pg=$pgn\">";
print "&lt;&lt; Prev";
print "</a>&nbsp;&nbsp;";
}

$totalpages = ceil($newsnum / $pgset);
for ($loop = 1; ;$loop++) {
if ($loop > $totalpages) {
break;
}
if ($loop == $pg) {
print "<b>$loop</b>";
print "&nbsp;&nbsp;";
}
else {
print "<b><a href=\"$PHP_SELF?pg=$loop\">";
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
</form>
<?
}
include("cn_foot.php");
?>