<?php 
include_once "init.php";
$pages = getAllSystemnames();
header("Content-type: text/plain; charset=utf-8");
foreach($pages as $page){
   echo "/?seite=$page => /$page.html";
   echo "\r\n";
   echo "/index.php?seite=$page => /$page.html";
   echo "\r\n";
   echo "/index.php?seite=$page&zoom=90 => /$page.html?zoom=90";
   echo "\r\n";
   echo "/index.php?seite=$page&zoom=95 => /$page.html?zoom=95";
   echo "\r\n";
   echo "/index.php?seite=$page&zoom=100 => /$page.html?zoom=100";
   echo "\r\n";
   echo "/index.php?seite=$page&css=color => /$page.html?css=color";
   echo "\r\n";
   echo "/index.php?seite=$page&css=black => /$page.html?css=black";
   echo "\r\n";
   
   echo "/?seite=$page => /$page.html";
   echo "\r\n";
   echo "/?seite=$page&zoom=90 => /$page.html?zoom=90";
   echo "\r\n";
   echo "/?seite=$page&zoom=95 => /$page.html?zoom=95";
   echo "\r\n";
   echo "/?seite=$page&zoom=100 => /$page.html?zoom=100";
   echo "\r\n";
   echo "/?seite=$page&css=color => /$page.html?css=color";
   echo "\r\n";
   echo "/?seite=$page&css=black => /$page.html?css=black";
   echo "\r\n";
}