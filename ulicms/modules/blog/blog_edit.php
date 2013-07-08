<?php 
function blog_edit_form($post_id){
    $query = db_query("SELECT * FROM `".tbname("blog")."` WHERE id = $post_id");
	if(mysql_num_rows($query) == 0){
	   return "<p>Dieser Blogpost ist nicht mehr vorhanden</p>";
	}else{
	   $post = mysql_fetch_object($query);
	
    $html_output = "";
    $all_languages = getAllLanguages();
    $html_output .= "<form action='".get_requested_pagename().".html?blog_admin=update' method='post' style=''>";
    
    $html_output .= "<table class='blog_admin_table'>";
    
    $html_output .= "<tr>";
    $html_output .= "<td style='width:220px;'>";
    $html_output .= "<strong>Titel dieser News:</strong>";
    $html_output .= "</td>";
    $html_output .= "<td>";
    $html_output .= "<input name='title' type='text' maxlength=200 size=70 value='".
	htmlspecialchars($post->title, ENT_QUOTES, "UTF-8")."'>";
    $html_output .= "</td>";
    $html_output .= "</tr>";
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>SEO-URL:</strong>";
    $html_output .= "</td>";
    $html_output .= "<td>";
    $html_output .= "http://".$_SERVER["SERVER_NAME"].
    "/pfad/zu/ulicms/".get_requested_pagename().
    ".html?single=<input style='font-weight:bold;background-color:rgb(240, 240, 240);border:0px;' name='seo_shortname' type='text' maxlength=200 size=50 value='".
	htmlspecialchars($post->seo_shortname)."'>";
    $html_output .= "</td>";
    $html_output .= "</tr>";
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>Sprache des Blogeintrags: </strong>";
    $html_output .= "</td>";
    
    $html_output .= "<td>";
    $html_output .= "<select name='language'>";
    for($i=0; $i<count($all_languages); $i++){
       $this_language = $all_languages[$i];
	   if($post->language == $this_language){
	      $html_output .= "<option value='$this_language' selected>$this_language</option>";
	   } else
	   {
       $html_output .= "<option value='$this_language'>$this_language</option>";
       }    
    }

    $html_output .= "</select>";
    
    $html_output .= "</td>";
    $html_output .= "</tr>";
    
    
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>Blogeintrag aktiviert:</strong>";
    $html_output .= "</td>";
    $html_output .= "<td>";
	
	if($post->entry_enabled){
       $html_output .= "
    <select name='entry_enabled'>
    <option value='1' selected>Ja</option>
    <option value='0'>Nein</option>
    ";
	}else{
	       $html_output .= "
    <select name='entry_enabled'>
    <option value='1'>Ja</option>
    <option value='0' selected>Nein</option>
    ";
	
	}
    $html_output .= "</td>";
    $html_output .= "</tr>";   
    
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>Kommentare aktiviert:</strong>";
    $html_output .= "</td>";
    $html_output .= "<td>";
	
	if($post->comments_enabled){
    $html_output .= "
    <select name='comments_enabled'>
    <option value='1' selected>Ja</option>
    <option value='0'>Nein</option>
    ";
	}
	else{
	   $html_output .= "
    <select name='comments_enabled'>
    <option value='1'>Ja</option>
    <option value='0' selected>Nein</option>
    ";
	}
    $html_output .= "</td>";
    $html_output .= "</tr>";    
   
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>Datum:</strong>";
    $html_output .= "</td>";   
    
    $html_output .= "<td>";
    $html_output .= "<strong><input name=\"datum\" type=\"datetime-local\" value=\"".date("Y-m-d\TH:i:s", $post->datum)."\" step=any></strong>";
    $html_output .= "</td>";   
      
    $html_output .= "</tr>";
    
   
    $html_output .= "<tr>";
    $html_output .= "<td></td>";
    $html_output .= "<td align='center'><strong>Inhalt:</strong></td>";
    $html_output .= "</tr>";
    
    

    $html_output .= "<tr>";
    $html_output .= "<td></td>";
        
    $html_output .= "<td align='center'>";    
    
    $html_output .= '<textarea name="content_full" id="content_full" cols=60 rows=20>'.
	htmlspecialchars($post->content_full).'</textarea>
<script type="text/javascript">
var editor = CKEDITOR.replace( \'content_full\',
					{
						skin : \'kama\'
					});
					
</script>

<noscript>
<p style="color:red;">Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a></p>

</noscript>';


    $html_output .= "</td>";    
    $html_output .= "</tr>";






    $html_output .= "<tr>";
    $html_output .= "<td></td>";
    $html_output .= "<td align='center' style='width:98%;'><strong>Vorschau:</strong></td>";
    $html_output .= "</tr>";



    $html_output .= "<tr>";
    $html_output .= "<td></td>";
        
    $html_output .= "<td style='width:98%;' align='center'>";    
    
    $html_output .= '<textarea name="content_preview" id="content_preview" cols=80 rows=20>'.
	htmlspecialchars($post->content_preview)
	.'</textarea>
<script type="text/javascript">
var editor = CKEDITOR.replace( \'content_preview\',
					{
						skin : \'kama\'
					});
					
</script>

<noscript>
<p style="color:red;">Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a></p>

</noscript>';


    $html_output .= "</td>";    
    
    $html_output .= "</tr>";

    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "</td>";
    $html_output .= "<td align='center'>";
    
    $html_output .= "<input type='checkbox' name='spellcheck' value='spellcheck' checked/> Häufige Rechtschreibfehler korrigieren";
    $html_output .= "</td>";
    $html_output .= "</tr>";


    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "</td>";
    $html_output .= "<td align='center' style='padding-top:50px;'>";
    $html_output .= "<input type='submit' value='Speichern'>";
    $html_output .= "</td>";
    $html_output .= "</tr>";
    
    







    
    $html_output .= "</table>";
    $html_output .="<input type='hidden' name='id' value='".$post->id."'>";
    $html_output .= "</form>";
    
    $html_output .= "
    <style type='text/css'>
    .blog_admin_table tr td{
    height:50px; 
    }
    

    .blog_admin_table input[type=button], .blog_admin_table input[type=submit]{
    background-color:white;
    color:black;
    width:200px;
    font-size:16pt;
    font-weight:bold;
    border:3px solid blue; }
    
    .blog_admin_table{
      border:0px;    
    }


   

        
    </style>";
    
    
    return $html_output;
}

}


?>