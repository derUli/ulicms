<?php
function blog_edit_form($post_id) {
	$query = db_query ( "SELECT * FROM `" . tbname ( "blog" ) . "` WHERE id = $post_id" );
	if (db_num_rows ( $query ) == 0) {
		return "<p class='ulicms_errror'>Dieser Blogpost ist nicht mehr vorhanden</p>";
	} else {
		$post = db_fetch_object ( $query );
		
		$html_output = '<div class="blog-admin-edit">';
		$all_languages = getAllLanguages ();
		$html_output .= "<form action='" . buildSEOUrl ( get_requested_pagename () ) . "?blog_admin=update' method='post' style=''>";
		
		$html_output .= get_csrf_token_html ();
		$html_output .= "<table class='blog_admin_table'>";
		
		$html_output .= "<tr>";
		$html_output .= "<td style='width:220px;'>";
		$html_output .= "<strong>Titel dieser News:</strong>";
		$html_output .= "</td>";
		$html_output .= "<td>";
		$html_output .= "<input name='title' type='text' maxlength=200 size=40 value='" . htmlspecialchars ( $post->title, ENT_QUOTES, "UTF-8" ) . "'>";
		$html_output .= "</td>";
		$html_output .= "</tr>";
		
		$html_output .= "<tr>";
		$html_output .= "<td>";
		$html_output .= "<strong>SEO-URL:</strong>";
		$html_output .= "</td>";
		$html_output .= "<td>";
		$html_output .= "http://" . $_SERVER ["SERVER_NAME"] . "/pfad/zu/ulicms/" . get_requested_pagename () . ".html?single=<input style='font-weight:bold;background-color:rgb(240, 240, 240);border:0px;' name='seo_shortname' type='text' maxlength=200 size=30 value='" . htmlspecialchars ( $post->seo_shortname ) . "'>";
		$html_output .= "</td>";
		$html_output .= "</tr>";
		
		$html_output .= "<tr>";
		$html_output .= "<td>";
		$html_output .= "<strong>Sprache des Blogeintrags: </strong>";
		$html_output .= "</td>";
		
		$html_output .= "<td>";
		$html_output .= "<select name='language'>";
		for($i = 0; $i < count ( $all_languages ); $i ++) {
			$this_language = $all_languages [$i];
			$translatedLanguage = getLanguageNameByCode ( $this_language );
			if ($post->language == $this_language) {
				$html_output .= "<option value='$this_language' selected>$translatedLanguage</option>";
			} else {
				$html_output .= "<option value='$this_language'>$translatedLanguage</option>";
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
		
		if ($post->entry_enabled) {
			$html_output .= "
    <select name='entry_enabled'>
    <option value='1' selected>Ja</option>
    <option value='0'>Nein</option>
    ";
		} else {
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
		
		if ($post->comments_enabled) {
			$html_output .= "
    <select name='comments_enabled'>
    <option value='1' selected>Ja</option>
    <option value='0'>Nein</option>
    ";
		} else {
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
		$html_output .= "<strong><input name=\"datum\" type=\"datetime-local\" value=\"" . date ( "Y-m-d\TH:i:s", $post->datum ) . "\" step=any></strong>";
		$html_output .= "</td>";
		$html_output .= "</tr>";
		
		$html_output .= "<tr>";
		$html_output .= "<td><strong>Meta Beschreibung:</strong></td>";
		$html_output .= "<td><textarea name=\"meta_description\" cols=\"40\" rows=\"3\">" . real_htmlspecialchars ( $post->meta_description ) . "</textarea></td>";
		$html_output .= "</tr>";
		
		$html_output .= "<tr>";
		$html_output .= "<td><strong>Meta Keywords:</strong></td>";
		$html_output .= "<td><textarea name=\"meta_keywords\" cols=\"40\" rows=\"3\">" . real_htmlspecialchars ( $post->meta_keywords ) . "</textarea></td>";
		$html_output .= "</tr>";
		
		$html_output .= "<tr>";
		$html_output .= "<td></td>";
		$html_output .= "<td align='center'><strong>Inhalt:</strong></td>";
		$html_output .= "</tr>";
		
		$html_output .= "<tr>";
		$html_output .= "<td></td>";
		
		$html_output .= "<td align='center'>";
		
		$html_output .= '<textarea name="content_full" id="content_full" cols=60 rows=20>' . htmlspecialchars ( $post->content_full ) . '</textarea>
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
		
		$html_output .= '<textarea name="content_preview" id="content_preview" cols=80 rows=20>' . htmlspecialchars ( $post->content_preview ) . '</textarea>
<noscript>
<p style="color:red;">Der Editor benötigt JavaScript. Bitte aktivieren Sie JavaScript. <a href="http://jumk.de/javascript.html" target="_blank">[Anleitung]</a></p>

</noscript>';
		
		$html_output .= "</td>";
		
		$html_output .= "</tr>";
				
		$html_output .= "<tr>";
		$html_output .= "<td>";
		$html_output .= "</td>";
		$html_output .= "<td align='center'>";
		$html_output .= "<input type='submit' value='Speichern'>";
		$html_output .= "</td>";
		$html_output .= "</tr>";
		
		$html_output .= "</table>";
		$html_output .= "<input type='hidden' name='id' value='" . $post->id . "'>";
		$html_output .= "</form>";
		
		$html_output .= "<script type=\"text/javascript\">
    

    
    
var editor1 = CKEDITOR.replace( 'content_full',
					{
						skin : '" . getconfig ( "ckeditor_skin" ) . "'
					});          
					
					
var editor2 = CKEDITOR.replace( 'content_preview',
					{
						skin : '" . getconfig ( "ckeditor_skin" ) . "'
					});   

    if(typeof jQuery != \"undefined\"){					                                    
editor1.on(\"instanceReady\", function()
{
	this.document.on(\"keyup\", CKCHANGED);
	this.document.on(\"paste\", CKCHANGED);
}
);

editor2.on(\"instanceReady\", function()
{
	this.document.on(\"keyup\", CKCHANGED);
	this.document.on(\"paste\", CKCHANGED);
}
);

function CKCHANGED() { 
	formChanged = 1;
}					
			
var formChanged = 0;
var submitted = 0;
 
$(document).ready(function() {
	$('form').each(function(i,n){
		$('input', n).change(function(){formChanged = 1});
		$('textarea', n).change(function(){formChanged = 1});
		$('select', n).change(function(){formChanged = 1}); 
		$(n).submit(function(){submitted=1});
	});
});
 
window.onbeforeunload = confirmExit;
function confirmExit()
{
	if(formChanged == 1 && submitted == 0)
		return \"Wenn Sie diese Seite verlassen gehen nicht gespeicherte Änderungen verloren.\";
	else 
		return;
}		

}	
</script>";
		
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
		
		$html .= "</div>";
		return $html_output;
	}
}

?>
