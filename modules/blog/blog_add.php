<?php 
function blog_add_form(){
    $html_output = "";
    $all_languages = getAllLanguages();
    $html_output .= "<form action='?seite=".get_requested_pagename()."&blog_admin=submit' method='post' style=''>";
    
    $html_output .= "<table class='blog_admin_table' style='width:90%;border:0px;'7>";
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>Titel dieser News:</strong>";
    $html_output .= "</td>";
    $html_output .= "<td>";
    $html_output .= "<input name='title' type='text' maxlength=200 size=70>";
    $html_output .= "</td>";
    $html_output .= "</tr>";
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>SEO-URL:</strong>";
    $html_output .= "</td>";
    $html_output .= "<td>";
    $html_output .= "http://".$_SERVER["SERVER_NAME"].
    "/pfad/zu/ulicms/".
    "?seite=".get_requested_pagename().
    "&single=<input style='font-weight:bold;background-color:rgb(240, 240, 240);border:0px;' name='seo_shortname' type='text' maxlength=200 size=50>";
    $html_output .= "</td>";
    $html_output .= "</tr>";
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>Sprache des Blogeintrags:</strong>";
    $html_output .= "</td>";
    
    $html_output .= "<td>";
    $html_output .= "<select name='language'>";
    for($i=0; $i<count($all_languages); $i++){
       $this_language = $all_languages[0];
       $html_output .= "<option value='$this_language'>$this_language</option>";
           
    }

    $html_output .= "</select>";
    
    $html_output .= "</td>";
    $html_output .= "</tr>";
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>Kommentare aktiviert:</strong>";
    $html_output .= "</td>";
    $html_output .= "<td>";
    $html_output .= "
    <select name='comments_enabled'>
    <option value='1'>Ja</option>
    <option value='0'>Nein</option>
    ";
    $html_output .= "</td>";
    $html_output .= "</tr>";    
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "</td>";
    $html_output .= "<td>";
    $html_output .= "<br/><input type='submit' value='Speichern'>";
    $html_output .= "</td>";
    $html_output .= "</tr>";
    
    
    

    
    $html_output .= "</table>";
    
    $html_output .= "</form>";
    
    $html_output .= "
    <style type='text/css'>
    .blog_admin_table tr td{
    height:50px;
    }</style>";
    
    
    return $html_output;
}


?>