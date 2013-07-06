<?php

// Ersetzt jedes Vorkommen des Wortes Google mit dem bunten Google Logo
function senseless_superbunt_content_filter($content){

      $content = str_replace(" Google "," <strong style=\"color: rgb(0, 0, 255);\">G</strong><strong style=\"color: rgb(255, 0, 0);\">o</strong><strong style=\"color: rgb(255, 255, 77);\">o</strong><strong style=\"color: rgb(0, 0, 255);\">g</strong><strong style=\"color: rgb(0, 128, 0);\">l</strong><strong style=\"color: rgb(255, 0, 0);\">e</strong> ", $content);
      
   return $content;
}
?>