<?php
class CommentManager {
	public function postComment($args) {
		$is_spam = false;
		$errors = array ();
		if (Settings::get ( "spamfilter_enabled" ) == "yes") {
			if (StringHelper::isNotNullOrEmpty ( $args ["phone"] )) {
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
		
		if ($is_spam) {
			Settings::set ( "contact_form_refused_spam_mails", Settings::get ( "contact_form_refused_spam_mails" ) + 1 );
		}
		
		if (StringHelper::isNullOrEmpty ( $args ["parent_id"] )) {
			$errors [] = get_translation ( "parent_id_is_empty" );
		}
		if (StringHelper::isNullOrEmpty ( $args ["article_author_name"] )) {
			$errors [] = get_translation ( "author_is_empty" );
		}
		if (StringHelper::isNullOrEmpty ( $args ["content"] )) {
			$errors [] = get_translation ( "message_is_empty" );
		}
		
		$parent = intval ( $args ["parent"] );
		$article_author_name = $args ["article_author_name"];
		$content = $args ["content"];
		$content = strip_tags ( $content );
		$content = make_links_clickable ( $content );
		$comment_homepage = $args ["comment_homepage"];
		$article_author_email = $args ["article_author_email"];
		$autor = get_user_id ();
		if ($autor == 0) {
			$guestUser = getUserByName ( "guest" );
			if ($guestUser) {
				$autor = $guestUser ["id"];
			}
		}
		
		if (count ( $errors ) <= 0) {
			try {
				$parentData = ContentFactory::getByID ( $parent );
				$comment = new Comment ();
				$comment->autor = $autor;
				$comment->article_author_name = $article_author_name;
				$comment->article_author_email = $article_author_email;
				$comment->content = $content;
				$comment->comment_homepage = $comment_homepage;
				$comment->parent = $parent;
				$comment->title = "Comment " . time () . " on Article $parent";
				$comment->systemname = $parentData ["system-name"] . "_comment_" . uniqid () . "_" . $autor;
				$comment->hidden = 1;
				$comment->menu = $parentData ["menu"];
				$comment->language = $parentData ["language"];
				$comment->save ();
				return true;
			} catch ( Exception $e ) {
				$errors [] = get_translation ( "no_such_parent" );
			}
		}
		
		ViewBag::set ( "errors", $errors );
		Template::executeDefaultOrOwnTemplate ( "comments/error" );
		
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
