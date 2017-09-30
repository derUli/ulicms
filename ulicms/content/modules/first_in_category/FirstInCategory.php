<?php
class FirstInCategory extends Controller {
	// gibt die erste Seite in der Kategorie $category_id zurück oder null
	public function getFirstPageInCategory($category_id, $language = null) {
		if (! $language) {
			$language = getCurrentLanguage ();
		}
		$query = Database::pQuery ( "select id, access from `{prefix}content` where active = ?
						  and category = ? and language = ? limit 1", array (
				1,
				intval ( $category_id ),
				$language 
		), true ) or die ( Database::error () );
		if (Database::any ( $query )) {
			$result = Database::fetchSingleOrDefault ( $query );
			// if (checkAccess ( $result->access )) {
			return ContentFactory::getByID ( intval ( $result->id ) );
			// }
		}
		return null;
	}
	// gibt die erste Liste zurück, die nach der Kategorie $category_id filtert.
	public function getFirstListWithCategory($category_id, $language = null) {
		if (! $language) {
			$language = getCurrentLanguage ();
		}
		$query = Database::pQuery ( "select n.content_id as id, c.access as access from `{prefix}lists` n
						inner join `{prefix}content` c
						on c.id = n.content_id where c.type = ? and
						c.active = ? and c.language = ? and
						n.category_id = ?", array (
				"list",
				1,
				$language,
				$category_id 
		), true );
		if (Database::any ( $query )) {
			$result = Database::fetchSingleOrDefault ( $query );
			// if (checkAccess ( $result->access )) {
			return ContentFactory::getByID ( intval ( $result->id ) );
			// }
		}
		return null;
	}
}