<?php
### dpost.php [v2.0]
### Deletes a single comment. Only callable by a logged-in admin.
### 'a' is the comment id, 'n' the news id used for the redirect back.

include("cn_auth.php");
include("cn_head.php");

if(!isset($useri) || ($useri['news'] != "on" && $useri['admin'] != "on")) {
print E(L("You are not allowed to delete comments"));
exit;
}

$com_id = isset($a) ? (int)$a : 0;
$news_id = isset($n) ? (int)$n : 0;

if($com_id <= 0) {
print E(L("No comment selected"));
} else {
$q['del'] = mysql_query("DELETE FROM $t_coms WHERE id = '$com_id'", $link) or E("Couldn't delete comment:<br>" . mysql_error());
print S(L("Comment has been deleted"));
print "<br>[ <a href=\"news.php?a=$news_id\">&lt;&lt; " . L("Back to the news item") . "</a> ]";
}

include("cn_foot.php");
?>
