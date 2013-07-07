<?php 
include_once getModulePath("fortune")."fortune_lib.php";

function fortune_render(){
  $fortune = getRandomFortune();
  return nl2br($fortune);
}
?>