<?php 
$allowed_tags = getconfig("allowed_tags");

if(strpos($allowed_tags, "<p>") === false)
   setconfig("allowed_tags", "<p>".$allowed_tags);

?>