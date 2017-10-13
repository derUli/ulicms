<?php
/**
 * WordpressXmlImporter class - Manages the WordPress XML file and gets all data from that.
 */
class WordpressXmlImporter {
	public $wpXML;
	public $db;
	function __construct($wpXML) {
		$this->wpXML = $wpXML;
	}
	private function _slug($text) {
		$text = StringHelper::cleanString ( $text, "_" );
		return $text;
	}
	public function getPosts() {
		$xml = simplexml_load_file ( $this->wpXML );
		$posts = array ();
		$namespaces = $xml->getNameSpaces ( true );
		
		foreach ( $xml->channel->item as $item ) {
			$categories = array ();
			foreach ( $item->category as $category ) {
				$categories [] = $category ['nicename'];
			}
			
			$content = $item->children ( 'http://purl.org/rss/1.0/modules/content/' );
			$excerpt = $item->children ( 'http://wordpress.org/export/1.2/excerpt/' );
			$wpNs = $item->children ( 'http://wordpress.org/export/1.2/' );
			
			$post = array (
					"postTitle" => strval ( $item [0]->title ),
					"postSlug" => ! $wpnNS->post_name ? $this->_slug ( strval ( $item [0]->title ) ) : $wpNs->post_name,
					"postContent" => nl2br ( strval ( $content->encoded ) ),
					"postDesc" => nl2br ( strval ( $excerpt->encoded ) ),
					"postDate" => strtotime ( $item [0]->pubDate ),
					"postCategories" => implode ( ", ", $categories ),
					"commentStatus" => strval ( $wpNs->comment_status ) == "open",
					"menuOrder" => intval ( $wpNs->menu_order ),
					"postParent" => intval ( $wpNs->post_parent ),
					"postId" => intval ( $wpNs->post_id ) 
			);
			$posts [] = ( object ) $post;
		}
		return $posts;
	}
}
