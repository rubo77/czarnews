<?
require($tpath . "cn_config.php");
require_once( "includes/php7_fix.inc.php");

// Set limits for multiple pages 
if(!isset($pg)) { $pg = 1; }
// Number of news items to display per page 
$pgset = $set['newslimit'];
$lims = ($pg-1)*$pgset;

// If article id is set [v1.01] 
if(isset($a)) {
// Comment posting [v1.12] 
	if($post == "comment") {
	$q[userchk] = mysql_query("SELECT * FROM $t_user WHERE user = '$name' LIMIT 1", $link) or E("Couldn't check reserved usernames:<br>" . mysql_error());
	$usernum = mysql_num_rows($q[userchk]);
	if(!isset($passw) || $usernum == "0") {
		if($usernum == "1") {
		?><b>Error</b><br><br>
		The username you entered is currently reserved.  To post with this username, please enter a password below:<br>
		<div align="center"><form method="post" action="<?=$PHP_SELF?>"><input type="password" name="passw">
		<input type="hidden" name="name" value="<?=$name?>">
		<input type="hidden" name="email" value="<?=$email?>">
		<input type="hidden" name="comment" value="<?=$comment?>">
		<input type="hidden" name="a" value="<?=$a?>">
		<input type="hidden" name="post" value="comment">
		<input type="submit" value="Submit">
		</form></div><?
		} else {
		// Post comment 
		if(empty($name) || empty($email) || empty($comment)) { print("Please fill-in all fields.  All fields are required to post a comment.<p>");
		} else {
		$q[ins_com] = mysql_query("INSERT INTO $t_coms (id, news_id, name, email, comment, date, ip) VALUES ('', '$a', '$name', '$email', '$comment', '$now', '$REMOTE_ADDR')", $link) or E("Couldn't insert new comment:<br>" . mysql_error());
		print "Your comment has been added.<br>[ <a href=\"$PHP_SELF?a=$a\">&lt;&lt; Go Back</a> ]<br><br>";
		}
		}
	} else {
		$q[userchk] = mysql_query("SELECT * FROM $t_user WHERE user = '$name' AND pass='$passw' LIMIT 1", $link) or E("Couldn't select news article:<br>" . mysql_error());
		if(mysql_num_rows($q[userchk]) == "1") {
		// Post comment with protected username 
		$q[ins_com] = mysql_query("INSERT INTO $t_coms (id, news_id, name, email, comment, date, ip) VALUES ('', '$a', '$name', '$email', '$comment', '$now', '$REMOTE_ADDR')", $link) or E("Couldn't insert new comment:<br>" . mysql_error());
		print "You comment has been added.<br>[ <a href=\"$PHP_SELF?a=$a\">&lt;&lt; Go Back</a> ]<br><br>";
		} else {
		print "The password you entered was incorrect.<br>[ <a href=\"javascript:history.go(-1)\">&lt;&lt; Go Back</a> ]<br><br>";
		}
	}


	} else {
	$q[info] = mysql_query("SELECT * FROM $t_news WHERE id = '$a' LIMIT 1", $link) or E("Couldn't select news article:<br>" . mysql_error());
	$newsnum = mysql_num_rows($q[info]);
	}

// If search is performed [v1.12] 
} elseif(isset($s)) {
$q[info] = mysql_query("SELECT * FROM $t_news WHERE content LIKE '%$s%' ORDER BY date DESC LIMIT $lims, $pgset", $link) or E("Couldn't search news articles:<br>" . mysql_error());
$newsnum = mysql_num_rows($q[info]);

// Retrieve all news items	
} else {

$extra = "";
if($c != "") { $extra = "WHERE cat = '$c'"; }
$q[cats] = mysql_query("SELECT * FROM $t_cats ORDER BY name ASC", $link) or E("Couldn't select categories:<br>" . mysql_error());
$catnum = mysql_num_rows($q[cats]);

$q[info] = mysql_query("SELECT * FROM $t_news $extra ORDER BY date DESC LIMIT $lims, $pgset", $link) or E("Couldn't select news:<br>" . mysql_error());
$q[count] = mysql_query("SELECT id FROM $t_news $extra", $link) or E("Couldn't count news:<br>" . mysql_error());
$newsnum = mysql_num_rows($q[count]);

if($catnum > "1" && $set[catbox] == "on") { ?>
<table width="90%" border="0" cellspacing="0" cellpadding="0"><tr>
<td align="left"><b>View news posted in category:</b></td>
<td align="right"><? print catBox("c","$c","yes","news"); ?></td>
</tr></table><br>
<?
}

}

if($newsnum == "0") {
print "<br>No news found in database<br><br>";
}

if(isset($q[info])) { // $q[info] check 

// Begin output of news items 
while ($r = mysql_fetch_array($q[info], MYSQL_ASSOC)) {
$auid = $r[author];
$output = $set[output];
$r[subject] = "<a name=\"cn_$r[id]\"></a>$r[subject]";
$serv_tzone = (date("Z")/3600);
// Format date to current timezone, using format setting specified 
$r[date] = date("$set[dateform]", zonechange("$serv_tzone", "$set[timezone]", "$r[date]"));

// Summarize story option [v1.12] 
if($r[sumstory] == "on" && isset($a)) { 
$output = str_replace("{news}", "$r[content]<p>$r[content2]", $output);
} elseif($r[sumstory] == "on") {
$r[content] .= "<p><small><a href=\"" . $_SERVER['PHP_SELF'] . "?a=$r[id]\">Read More...</a></small>";
$output = str_replace("{news}", "$r[content]", $output);
} else {
$output = str_replace("{news}", "$r[content]", $output);
}
$output = str_replace("\n", "<br>\n", $output);

// Use Keywords and word filter if filter is turned "on" [v1.12] 
if($set[words] == "on") {
$q[words] = mysql_query("SELECT * FROM $t_words ORDER BY word ASC", $link) or E("Couldn't select keywords:<br>" . mysql_error());
while ($w = mysql_fetch_array($q[words], MYSQL_ASSOC)) {
if($w[type] == "link") {
$w[replace] = "<a href=\"$w[replaced]\" target=\"_blank\">$w[word]</a>";
} elseif($w[type] == "picture") {
$w[replace] = "<img src=\"$w[replaced]\" alt=\"$w[word]\">";
}
$output = str_replace("$w[word]", "$w[replaced]", $output);
}
}

// Build user-defined source link [v1.12] 
if(empty($r[source]) || empty($r[sourceurl])) {
$output = str_replace("{source}", "", $output);
} else {
if(empty($set[source])) {
$setsource = "<a href=\"$r[sourceurl]\" target=\"_blank\">$r[source]</a>";
$output = str_replace("{source}", "$setsource", $output);
} else {
$setsource = str_replace("{sname}", "$r[source]", $set[source]);
$setsource = str_replace("{surl}", "$r[sourceurl]", $setsource);
$output = str_replace("{source}", "$setsource", $output);
}
}

// Build user-defined author link [v1.12] 
if(empty($set[author])) {
$setauthor = "<a href=\"mailto:" . preg_replace("/@/", " AT ", getinfo($r[author],"email")) . "\">" . getinfo($r[author]) . "</a>";
$output = str_replace("{author}", "$setauthor", $output);
} else {
$setauthor = str_replace("{aemail}", "" . preg_replace("/@/", " AT ", getinfo($r[author],"email")) . "", $set[author]);
$setauthor = str_replace("{aname}", "" . getinfo($r[author]) . "", $setauthor);
$output = str_replace("{author}", "$setauthor", $output);
}

$output = str_replace("{subject}", "$r[subject]", $output);
$output = str_replace("{date}", "$r[date]", $output);

// View/Post Comments Link [v1.12] 
if($set[comments] == "on" && !isset($a)) {
$q[comsn] = mysql_query("SELECT id FROM $t_coms WHERE news_id = '$r[id]' ORDER BY date ASC", $link) or E("Couldn't select comments for current news article:<br>" . mysql_error());
$comsnum = mysql_num_rows($q[comsn]);
if(empty($set[coms_text])) {
$output = str_replace("{comments}", "<a href=\"" . $_SERVER['PHP_SELF'] . "?a=$r[id]\"><small>View/Post Comments ($comsnum)</small></a>", $output);
} else {
$setcoms_text = str_replace("{cnum}", "$comsnum", $set[coms_text]);
$output = str_replace("{comments}", "<a href=\"" . $_SERVER['PHP_SELF'] . "?a=$r[id]\"><small>$setcoms_text</small></a>", $output);
}
} elseif($set[comments] == "on") {
$output = str_replace("{comments}", "<small>[ <a href=\"" . $_SERVER['PHP_SELF'] . "\">&lt;&lt; Return to News Page</a> ]</small>", $output);
} else {
$output = str_replace("{comments}", "", $output);
}

// Highlight searched item in returned news if search is performed and searches are allowed [v1.12] 
if(isset($s)) {
$output = highlight($output, "$s"); // case-insensitive or partial word search 
//$output = str_replace("$s", "<span style=\"background-color:yellow; color: black\">$s</span>", $output); // exact word search 
}

// Output formatted news 
print stripslashes($output);

} // End While (output of news items) 

// If single article is viewed, and comments are turned "on", list comments [v1.12] 
if(isset($a) && $set[comments] == "on") {
$q[coms] = mysql_query("SELECT * FROM $t_coms WHERE news_id = '$a' ORDER BY date ASC", $link) or E("Couldn't select comments for current news article:<br>" . mysql_error());
$comsnum = mysql_num_rows($q[coms]);
	if($comsnum != "0") {
?>
<hr size="1" width="95%">
<b>Comments: (<?=$comsnum?> total)</b><br>
<?
		while ($c = mysql_fetch_array($q[coms], MYSQL_ASSOC)) {
		print "
		<br>\n
		<b>Name:</b> $c[name]<br>\n
		<b>Email:</b> <a href=\"mailto:$c[email]\"></a>$c[email]<br>\n
		<b>Comments:</b> $c[comment]<br>\n
		<br>\n
		";
		}
	}
?>
<hr size="1" width="95%">
<b>Post a Comment</b><br><br>
<table border="0" cellpadding="1" cellspacing="0">
<form method="post" action="<?=$PHP_SELF?>">
<tr><td><b>Name:</b></td><td><input type="text" name="name"></td></tr>
<tr><td><b>Email:</b></td><td><input type="text" name="email"></td></tr>
<tr><td><b>Comment:</b></td><td><textarea rows="4" cols="30" name="comment"></textarea></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="Add Comment"></td></tr>
<input type="hidden" name="a" value="<?=$a?>">
<input type="hidden" name="post" value="comment">
</form>
</table>
<?
}
?>
<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center"><tr>
<? if($set[search] == "on" && !isset($a)) { ?>
<form method="post" action="<?=$PHP_SELF?>">
<td align="left">
</td><? } ?>
<td align="center">
<?
$extras = "";
if($c != "") { $extras .= "&c=$c"; }

### Page numbering code 
if($newsnum != "1" && $set[pages] == "on") {
if ($newsnum > $pgset) {
if ($pg != "1") {
$pgn = $pg-1;
print "<a href=\"?pg=$pgn$extras\">";
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
print "<b><a href=\"$PHP_SELF?pg=$loop$extras\">";
print $loop;
print "</a></b>";
print "&nbsp;&nbsp;";
}
}
}

if ($pg < $totalpages) {
$pgn = $pg+1;
print "<a href=\"?pg=$pgn$extras\">";
print "Next &gt;&gt;";
print "</a>";
}
}

} // End $q[info] check 
?>
</td>

</tr></table>
<br>
