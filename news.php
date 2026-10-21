<?php
// $tpath is set by the page that embeds this file; default to this
// directory so news.php also works when requested directly. It must
// never come from the request (remote file inclusion).
if(!isset($tpath)) { $tpath = __DIR__ . "/"; }
require($tpath . "cn_config.php");
require_once(__DIR__."/includes/php7_fix.inc.php");

// Set limits for multiple pages 
if(!isset($pg)) { $pg = 1; }
$pg = max(1, (int)$pg);
if(!isset($c)) { $c = ""; }
if(!isset($post)) { $post = ""; }
// Request ids are always numeric; incoming values go into SQL queries
if(isset($a)) { $a = (int)$a; }
// Number of news items to display per page 
$pgset = $set['newslimit'];
$lims = ($pg-1)*$pgset;

// If article id is set [v1.01] 
if(isset($a)) {
	// Comment posting [v1.12] 
	if($post == "comment") {
		$name_esc = mysql_real_escape_string($name ?? "", $link);
		$email_esc = mysql_real_escape_string($email ?? "", $link);
		$comment_esc = mysql_real_escape_string($comment ?? "", $link);
		$q['userchk'] = mysql_query("SELECT * FROM $t_user WHERE user = '$name_esc' LIMIT 1", $link) or E("Couldn't check reserved usernames:<br>" . mysql_error());
		$usernum = mysql_num_rows($q['userchk']);
		if(!isset($passw) || $usernum == "0") {
			if($usernum == "1") {
				?><b><?=L("Error")?></b><br><br>
				<?=L("The username you entered is currently reserved.  To post with this username, please enter a password below:")?><br>
				<div align="center"><form method="post" action="<?=$_SERVER['PHP_SELF']?>"><input type="password" name="passw">
				<input type="hidden" name="name" value="<?=htmlspecialchars($name ?? "", ENT_QUOTES)?>">
				<input type="hidden" name="email" value="<?=htmlspecialchars($email ?? "", ENT_QUOTES)?>">
				<input type="hidden" name="comment" value="<?=htmlspecialchars($comment ?? "", ENT_QUOTES)?>">
				<input type="hidden" name="a" value="<?=$a?>">
				<input type="hidden" name="post" value="comment">
				<input type="submit" value="<?=L("Submit")?>">
				</form></div><?php 
			} else {
				// Post comment 
				if(empty($name) || empty($email) || empty($comment)) { print L("Please fill-in all fields.  All fields are required to post a comment.") . "<p>";
				} else {
					$q['ins_com'] = mysql_query("INSERT INTO $t_coms (id, news_id, name, email, comment, date, ip) VALUES ('', '$a', '$name_esc', '$email_esc', '$comment_esc', '$now', '{$_SERVER['REMOTE_ADDR']}')", $link) or E("Couldn't insert new comment:<br>" . mysql_error());
					print L("Your comment has been added.") . "<br>[ <a href=\"{$_SERVER['PHP_SELF']}?a=$a\">&lt;&lt; " . L("Go Back") . "</a> ]<br><br>";
				}
			}
		} else {
			$passw_esc = mysql_real_escape_string($passw, $link);
			$q['userchk'] = mysql_query("SELECT * FROM $t_user WHERE user = '$name_esc' AND pass='$passw_esc' LIMIT 1", $link) or E("Couldn't select news article:<br>" . mysql_error());
			if(mysql_num_rows($q['userchk']) == "1") {
				// Post comment with protected username 
				$q['ins_com'] = mysql_query("INSERT INTO $t_coms (id, news_id, name, email, comment, date, ip) VALUES ('', '$a', '$name_esc', '$email_esc', '$comment_esc', '$now', '{$_SERVER['REMOTE_ADDR']}')", $link) or E("Couldn't insert new comment:<br>" . mysql_error());
				print L("You comment has been added.") . "<br>[ <a href=\"{$_SERVER['PHP_SELF']}?a=$a\">&lt;&lt; " . L("Go Back") . "</a> ]<br><br>";
			} else {
				print L("The password you entered was incorrect.") . "<br>[ <a href=\"javascript:history.go(-1)\">&lt;&lt; " . L("Go Back") . "</a> ]<br><br>";
			}
		}
		
		
	} else {
		$q['info'] = mysql_query("SELECT * FROM $t_news WHERE id = '$a' LIMIT 1", $link) or E("Couldn't select news article:<br>" . mysql_error());
		$newsnum = mysql_num_rows($q['info']);
	}
	
	// If search is performed [v1.12] 
} elseif(isset($s)) {
	$s_esc = mysql_real_escape_string($s, $link);
	$q['info'] = mysql_query("SELECT * FROM $t_news WHERE content LIKE '%$s_esc%' ORDER BY date DESC LIMIT $lims, $pgset", $link) or E("Couldn't search news articles:<br>" . mysql_error());
	$newsnum = mysql_num_rows($q['info']);
	
	// Retrieve all news items	
} else {
	
	$extra = "";
	if($c != "") { $extra = "WHERE cat = '" . (int)$c . "'"; }
	$q['cats'] = mysql_query("SELECT * FROM $t_cats ORDER BY name ASC", $link) or E("Couldn't select categories:<br>" . mysql_error());
	$catnum = mysql_num_rows($q['cats']);
	
	$q['info'] = mysql_query("SELECT * FROM $t_news $extra ORDER BY date DESC LIMIT $lims, $pgset", $link) or E("Couldn't select news:<br>" . mysql_error());
	$q['count'] = mysql_query("SELECT id FROM $t_news $extra", $link) or E("Couldn't count news:<br>" . mysql_error());
	$newsnum = mysql_num_rows($q['count']);
	
	if($catnum > "1" && $set['catbox'] == "on") { ?>
		<table width="90%" border="0" cellspacing="0" cellpadding="0"><tr>
		<td align="left"><b><?=L("View news posted in category:")?></b></td>
		<td align="right"><?php  print catBox("c","$c","yes","news"); ?></td>
		</tr></table><br>
		<?php 
	}
	
}

if($newsnum == "0") {
	print "<br>No news found in database<br><br>";
}

if(isset($q['info'])) { // $q['info'] check 
	
	// Begin output of news items 
	while ($r = mysql_fetch_array($q['info'], MYSQL_ASSOC)) {
		$auid = $r['author'];
		$output = $set['output'];
		$r['subject'] = "<a name=\"cn_{$r['id']}\"></a>{$r['subject']}";
		$serv_tzone = (date("Z")/3600);
		// Format date to current timezone, using format setting specified 
		$r['date'] = date("{$set['dateform']}", zonechange("$serv_tzone", "{$set['timezone']}", "{$r['date']}"));
		
		// Summarize story option [v1.12] 
		if($r['sumstory'] == "on" && isset($a)) { 
			$output = str_replace("{news}", "{$r['content']}<p>{$r['content2']}", $output);
		} elseif($r['sumstory'] == "on") {
			$r['content'] .= "<p><small><a href=\"" . $_SERVER['PHP_SELF'] . "?a={$r['id']}\">" . L("Read More...") . "</a></small>";
			$output = str_replace("{news}", "{$r['content']}", $output);
		} else {
			$output = str_replace("{news}", "{$r['content']}", $output);
		}
		$output = str_replace("\n", "<br>\n", $output);
		
		// Use Keywords and word filter if filter is turned "on" [v1.12] 
		if($set['words'] == "on") {
			$q['words'] = mysql_query("SELECT * FROM $t_words ORDER BY word ASC", $link) or E("Couldn't select keywords:<br>" . mysql_error());
			while ($w = mysql_fetch_array($q['words'], MYSQL_ASSOC)) {
				if($w['type'] == "link") {
					$w['replace'] = "<a href=\"".$w['replaced']."\" target=\"_blank\">".$w['word']."</a>";
				} elseif($w['type'] == "picture") {
					$w['replace'] = "<img src=\"".$w['replaced']."\" alt=\"".$w['word']."\">";
				}
				$output = str_replace($w['word'], $w['replaced'], $output);
			}
		}
		
		// Build user-defined source link [v1.12] 
		if(empty($r['source']) || empty($r['sourceurl'])) {
			$output = str_replace("{source}", "", $output);
		} else {
			if(empty($set['source'])) {
				$setsource = "<a href=\"{$r['sourceurl']}\" target=\"_blank\">{$r['source']}</a>";
				$output = str_replace("{source}", "$setsource", $output);
			} else {
				$setsource = str_replace("{sname}", "{$r['source']}", $set['source']);
				$setsource = str_replace("{surl}", "{$r['sourceurl']}", $setsource);
				$output = str_replace("{source}", "$setsource", $output);
			}
		}
		
		// Build user-defined author link [v1.12] 
		if(empty($set['author'])) {
			$setauthor = "<a href=\"mailto:" . preg_replace("/@/", " AT ", getinfo($r['author'],"email")) . "\">" . getinfo($r['author']) . "</a>";
			$output = str_replace("{author}", "$setauthor", $output);
		} else {
			$setauthor = str_replace("{aemail}", "" . preg_replace("/@/", " AT ", getinfo($r['author'],"email")) . "", $set['author']);
			$setauthor = str_replace("{aname}", "" . getinfo($r['author']) . "", $setauthor);
			$output = str_replace("{author}", "$setauthor", $output);
		}
		
		$output = str_replace("{subject}", "{$r['subject']}", $output);
		$output = str_replace("{date}", "{$r['date']}", $output);
		
		// View/Post Comments Link [v1.12] 
		if($set['comments'] == "on" && !isset($a)) {
			$q['comsn'] = mysql_query("SELECT id FROM $t_coms WHERE news_id = '{$r['id']}' ORDER BY date ASC", $link) or E("Couldn't select comments for current news article:<br>" . mysql_error());
			$comsnum = mysql_num_rows($q['comsn']);
			if(empty($set['coms_text'])) {
				$output = str_replace("{comments}", "<a href=\"" . $_SERVER['PHP_SELF'] . "?a={$r['id']}\"><small>" . L("View/Post Comments") . " ($comsnum)</small></a>", $output);
			} else {
				$setcoms_text = str_replace("{cnum}", "$comsnum", $set['coms_text']);
				$output = str_replace("{comments}", "<a href=\"" . $_SERVER['PHP_SELF'] . "?a={$r['id']}\"><small>$setcoms_text</small></a>", $output);
			}
		} elseif($set['comments'] == "on") {
			$output = str_replace("{comments}", "<small>[ <a href=\"" . $_SERVER['PHP_SELF'] . "\">&lt;&lt; " . L("Return to News Page") . "</a> ]</small>", $output);
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
	if(isset($a) && $set['comments'] == "on") {
		// Authenticated users with news/admin rights get a delete link
		// per comment (dpost.php, v1.14 feature)
		$com_admin = false;
		if(!empty($_COOKIE['cncook']) && strpos($_COOKIE['cncook'], ',') !== false) {
			list($cu, $cp) = explode(',', $_COOKIE['cncook'], 2);
			$q['adm'] = mysql_query("SELECT news, admin FROM $t_user WHERE user='" . mysql_real_escape_string($cu, $link) . "' AND pass='" . mysql_real_escape_string($cp, $link) . "' LIMIT 1", $link);
			if($q['adm'] && mysql_num_rows($q['adm']) == 1) {
				$admrow = mysql_fetch_array($q['adm'], MYSQL_ASSOC);
				$com_admin = ($admrow['news'] == "on" || $admrow['admin'] == "on");
			}
		}
		$q['coms'] = mysql_query("SELECT * FROM $t_coms WHERE news_id = '$a' ORDER BY date ASC", $link) or E("Couldn't select comments for current news article:<br>" . mysql_error());
		$comsnum = mysql_num_rows($q['coms']);
		if($comsnum != "0") {
			?>
			<hr size="1" width="95%">
			<b><?=L("Comments")?>: (<?=$comsnum?> <?=L("total")?>)</b><br>
			<?php 
			while ($c = mysql_fetch_array($q['coms'], MYSQL_ASSOC)) {
				$c_name = htmlspecialchars($c['name'], ENT_QUOTES);
				$c_email = htmlspecialchars($c['email'], ENT_QUOTES);
				$c_comment = htmlspecialchars($c['comment'], ENT_QUOTES);
				print "
				<br>\n
				<b>" . L("Name") . ":</b> {$c_name}<br>\n
				<b>" . L("Email") . ":</b> {$c_email}<br>\n
				<b>" . L("Comments") . ":</b> {$c_comment}<br>\n
				" . ($com_admin ? "<small>[ <a href=\"dpost.php?a={$c['id']}&n=$a\">" . L("delete") . "</a> ]</small><br>\n" : "") . "
				<br>\n
				";
			}
		}
		?>
		<hr size="1" width="95%">
		<b><?=L("Post a Comment")?></b><br><br>
		<table border="0" cellpadding="1" cellspacing="0">
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<tr><td><b><?=L("Name")?>:</b></td><td><input type="text" name="name"></td></tr>
		<tr><td><b><?=L("Email")?>:</b></td><td><input type="text" name="email"></td></tr>
		<tr><td><b><?=L("Comment")?>:</b></td><td><textarea rows="4" cols="30" name="comment"></textarea></td></tr>
		<tr><td>&nbsp;</td><td><input type="submit" value="<?=L("Add Comment")?>"></td></tr>
		<input type="hidden" name="a" value="<?=$a?>">
		<input type="hidden" name="post" value="comment">
		</form>
		</table>
		<?php 
	}
	?>
	<table width="95%" border="0" cellspacing="0" cellpadding="0" align="center"><tr>
	<?php  
	if($set['search'] == "on" && !isset($a)) { ?>
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<td align="left">
		</td><?php  
	} ?>
	<td align="center">
	<?php 
	$extras = "";
if($c != "") { $extras .= "&c=$c"; }
	
	### Page numbering code 
	$totalpages = 0;
	if($newsnum != "1" && $set['pages'] == "on") {
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
					print "<b><a href=\"{$_SERVER['PHP_SELF']}?pg=$loop$extras\">";
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
	
} // End $q['info'] check 
?>
</td>

</tr></table>
<br>
