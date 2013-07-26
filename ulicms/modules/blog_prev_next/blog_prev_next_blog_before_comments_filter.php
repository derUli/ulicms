<?php
function blog_prev_next_blog_before_comments_filter($html){
     include_once getModulePath("blog_prev_next") . "blog_prev_next_main.php";
     return $html . blog_prev_next_render();
    }
?>