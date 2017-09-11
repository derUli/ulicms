<?php
class SpamFilterController extends Controller {
	public function savePost() {
		add_hook ( "before_save_spamfilter_settings" );
		
		if ($_POST ["spamfilter_enabled"] == "yes") {
			Settings::set ( "spamfilter_enabled", "yes" );
		} else {
			Settings::set ( "spamfilter_enabled", "no" );
		}
		
		if (isset ( $_POST ["country_blacklist"] )) {
			Settings::set ( "country_blacklist", $_POST ["country_blacklist"] );
		}
		
		if (isset ( $_POST ["check_for_spamhaus"] )) {
			Settings::set ( "check_for_spamhaus", "check" );
		} else {
			Settings::delete ( "check_for_spamhaus" );
		}
		
		if (isset ( $_POST ["spamfilter_words_blacklist"] )) {
			$blacklist = $_POST ["spamfilter_words_blacklist"];
			$blacklist = str_replace ( "\r\n", "||", $blacklist );
			$blacklist = str_replace ( "\n", "||", $blacklist );
			Settings::set ( "spamfilter_words_blacklist", $blacklist );
		}
		
		if (isset ( $_POST ["disallow_chinese_chars"] )) {
			Settings::set ( "disallow_chinese_chars", "disallow" );
		} else {
			Settings::delete ( "disallow_chinese_chars" );
		}
		if (isset ( $_POST ["disallow_cyrillic_chars"] )) {
			Settings::set ( "disallow_cyrillic_chars", "disallow" );
		} else {
			Settings::delete ( "disallow_cyrillic_chars" );
		}
		add_hook ( "after_save_spamfilter_settings" );
		Request::redirect ( ModuleHelper::buildActionURL ( "spam_filter" ) );
	}
}