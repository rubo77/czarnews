##########################################
### 
### CzarNews v1.12
### Made by: Czaries  [czaries@czaries.net]
### http://www.czaries.net/scripts/
### for more scripts and updates.
###
##########################################

# Installation/Upgrade
# -----------------------------------------------------------------

1.   Upload all the files in the ZIP file to your website in the directory of your
	choice (i.e. http://www.yoursite.com/czarnews)
2.	Open up that directory in your browser window.
3.	You should be taken to an installation page.  If you for some reason are
	not, type in www.yoursite.com/czarnews/install.php
4.  If this is your first time installing the script, or if you would like to start 'fresh'
	again (delete all existing data), choose 'Full Install', otherwise choose 'Upgrade'.
5.	Follow the instructions of the page, making sure you fill out EVERYTHING
	that is asked for, and click the Install button.  If any errors come up, try
	re-typing your MySQL username and password.  If you still get errors,
	CHOMD your directory 777 and try again.
6.	After the install, you will be brought to a configuration page.  Make sure all
	the variables are correct, and save the settings.

### INCLUDING THE FILE ###

7.  To get the proper PHP code for including the file, you can log-in to the script
	admin panel, and click "Code Generator" (only "Ultimate Admin" users have access).
	Select the appropriate options, click generate code, and copy and paste the code
	into your own php file.
	
	--- OR, you can do it manually with the followings steps: ---
	
8.	On the page you wish to show your news, you must INCLUDE the file
	'news.php' via PHP's include() function.  The page you include the news
	file on must be a PHP page (.php, .php3, .php4, .phtml), NOT an HTML page (.html)
	You must also provide the root path to the directory the script is in, so it can
	include other files that it needs to such as the configuration file.

	The PHP code for including news would be:
	<?
	$tpath = "/home/user/public_html/path/to/czarnews/";
	include($tpath . "news.php");
	?>

	YOU MUST DECLARE THE '$tpath' VARIABLE!  The script 'news.php' uses this
	variable for other functions!  Omitting it will result in an error!
	Notice the starting and trailing slashes for the '$tpath' variable.  This variable
	should be the full root path to the directory you installed CzarNews in.
	(If you don't know your ROOT path, contact your hosting provider, or
	make a PHP file with the line: <? print $DOCUMENT_ROOT; ?>)  If you
	need additional help on the include() function, check PHP's website
	at http://www.php.net.
	---
	
9. 	You are now ready to post news.  Open up that directory in your browser
	window, login if necessary (the install should have logged you in), and
	post news.  Your news should show up on the page you included news.php
	on.



# Troubleshooting
# -----------------------------------------------------------------

There is now a 'troubleshooting' installation mode that will help guide you through
parts of the installation if you run into problems.  If your MySQL tables did not get
setup correctly, or if your database definitions file did not get created, or something
else goes wrong during installation, please run the troubleshooting installation and
see if you can fix the problem manually.

Additionally, if you have problems, try using the 'czarnews.sql' file that was included.

If you run into some sort of problem, please post it on the FORUMS under Scripts
and Troubleshooting.

Scripts Forums: http://forum.czaries.net

If that fails, you may email me at: czaries@czaries.net



# License/Distribution
# -----------------------------------------------------------------

This script is distributed for free by www.czaries.net, or my personal collection
of websites only.  It is intended for use on websites of all types, personal and
commercial, as long as the credit link remains intact and visible wherever the
news is displayed.  This script was made in 2003 by Vance Lucas.  If you
choose to modify your copy of the script, you have the right to do so freely.
You may submit your modifications to me, but please do not re-distribute the
script with your modifications, as it is not the original work.


# Changelog
# -----------------------------------------------------------------

v1.12	- March 7, 2004
			
			New Features:
			- Added 'All Categories' news display selection for category box
			- Added news search feature with highlighting of searched word
			- Added Comments with current username protection (somone cannot post a comment under a CzarNews user's name w/o their password)
			- Added 'cn_info.php' file to explain a little about the script and myself
			- Added 'cn_generate.php' file to generate PHP code that you can use to include the file 'news.php'
			- Added ability to create news summaries and full stories
			- Added ability to move and delete multiple news items at once
			- Added ability to edit HTML for 'source' and 'author' links
			- Added warning for having 'install.php' on server after script is installed

			Bug Fixes/Improvements:
			- Optimized several lines of code for faster execution speed
			- Fixed bug with category display box (would not display if user was not logged in)
			- Fixed security bug that allowed users to delete news items outside their category permissions
			- Fixed news setting to allow user permission: 'allow user to edit/delete only posts that they themselves posted'

---------------------------------------------
v1.01	- December 16, 2003

			New Features:
			- 'Forgot Password' mailer
			- Single article link (can view single news item with id)
			
			Bug Fixes:
			- News numbering problem in news admin page
			- Modified default selections when adding new user to make it easier
			- User listing fixes when only one user exists
			- Added selection box for viewing and displaying news in different
			  categories when more than one category exists
			- Source URL would not display properly when used in a news post
			- Updated some functions in 'cn_config.php' to increase excecution speed
			- Minor modifications to install script
			- Provided SQL file of table structure incase install script failed (czarnews.sql)

---------------------------------------------
v1.00	- July 30, 2003
			
			(BETA) First initial public release.  Released under BETA becuase the script
			had never been tested on a public scale