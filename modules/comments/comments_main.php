<?php 
function comments_render(){
    ob_get_clean();
    if(function_exists("blog"))
       blog();
    return ob_clean();
}
?>