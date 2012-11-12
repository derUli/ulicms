<?php

function blog_render(){

     // Prüfen, ob Blog schon installiert
     blog_check_installation();


    if(!empty($_GET["single"])){
       require_once getModulePath("blog")."blog_single.php";
       return blog_single(mysql_real_escape_string($_GET["single"]));
    }
    else if(!empty($_GET["blog_admin"])){
        
    } 
    else{
       require_once getModulePath("blog")."blog_list.php";
       return blog_list();
    }

}







function blog_check_installation(){
	$test = mysql_query("SELECT * FROM ".tbname("blog"));
	if(!$test){
  	require_once getModulePath("blog")."blog_install.php";
  	blog_do_install();		
	}	
}







?>