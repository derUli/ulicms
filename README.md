# UliCMS
UliCMS is an enhanced web content management solution.
It runs on an LAMP Stack and includes an MVC inspired development framework.

## Requirements to run UliCMS
   * Apache Webserver (other webservers may work but are not officially supported)
   * PHP 5.6 or PHP 7.x
   * MySQL 5.5.3 or newer / MariaDB
   * For secure access to UliCMS services (for example package source) you will also need the root certificate of Let's encrypt on your server (in Debian Linux contained in the ca-certificates package).
   
#### PHP Modules
UliCMS requires some special modules for PHP
   * mysqli
   * gd
   * json
   * mbstring
   * openssl   
   * dom
   * xml

For development you require the "composer" package manager.
You have to run "composer install" to install dependencies.

UliCMS should work on common shared webhosting environment.

## Extras

The `extras` folder contains additional content such as

* Configuration examples for other http servers.
Please note that these http servers are not officially supported by the UliCMS project.
* Advertising materials such as logos, texts and banners
* scripts that may be useful for some users

If you have any questions or need further information see "doc" folder or go to https://en.ulicms.de
