<?php
/**
 * WordPress class - Manages the WordPress XML file and gets all data from that.
 */
include_once "init.php";
class Wordpress {
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
		
		foreach ( $xml->channel->item as $item ) {
			$categories = array ();
			foreach ( $item->category as $category ) {
				$categories [] = $category ['nicename'];
			}
			
			$content = $item->children ( 'http://purl.org/rss/1.0/modules/content/' );
			$excerpt = $item->children ( 'http://wordpress.org/export/1.2/excerpt/' );
			$post = array (
					"postTitle" => strval ( $item [0]->title ),
					"postSlug" => $this->_slug ( strval ( $item [0]->title ) ),
					"postCont" => nl2br ( strval ( $content->encoded ) ),
					"postDesc" => nl2br ( strval ( $excerpt->encoded ) ),
					"postDate" => strftime ( "%Y-%m-%d %H:%M:%S", strtotime ( $item [0]->pubDate ) ),
					"postCategories" => implode ( ", ", $categories ) 
			);
			$posts [] = ( object ) $post;
		}
		
		return $posts;
	}
}
$wp = new Wordpress ( "wordpressdemoinstall.wordpress.2017-10-02.xml" );
$posts = $wp->getPosts ();
var_dump ( $posts );
