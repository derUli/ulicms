<?php
class DesignSettingsController extends Controller {
	public function savePost() {
		if (! isset ( $_REQUEST ["disable_custom_layout_options"] )) {
			setconfig ( "disable_custom_layout_options", "disable" );
		} else {
			Settings::delete ( "disable_custom_layout_options" );
		}
		
		if (isset ( $_REQUEST ["no_mobile_design_on_tablet"] )) {
			setconfig ( "no_mobile_design_on_tablet", "no_mobile_design_on_tablet" );
		} else {
			Settings::delete ( "no_mobile_design_on_tablet" );
		}
		
		if (isset ( $_REQUEST ["video_width_100_percent"] )) {
			setconfig ( "video_width_100_percent", "width" );
		} else {
			Settings::delete ( "video_width_100_percent" );
		}
		
		if ($_REQUEST ["additional_menus"] !== $additional_menus) {
			setconfig ( "additional_menus", db_escape ( $_REQUEST ["additional_menus"] ) );
		}
		
		// Wenn Formular abgesendet wurde, Wert Speichern
		if ($_REQUEST ["theme"] !== $theme) { // if theme auf
			$themes = getThemesList ();
			if (faster_in_array ( $_REQUEST ["theme"], $themes )) { // if faster_in_array theme auf
				setconfig ( "theme", db_escape ( $_REQUEST ["theme"] ) );
				$theme = $_REQUEST ["theme"];
			} // if faster_in_array theme zu
		} // if theme zu
		  
		// Wenn Formular abgesendet wurde, Wert Speichern
		if ($_REQUEST ["mobile_theme"] !== $mobile_theme) { // if mobile_theme auf
			$themes = getThemesList ();
			if (empty ( $_REQUEST ["mobile_theme"] ))
				Settings::delete ( "mobile_theme" );
			else if (faster_in_array ( $_REQUEST ["mobile_theme"], $themes )) { // if faster_in_array mobile_theme auf
				setconfig ( "mobile_theme", db_escape ( $_REQUEST ["mobile_theme"] ) );
				$mobile_theme = $_REQUEST ["mobile_theme"];
			} // if faster_in_array mobile_theme zu
		} // if mobile_theme zu
		
		if ($_REQUEST ["default-font"] != Settings::get ( "default-font" )) {
			if (! empty ( $_REQUEST ["custom-font"] )) {
				$font = $_REQUEST ["custom-font"];
			} else {
				$font = $_REQUEST ["default-font"];
			}
			
			$font = db_escape ( $font );
			
			setconfig ( "default-font", $font );
		}
		
		if (! empty ( $_REQUEST ["google-font"] )) {
			$font = $_REQUEST ["google-font"];
			$font = db_escape ( $font );
			setconfig ( "google-font", $font );
		}
		
		setconfig ( "zoom", intval ( $_REQUEST ["zoom"] ) );
		setconfig ( "font-size", db_escape ( $_REQUEST ["font-size"] ) );
		setconfig ( "ckeditor_skin", db_escape ( $_REQUEST ["ckeditor_skin"] ) );
		
		if (Settings::get ( "header-background-color" ) != $_REQUEST ["header-background-color"]) {
			setconfig ( "header-background-color", db_escape ( $_REQUEST ["header-background-color"] ) );
		}
		
		if (Settings::get ( "body-text-color" ) != $_REQUEST ["body-text-color"]) {
			setconfig ( "body-text-color", db_escape ( $_REQUEST ["body-text-color"] ) );
		}
		
		if (Settings::get ( "title_format" ) != $_REQUEST ["title_format"])
			setconfig ( "title_format", db_escape ( $_REQUEST ["title_format"] ) );
		
		if (Settings::get ( "body-background-color" ) != $_REQUEST ["body-background-color"]) {
			setconfig ( "body-background-color", db_escape ( $_REQUEST ["body-background-color"] ) );
		}
	}
}