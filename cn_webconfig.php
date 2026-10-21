<?php 
$pagetitle = "CzarNews Configuration";
include("cn_auth.php");
include("cn_head.php");

// optional request parameters
if(!isset($op)) { $op = ""; }
if(!isset($go)) { $go = ""; }
if(!isset($demo)) { $demo = "off"; }

### If user is not a "Config Admin" or an "Ultimate Admin", do not grant access
if($useri['config'] != "on" && $useri['admin'] != "on") {
print E("You are not allowed to edit news configurations");
}

if($op == "edit") {
if($go == "true") {
if($demo == "on") { print S("This would normally update and save the changes you have made in the configuration table."); }
### Save changes into DB
$sitename = addslashes($sitename);
$lang = addslashes($lang);
$logo = addslashes($logo);
$q['update'] = mysql_query("UPDATE $t_conf SET sitename='$sitename', siteurl='$siteurl', scripturl='$scripturl', newslimit='$newslimit', timezone='$timezone', dateform='$dateform', output='$output', source='$source', author='$author', coms_text='$coms_text', lang='$lang', logo='$logo', words='$words', comments='$comments', search='$search', pages='$pages', catbox='$catbox'", $link) or E("Could not update news configurations:<br>" . mysql_error());
echo S(L("News configurations have been updated"));
}
}

// Define a few variables...
$servtime = (date("Z")/3600);
$tpath = $_SERVER['PHP_SELF'];

// Reverse search of strrchr.
function strrrchr($haystack,$needle)
{
// Returns everything before $needle (inclusive).
return substr($haystack,0,strrpos($haystack,$needle)+1);
}

$rtpath = strrrchr($tpath,"/");

// Set default configuration setttings
$defoutput = "<b>{subject}</b> Posted on {date} by {author}<br>
{news}<br>
<small>{source}</small>";
$setsource = "<small>Source: <a href=\"{surl}\" target=\"_blank\">{sname}</a></small><br><br>";
$setauthor = "<a href=\"mailto:{aemail}\">{aname}</a>";
$setcoms = "View/Post Comment ({cnum})";

// Check if config settings already exist
$q['chk'] = mysql_db_query($dbname,"SELECT COUNT(*) from $t_conf");
$confnum = mysql_result($q['chk'],0,"COUNT(*)"); 

if($confnum != "1") {
$q['del'] = mysql_query("DELETE FROM $t_conf", $link) or E("Could not clear configuration settings:<br>" . mysql_error());
$q['prep'] = mysql_query("INSERT INTO $t_conf (sitename, siteurl, scripturl, newslimit, timezone, dateform, output, source, author, coms_text, version, lang, logo, words, comments, search, pages, catbox) VALUES ('', 'http://{$_SERVER['HTTP_HOST']}/', 'http://{$_SERVER['HTTP_HOST']}$rtpath', '15', '$servtime', 'M d, Y', '$defoutput', '$setsource', '$setauthor', '$setcoms', '$cnver', 'en', '', 'on', 'on', 'on', 'on', 'on')", $link) or E("Could not prepare configuration settings:<br>" . mysql_error());
}
$q['edit'] = mysql_query("SELECT * FROM $t_conf LIMIT 1", $link) or E("Could not retieve configuration settings:<br>" . mysql_error());
$ev = mysql_fetch_array($q['edit'], MYSQL_ASSOC);
?>

<form method="post" action="<?php  echo $_SERVER['PHP_SELF']; ?>" name="theform">
<table  width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
<tr><td nowrap>
<b>Site & News Settings</b>
</td><td>
<hr size="2" color="#000000" width="100%">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
Website Name:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="sitename" size="25" class="input" value="<?=stripslashes($ev['sitename'])?>">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
Website URL:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="siteurl" size="35" class="input" value="<?=$ev['siteurl']?>">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
HTTP path to CzarNews:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="scripturl" size="35" class="input" value="<?=$ev['scripturl']?>">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
<?=L("Language")?>:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<select name="lang">
<option value="en"<?php if($ev['lang'] == "en") { print " SELECTED"; } ?>>English</option>
<option value="de"<?php if($ev['lang'] == "de") { print " SELECTED"; } ?>>Deutsch</option>
</select>
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
<?=L("Logo")?>:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="logo" size="35" class="input" value="<?=htmlspecialchars($ev['logo'], ENT_QUOTES)?>"><br>
<small><?=L("Logo URL (shown in the admin header; leave empty for default)")?></small>
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
News per page:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="newslimit" size="3" class="input" value="<?=$ev['newslimit']?>">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
Timezone Offset:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<u>Current Server Time:</u><br>
GMT <?php  print (date("Z")/3600); ?> Hours<br>
<?php  print date("F j, Y, g:i a"); ?><br><br>
<u>Display News in:</u><br>
	<select name="timezone">
<?php  foreach($tzones as $t1 => $t2) { ?>
<option value="<?php  print $t1; ?>"<?php if($t1==$ev['timezone']){print " SELECTED";}?>><?php  print $t2 ?></option>
<?php  } ?>
	</select><br>
</td></tr>
<tr><td nowrap>
<b>Output Settings</b><br>
(<a href="news.php" target="_blank">View Current</a>)
</td><td>
<hr size="2" color="#000000" width="100%">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
Date Format:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="dateform" size="15" class="input" value="<?=$ev['dateform']?>">
(Use PHP's <a href="http://www.php.net/manual/en/function.date.php" target="_blank">date()</a> format)
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
Comment Link Text:<br>
{cnum} - # of coms
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="text" name="coms_text" size="20" class="input" value="<?=$ev['coms_text']?>">
("View/Post Comments" Text)
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
Source Link:<br>
{sname} {surl}
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<small>HTML format the source and author links will be made with</small>
<textarea cols="40" rows="2" name="source" class="input"><?=$ev['source']?></textarea>
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
Author Link:<br>
{aemail} {aname}
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<textarea cols="40" rows="2" name="author" class="input"><?=$ev['author']?></textarea>
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>">
Output Display:<br><br>
<small><u>Available Tags:</u></small><br>
{news} {subject} {author} {date} {source} {comments}<br>
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<small>HTML format that each news item will be generated with</small>
<textarea cols="40" rows="6" name="output" class="input"><?=stripslashes($ev['output'])?></textarea>
</td></tr>
<tr><td nowrap>
<b>Display Options</b>
</td><td>
<hr size="2" color="#000000" width="100%">
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>" nowrap>
Additional Options:
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="checkbox" name="words" id="words"<?php  if($ev['words'] == "on") { print " CHECKED"; } ?>> <label for="words">Use keywords/word filter for news/comments</label><br>
<input type="checkbox" name="comments" id="comments"<?php  if($ev['comments'] == "on") { print " CHECKED"; } ?>> <label for="comments">Allow news comments</label><br>
<input type="checkbox" name="search" id="search"<?php  if($ev['search'] == "on") { print " CHECKED"; } ?>> <label for="search">Display search box at bottom of news page</label><br>
<input type="checkbox" name="pages" id="pages"<?php  if($ev['pages'] == "on") { print " CHECKED"; } ?>> <label for="pages">Display page numbers at bottom of news page</label><br>
<input type="checkbox" name="catbox" id="catbox"<?php  if($ev['catbox'] == "on") { print " CHECKED"; } ?>> <label for="catbox">Display category selection box at top of news page</label><br>
</td></tr>
<tr><td bgcolor="<?php  print $MenuBg2; ?>">
&nbsp;
</td><td bgcolor="<?php  print $MenuBg1; ?>">
<input type="hidden" name="op" value="edit">
<input type="hidden" name="go" value="true">
<input type="submit" name="submit" value="<?=L("Save Settings")?>" class="input">&nbsp;&nbsp;
<input type="button" name="cancel" value="<?=L("Cancel")?>" class="input" onClick="javascript:location.href='<?=$_SERVER['PHP_SELF']?>'">
</td></tr>
</table><br>
</form>

<?php 
include("cn_foot.php");
?>