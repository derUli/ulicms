<?php
class EvilCss extends Controller {
	public function frontendFooter() {
		enqueueStylesheet ( ModuleHelper::buildRessourcePath ( "evil_css", "evil.css" ) );
		return get_combined_stylesheet_html ();
	}
}