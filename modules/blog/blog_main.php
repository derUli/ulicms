<?php

function blog_render(){

     // Prüfen, ob Blog schon installiert
     blog_check_installation();


    if(isset($_GET["single"])){
    
       return blog_single(intval($_GET["single"]));
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