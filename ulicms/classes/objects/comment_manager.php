<?php
class CommentManager {
	public function postComment($args) {
		$is_spam = false;
		$errors = array ();
		if (Settings::get ( "spamfilter_enabled" ) == "yes") {
			if (isNotNullOrEmpty ( $args ["phone"] )) {
				$errors [] = get_translation ( "spam_honeypot_trapped" );
				$is_spam = true;
			}
			if ($this->containsBadWords ( $args ["article_author_name"] ) or $this->containsBadWords ( $args ["content"] )) {
				$errors [] = get_translation ( "comment_contains_badwords" );
				$is_spam = true;
			}
			if (isCountryBlocked ()) {
				$errors [] = get_translation ( "your_country_is_blocked" );
				$is_spam = true;
			}
			if (Settings::get ( "disallow_chinese_chars" ) and is_chinese ( $args ["content"] )) {
				$errors [] = get_translation ( "chinese_is_not_allowed" );
				$is_spam = true;
			}
		}

		if($is_spam){
			Settings::set( "contact_form_refused_spam_mails", Settings::get( "contact_form_refused_spam_mails" ) + 1);
		}
		
		if (isNullOrEmpty ( $args ["parent_id"] )) {
			$errors [] = get_translation ( "parent_id_is_empty" );
		}
		if (isNullOrEmpty ( $args ["article_author_name"] )) {
			$errors [] = get_translation ( "author_is_empty" );
		}
		if (isNullOrEmpty ( $args ["content"] )) {
			$errors [] = get_translation ( "message_is_empty" );
		}

		$parent_id = intval ( $args ["parent_id"] );
		$article_author_name = $args ["article_author_name"];
		$content = $args ["content"];
		$content = strip_tags ( $content );
		$content = make_links_clickable ( $content );
		$comment_homepage = $args ["comment_homepage"];

		if (count ( $errors ) <= 0) {
			try {
				$parent = ContentFactory::getByID ( $parent_id );
			} catch ( Exception $e ) {
				$errors [] = get_translation ( "no_such_parent" );
			}

			return true;
		}
		return false;
	}
	public function containsBadWords() {
		$words_blacklist = getconfig ( "spamfilter_words_blacklist" );
		$str = strtolower ( $str );

		if ($words_blacklist !== false) {
			$words_blacklist = explode ( "||", $words_blacklist );
		} else {
			return false;
		}

		for($i = 0; $i < count ( $words_blacklist ); $i ++) {
			$word = strtolower ( $words_blacklist [$i] );
			if (strpos ( $str, $word ) !== false)
				return true;
		}

		return false;
	}
}
