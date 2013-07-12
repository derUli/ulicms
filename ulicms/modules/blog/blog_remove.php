<?php
function blog_remove_post($post_id){
     if($_SESSION["group"] >= 20){
         db_query("DELETE FROM `" . tbname("blog") . "` WHERE id = $post_id");
         return "<p>Der Blogpost wurde erfolgreich gelöscht!</p>";
         }else{
         return "<p>Zugriff verweigert!</p>";
         }
     }


function blog_remove_comment($post_id){
     if($_SESSION["group"] >= 40){
         db_query("DELETE FROM `" . tbname("blog_comments") . "` WHERE id = $post_id");
         return "<script type='text/javascript'>
	   history.back();
	   </script>";
         }else{
         return "<p>Zugriff verweigert</p>";
         }
     }
?>