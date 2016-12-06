-------------------------------------------------------------------
					CPSC 471 - Database Project
							Group 11
						Patrick Settle
						Joshua Walters
						Andrew Schneider
-------------------------------------------------------------------
Installation Guide:

1. The website requires a MySQL server and an apache PHP server, with PHP version 5.6
   Note: We recommend the bitnami wampstack: https://bitnami.com/stack/wamp
2. Extract the zip so that the folder named "drupal" is at the root document level of the webserver
   For example, on xampp, the structure should be
   xampp
   	->htdocs
   		->drupal
3. Create a new database named "drupal" in the MySQL server, through phpmyadmin for example, or another editor
	-> select collation "utf8_general_ci"
4. Navigate to localhost/drupal/install.php, and follow the on screen instructions as follows:
	4.1 Select "Standard" for installation profile, click "Save and continue"
	4.2 Select English for language, click "Save and continue"
	4.3 Select "MySQL, MariaDB, or equivalent"
		Input "drupal" for database name, along with the database credentials for the MySQL server in the fields below
		Open the tab of "Advanced options"
		Put 127.0.0.1 for database host (Or another remote ip if running a remote database)
		Click "Save and continue"
	4.4 Input a valid email address for "Site e-mail address", Note: it doesn't have to be your email, we recommend "a@a.a"
		Create a username for the root account, Note: We recommend "root"
		Input another valid email address for the account, again we recommend "a@a.a"
		Input a password for the root account twice, we recommend "abc123"
		Click "Save and continue"
	4.5 Click the link "Visit your new site"
		You should be logged in as the root account, and see a black menu bar at the top of the screen,
		if not, you can log in via the login menu on the left hand side of the screen, use the username and password set
		in step 4.4
5. On the black menu bar at the top of the screen, click "Modules"
	5.1 Search for "Date API" and "Date Popup" in the module list, and click the checkbox associated
	5.2 Click "Save configuration" at the bottom of the screen
	5.3 Search for and find cpsc471.prj from in the module list, again, click the checkbox associated
	5.4 Click "Save configuration" at t he bototm of the screen
6. On the black menu bar at the top of the screen click "Structure", then "Blocks"
	6.1 At the bottom of the page, there is a list of disabled blocks,
		In the select to the right of "Scotch Creek Cottages" select "Sidebar First"
		In the select to the right of "User Menu" select "Sidebar First"
		Click "Save Blocks" at the bottom of the page
7. Click on the "House Icon" at the top left corner of the screen, the project is now installed

-------------------------------------------------------------------

Accessing the application

To access the application, navigate to localhost/drupal,
if installed correctly, there is a menu on the left hand side of the screen titled "Scotch Creek Cottages"
that has links to the different functionalities of the website


-------------------------------------------------------------------

Test Users Availible

Root Account - Has access to everything
	username: <Set during install, we recommended "root">
	password: <Set during install, we recommended "abc123">

Manager Account - Has access to manager side of the application
	username: "manager"
	password: "manager"


Renter Accounts - Have access to renter side of the application
	username: "xXTheWalDoXx"
	password: "password"
	
	username: "xXTheRustlerXx"
	password: "password"
	
	username: "xXJohnSlayerXx"
	password: "password"
	
Note: New rental accounts can be made by navigating to localhost/drupal/scc/ while logged out of the application



