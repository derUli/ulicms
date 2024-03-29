<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

add_translation('INSTALLATION', 'Installation');
add_translation('TITLE_STEP_1', 'Select Language');
add_translation('TITLE_STEP_2', 'Accept License Conditions');
add_translation('TITLE_STEP_3', 'System Requirements Check');
add_translation('TITLE_STEP_4', 'Change File Permissions');
add_translation('TITLE_STEP_5', 'Connect to Database');
add_translation('TITLE_STEP_6', 'Create Administrator Account');
add_translation('TITLE_STEP_7', 'Select optional Components');
add_translation('TITLE_STEP_8', 'Build Database');
add_translation('TITLE_STEP_9', 'Create Config File');
add_translation('TITLE_STEP_10', 'Remove Installer Folder');
add_translation('SELECT_LANGUAGE', 'Please select your language.');
add_translation('FOLLOW_INSTRUCTIONS', 'Please follow instructions, to install UliCMS on this Server.');
add_translation('CHMOD', '<p>Please chmod the following files to 0755.<br/>
<ol>
<li>The main directory of UliCMS (recursive, including all files and subfolders)</li>
</ol>
</p>');
add_translation('PERMISSION', 'Please set the file permissions like in this screenshot');
add_translation('PERMISSIONS2', 'FTP Permission changes');
add_translation('MYSQL_HOST', 'MySQL Host');
add_translation('MYSQL_USER', 'MySQL User');
add_translation('MYSQL_PASSWORD', 'MySQL Password');
add_translation('MYSQL_DATABASE', 'MySQL Database');
add_translation('CONNECT', 'Connect');
add_translation('DB_CONNECTION_FAILED', "Can't connect to database server.");
add_translation('CANT_OPEN_SCHEMA', "Can't open database. No such scheme exists");
add_translation('ADMIN_USER', 'Admin Username');
add_translation('ADMIN_PASSWORD', 'Admin Password');
add_translation('ADMIN_PASSWORD_REPEAT', 'Admin Password (repeat)');
add_translation('ADMIN_FIRSTNAME', 'Firstname');
add_translation('ADMIN_LASTNAME', 'Lastname');
add_translation('APPLY', 'Apply');
add_translation('ADMIN_EMAIL', 'E-Mail Address');
add_translation('MYSQL_PREFIX', 'MySQL Prefix');
add_translation('INSTALL_DEMO_DATA', 'Install additional Demo Data');
add_translation('INSTALL_X_OF_Y', 'Install File %x% of %y%');
add_translation('BUILD_DATABASE', 'Build Database');
add_translation('CREATE_CMS_CONFIG_PHP', 'Create .env');
add_translation('WRITE_CMS_CONFIG_FAILED', 'Writing .env failed.<br/>' . 'Please create .env manually and insert the following content:');
add_translation('LAST_STEP', 'This is the last step of the installation.<br/>'
        . 'Now, please delete the "installer" Folder from your web server.<br/>'
        . 'Then you can login to UliCMS.');
add_translation('goto_login', 'Goto Login');

add_translation('ADD_FK', 'Add Foreign Keys');
add_translation('ACCEPT_LICNSE', 'Accept License');
add_translation('NEXT', 'Next');
add_translation('THIS_PROCEDUDRE_WILL_TAKE_SOME_MINUTES', 'This procedure will take some minutes.');

add_translation('TRANSLATION_PHP_VERSION', 'PHP >=');
add_translation('TRANSLATION_IS_WRITABLE', 'ULICMS_ROOT is writable');
add_translation('TRANSLATION_PHP_MODULE', 'PHP Modul');
