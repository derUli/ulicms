<?php

// Demonstration von "PHP Form Builder Class"
// Diese ist ab UliCMS 9.0.0 im Kern der Software enthalten.
// Unter UliCMS 8.0.x muss zuerst das "pfbc" Paket installiert werden
function pfbc_sample_render() {
	// PHP Formbuilder Klasse initialisieren
	initPFBC ();
	$options = array (
			"Option #1",
			"Option #2",
			"Option #3" 
	);
	$form = new Form ( "html5" );
	$form = new Form ( "form-elements" );
	$form->configure ( array (
			"prevent" => array (
					"bootstrap",
					"jQuery" 
			) 
	) );
	$form->addElement ( new Element_Hidden ( "form", "form-elements" ) );
	$form->addElement ( new Element_HTML (get_csrf_token_html()) );
	$form->addElement ( new Element_HTML ( '<legend>Standard</legend>' ) );
	$form->addElement ( new Element_Textbox ( "Textbox:", "Textbox" ) );
	$form->addElement ( new Element_Password ( "Password:", "Password" ) );
	$form->addElement ( new Element_File ( "File:", "File" ) );
	$form->addElement ( new Element_Textarea ( "Textarea:", "Textarea" ) );
	$form->addElement ( new Element_Select ( "Select:", "Select", $options ) );
	$form->addElement ( new Element_Radio ( "Radio Buttons:", "RadioButtons", $options ) );
	$form->addElement ( new Element_Checkbox ( "Checkboxes:", "Checkboxes", $options ) );
	$form->addElement ( new Element_HTML ( '<legend>HTML5</legend>' ) );
	$form->addElement ( new Element_Phone ( "Phone:", "Phone" ) );
	$form->addElement ( new Element_Search ( "Search:", "Search" ) );
	$form->addElement ( new Element_Url ( "Url:", "Url" ) );
	$form->addElement ( new Element_Email ( "Email:", "Email" ) );
	$form->addElement ( new Element_Date ( "Date:", "Date" ) );
	$form->addElement ( new Element_DateTime ( "DateTime:", "DateTime" ) );
	$form->addElement ( new Element_DateTimeLocal ( "DateTime-Local:", "DateTimeLocal" ) );
	$form->addElement ( new Element_Month ( "Month:", "Month" ) );
	$form->addElement ( new Element_Week ( "Week:", "Week" ) );
	$form->addElement ( new Element_Time ( "Time:", "Time" ) );
	$form->addElement ( new Element_Number ( "Number:", "Number" ) );
	$form->addElement ( new Element_Range ( "Range:", "Range" ) );
	$form->addElement ( new Element_Color ( "Color:", "Color" ) );
	$form->addElement ( new Element_HTML ( '<legend>jQuery UI</legend>' ) );
	$form->addElement ( new Element_jQueryUIDate ( "Date:", "jQueryUIDate" ) );
	$form->addElement ( new Element_Checksort ( "Checksort:", "Checksort", $options ) );
	$form->addElement ( new Element_Sort ( "Sort:", "Sort", $options ) );
	$form->addElement ( new Element_HTML ( '<legend>WYSIWYG Editor</legend>' ) );
	$form->addElement ( new Element_TinyMCE ( "TinyMCE:", "TinyMCE" ) );
	$form->addElement ( new Element_CKEditor ( "CKEditor:", "CKEditor" ) );
	$form->addElement ( new Element_HTML ( '<legend>Custom/Other</legend>' ) );
	$form->addElement ( new Element_State ( "State:", "State" ) );
	$form->addElement ( new Element_Country ( "Country:", "Country" ) );
	$form->addElement ( new Element_YesNo ( "Yes/No:", "YesNo" ) );
	$form->addElement ( new Element_Captcha ( "Captcha:" ) );
	$form->addElement ( new Element_Button () );
	$form->addElement ( new Element_Button ( "Cancel", "button", array (
			"onclick" => "history.go(-1);" 
	) ) );
	
	return $form->render ( true );
}

