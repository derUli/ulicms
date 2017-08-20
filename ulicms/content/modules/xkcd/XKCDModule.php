<?php
class XKCDModule extends Controller {
	public function render() {
		$xkcd = new xkcd ();
		$html = '<div class="random-xkcd">';
		$comic = $xkcd->random ();
		$html .= '<h3>' . $comic->safe_title . ' - xkcd</h3>'; // prints the title
		$html .= "<img class=\"responsive-image\" src=\"{$comic->img}\" title=\"{$comic->alt}\"/>"; // prints the image (don't miss the hover text!)
		$html .= '</div>';
		$html = apply_filter ( $html, 'random_xkcd' );
		return $html;
	}
	public function accordionLayout() {
		$xkcd = new xkcd ();
		$comic = $xkcd->random ();
		$html .= '<h2 class="accordion-header">' . $comic->safe_title . ' - xkcd</h2>';
		$html .= '<div class="accordion-content">';
		$html .= "<img class=\"responsive-image\" src=\"{$comic->img}\" title=\"{$comic->alt}\"/>"; // prints the image (don't miss the hover text!)
		
		$html .= '</div>';
		$html = apply_filter ( $html, 'random_xkcd' );
		echo $html;
	}
}