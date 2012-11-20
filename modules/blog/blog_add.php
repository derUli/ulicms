<?php 
function blog_add_form(){
    $html_output = "";
    $all_languages = getAllLanguages();
    $html_output .= "<form action='?seite=".get_requested_pagename()."&blog_admin=submit' method='post'>";
    
    $html_output .= "<table style='width:90%;border:0px;'7>";
    
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "<strong>Titel dieser News:</strong>";
    $html_output .= "</td>";
    $html_output .= "<td>";
    $html_output .= "<input name='title' type='text' maxlength=200 size=70>
    <br/><br/>";
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
    "&single=<input style='font-weight:bold;background-color:rgb(240, 240, 240);border:0px;' name='seo_shortname' type='text' maxlength=200 size=50><br/>";
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
    
    
    return $html_output;
}


?>