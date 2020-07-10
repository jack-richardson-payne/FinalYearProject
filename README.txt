File list:
Front-End:
-Project code folder : contains all code for the front end of the web app.
	-footer.php : file containing code to implement a uniform footer on each web page.
	-header.php : file containing code to implement a uniform header on each web page.
	-index.php : landing/home page of the web app.
	-match.php : page to display a particular matches information.
	-myMatches.php : page displaying all matches a user has logged.
	-myStats.php : page displaying stats for matches the user has logged.
	-profile.php : page displaying user profile content e.g. password changing and following other users.
	-reset.php : page to reset a particular users password.
	-searchResult.php : page to display the outcome of a user searching for matches. 
	-signup.php : page to sign a new user up to the app. 
	-pictures folder : contains all images used.
	-style folder: containes file for the stylesheet of web app.
	-includes folder : any other non-page php files for functionality of app.
		-change.php : code used to change the settings of the stats page e.g. team, time period or user. 
		-changePass.php : code to allow user to change thier password.
		-changeTeam.php : code to allow user to chnage thier favourite team.
		-dbh.php : database handler, allows a connection to the database.
		-delete.php : removes a match from the users logged matches.
		-fav.php : allows users to favourite or unfavourite a match.
		-follow.php : allows user to follow another user.
		-getStats.php : code run when user enters stats page to gather all stats and set to values. 
		-insert.php : code to log a user selected match. 
		-login.php : code to log a user in and initialise session.
		-logout.php : code to log a user out. 
		-resetPass.php : code to reset the users password. 
		-sign-up.php : code to sign a new user up with enterred credentials. 
		-undo.php : code to undo last delete of users matches. 
		-unfollow.php : code to allow a user to unfollow another. 
Back-End:
-compproj.sql : sql script which when imported/run in xampp will create the relevant tables and add necissary data for the projects back end (NOTE this will not create a database, one should be created then this imported into it) 
-compproj folder : contains all the raw mysql files for backend, taken from XAMPP's folder '..\xampp\mysql\data'.
-data.csv : file containing all the raw match data (compproj.sql will import this data automatically if run). 

Guidance for hosting/deployment via XAMPP: 
-Once XAMPP installed and setup, place all front-end project code within the newly created 'htdocs' folder in the set of XAMPP folders. 
-Start the XAMPP control panel and start both 'Apache' and 'MySQL'.
-Using a browser navigate to http://localhost/phpmyadmin/ . Here you can create a new sql database and import the compproj.sql file. (NOTE that you will need to configure the 'dbh.php' file to match the settings of your XAMPP and database) 
-Once all this is done the site should be hosted locally on http://localhost/Project%20code/