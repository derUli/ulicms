<?php 
function blog_remove_post($post_id){
    if($_SESSION["group"] >= 20) {
	   mysql_query("DELETE FROM `".tbname("blog")."` WHERE id = $post_id");
	   return "<p>Der Blogpost wurde erfolgreich gelöscht</p>";
	} else{
	   return "<p>Zugriff verweigert</p>";
	}
}
?>