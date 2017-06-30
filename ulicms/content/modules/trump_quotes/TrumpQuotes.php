<?php
class TrumpQuotes extends Controller {
	const SERVICE_URL = "https://api.whatdoestrumpthink.com/api/v1/quotes/";
	public function getTrumpQuote() {
		$content = file_get_contents_wrapper ( self::SERVICE_URL, false );
		$data = "";
		if ($content) {
			$data = json_decode ( $content );
			$data = $data->messages->non_personalized;
			$data = $data [array_rand ( $data, 1 )];
			$data = Template::getEscape ( $data );
		}
		return $data;
	}
	public function render() {
		return nl2br ( $this->getTrumpQuote () );
	}
	public function accordionLayout() {
		return Template::executeModuleTemplate ( "trump_quotes", "Dashboard.php" );
	}
}