<?php
class HighlightPHPCode extends Controller {
	public function contentFilter($html) {
		preg_match_all ( "/\[php_code]([\s\S]*)\[\/php_code]/i", $html, $match );
		if (count ( $match ) > 0) {
			for($i = 0; $i < count ( $match [0] ); $i ++) {
				$placeholder = $match [0] [$i];
				$code = $match [1] [$i];
				$code = strip_tags ( $code );
				$code = unhtmlspecialchars ( $code );
				$code = highlight_string ( $code, true );
				$html = str_replace ( $placeholder, $code, $html );
			}
		}
		return $html;
	}
}