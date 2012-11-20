<?php 
function blog_add_form(){
    $html_output = "";

    $html_output .= "<form action='?seite=".get_requested_pagename()."&blog_admin=submit' method='post'>";
    
    $html_output .= "<table>";
    $html_output .= "<tr>";
    $html_output .= "<td>";
    $html_output .= "</td>";
    $html_output .= "<td>";
    $html_output .= "<input type='submit' value='Speichern'>";
    $html_output .= "</td>";
    $html_output .= "</tr>";
    
    
    $html_output .= "</table>";
    
    $html_output .= "</form>";
    
    
    return $html_output;
}


?>