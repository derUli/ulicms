<?php
if (! function_exists ( 'getFontFamilys' )) {
	function getFontFamilys() {
		global $fonts;
		$fonts = Array ();
		$fonts ["Times New Roman"] = "TimesNewRoman, 'Times New Roman', Times, Baskerville, Georgia, serif";
		$fonts ["Georgia"] = "Georgia, Times, 'Times New Roman', serif";
		$fonts ["Sans Serif"] = "sans-serif";
		$fonts ["Arial"] = "Arial, 'Helvetica Neue', Helvetica, sans-serif";
		$fonts ["Comic Sans MS"] = "Comic Sans MS";
		$fonts ["Helvetica"] = "Helvetica, Arial, 'lucida grande',tahoma,verdana,arial,sans-serif;";
		$fonts ["Tahoma"] = "Tahoma, Verdana, Segoe, sans-serif";
		$fonts ["Verdana"] = "Verdana, Geneva, sans-serif";
		$fonts ["Trebuchet MS"] = "'Trebuchet MS'";
		$fonts ["Lucida Grande"] = "'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Geneva, Verdana, sans-serif";
		$fonts ["monospace"] = "monospace";
		$fonts ["Courier"] = "Courier";
		$fonts ["Courier New"] = "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace";
		$fonts ["Lucida Console"] = "'Lucida Console', 'Lucida Sans Typewriter', Monaco, 'Bitstream Vera Sans Mono', monospace";
		$fonts ["fantasy"] = "fantasy";
		$fonts ["cursive"] = "cursive";
		$fonts ["Calibri"] = "Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif";
		$fonts ["Brush Script MT"] = "'Brush Script MT',Phyllis,'Lucida Handwriting',cursive";
		$fonts ["Zapf Chancery"] = "'Zapf Chancery', cursive";
		$fonts ["Calibri"] = "Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif";
		$fonts ["Segoe"] = "'wf_SegoeUI', 'Segoe UI', 'Segoe','Segoe WP', 'Tahoma', 'Verdana', 'Arial', 'sans-serif'";
		$fonts ["Google Font"] = "google";
		add_hook ( "custom_fonts" );
		
		// Hier bei Bedarf weitere Fonts einfügen
		// $fonts["Meine Font 1"] = "myfont1";
		// $fonts["Meine Font 2"] = "myfont2";
		// $fonts["Meine Font 3"] = "myfont3";
		// Weitere Fonts Ende
		uksort ( $fonts, "strnatcasecmp" );
		
		return $fonts;
	}
}
?>
