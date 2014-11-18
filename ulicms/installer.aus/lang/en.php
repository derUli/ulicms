<?php 
define("TRANSLATION_TITLE", "UliCMS Installation");
define("TRANSLATION_WELCOME", "Welcome");
define("TRANSLATION_WELCOME2", "Welcome to the installation of UliCMS.");
define("TRANSLATION_BETA_VERSION", "This is a preview release of UliCMS.<br/>
This means, that this is an unfinished version of this software.<br/>
Please don't use this in an production enviroment.");
define("TRANSLATION_FOLLOW_INSTRUCTIONS", "Please follow instructions, to install UliCMS on this Server.");
define("TRANSLATION_CHMOD", "<p>Please chmod the following files to 0755.<br/>
<ol>
<li>The main directory of UliCMS (excluding subdirectories)</li>
<li>templates/ (recursive)</li>
<li>content/ (recursive)</li>
<li>modules/ (recursive)</li>
</ol>
</p>");
define("TRANSLATION_PERMISSION", "Please set the file permissions like in this screenshot");
define("TRANSLATION_PERMISSIONS2", "FTP Permission changes");
define("TRANSLATION_GD_MISSING", "<strong>php5-gd</strong> is not installed. php5-gd is required to use the media manager.");
define("TRANSLATION_MYSQLI_MISSING", "<strong>php5-mysqli</strong> is missing. You can't continue without this PHP extension.");
define("TRANSLATION_JSON_MISSING", "<strong>php5-json</strong> is missing. You can't continue without this PHP extension.");
define("TRANSLATION_NEXT", "Next");
define("TRANSLATION_MYSQL_LOGIN", "MySQL Login informations");
define("TRANSLATION_MYSQL_LOGIN_HELP", "Please fill in login data of your MySQL Database Server");

define("TRANSLATION_SERVERNAME", "Server Name");
define("TRANSLATION_LOGINNAME", "User Name");
define("TRANSLATION_PASSWORD", "Password");
define("TRANSLATION_DATABASE_NAME", "Database Name");
define("TRANSLATION_PREFIX", "Prefix");
define("TRANSLATION_DB_CONNECTION_FAILED", "Can't connect to database server.");
define("TRANSLATION_CANT_OPEN_SCHEMA", "Can't open database. No such scheme exists");
define("TRANSLATION_HOMEPAGE_SETTINGS", "Homepage Settings");
define("TRANSLATION_HOMEPAGE_TITLE", "Homepage Titel");
define("TRANSLATION_SITE_SLOGAN", "Motto");
define("TRANSLATION_YOUR_FIRSTNAME", "Your Firstname");
define("TRANSLATION_YOUR_LASTNAME", "Your Lastname");
define("TRANSLATION_YOUR_EMAIL_ADRESS", "Your E-Mail Adress");
define("TRANSLATION_ADMIN_NAME", "Name of Admin Account");
define("TRANSLATION_ADMIN_PASSWORD", "Password of Admin Account");
define("TRANSLATION_DO_INSTALL", "Install");
define("TRANSLATION_INSTALLATION_FINISHED", "Installation finished.");
define("TRANSLATION_FIRST_LOGIN_HELP", 'The Installation of UliCMS is finished.<br/>Please delete "installer" directory for security reasons. Then you can login <a href="../admin/">here</a>.');
define("TRANSLATION_LOGIN_DATA_SENT_BY_MAIL", "The Login informations was sent by mail");
define("TRANSLATION_LOGIN_DATA_NOT_SENT_BY_MAIL", "Can't send login informations due to non working mail() Function.");
define("TRANSLATION_MAIL_MESSAGE_TITLE", "UliCMS Installation at %domain%");

define("TRANSLATION_MAIL_MESSAGE_TEXT", "Hello %person_name%!\n" .
         "UliCMS was successfully installed on %domain%.\n\n" .
         "Here are the login informations:\n" .
         "Username: %username%\n" .
         "Password: %password%\n\n" .
         "You can find the administration area by appending /admin after the last slash.");
         
         
define("TRANSLATION_SUCCESSFULL_DB_CONNECT", "Successfull connected to database.");