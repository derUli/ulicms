<?php
class SearchController extends Controller {
	public function truncateSearchIndex() {
		return Database::query ( "TRUNCATE `{prefix}fulltext`", true );
	}
	public function saveDataset($identifier, $url, $title, $content, $language) {
		$identifier = strval ( $identifier );
		$url = strval ( $url );
		$title = strval ( $title );
		$content = strval ( $content );
		$language = strval ( $language );
		$args = array (
				$identifier,
				$url,
				$title,
				$content,
				$language 
		);
		$sql = "REPLACE INTO `{prefix}fulltext` (`identifier`, `url`, `title`, `content`, `language`) values (?, ?, ?, ?, ?)";
		return Database::pQuery ( $sql, $args, true );
	}
	public function runAllIndexers() {
		$modules = getAllModules ();
		foreach ( $modules as $module ) {
			$indexers = getModuleMeta ( $module, "indexers" );
			foreach ( $indexers as $key => $value ) {
				$fullPath = getModulePath ( $module ) . $value;
				if (file_exists ( $fullPath )) {
					include_once $fullPath;
					if (class_exists ( $key )) {
						$runner = new $key ();
						if ($runner instanceof Indexer and method_exists ( $runner, "doIndex" )) {
							$runner->doIndex ();
						}
					}
				}
			}
		}
	}
	public function search($subject, $language) {
		$subject = strval ( $subject );
		$sql = "SELECT *, MATCH (`content`) AGAINST (?) AS relevance
		FROM `{prefix}fulltext`
		WHERE MATCH (`content`) AGAINST (?) and language = ?
		ORDER BY relevance DESC";
		$args = array (
				$subject,
				$subject,
				$language 
		);
		$query = Database::pQuery ( $sql, $args, true );
		$result = array ();
		if (Database::getNumRows ( $query ) > 0) {
			while ( $row = Database::fetchObject ( $query ) ) {
				$result [] = $row;
			}
		}
		return $result;
	}
}
