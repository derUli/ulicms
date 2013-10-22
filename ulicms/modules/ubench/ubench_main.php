<?php 
function ubench_render(){
   return "<p>ubench Example Usage:</p>".highlight_file ( getModulePath("ubench")."example.txt", true);
}
?>