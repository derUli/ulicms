<?php
class WordpressXmlImporterHooks extends Controller {
	private $moduleName = "wordpress_xml_importer";
	public function getSettingsHeadline() {
		return "Wordpress XML Importer";
	}
	public function getSettingsText() {
		return get_translation ( "open" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "form.php" );
	}
	public function doImport() {
		@set_time_limit ( 0 );
		$idMapping = array ();
		
		$import_from = Request::getVar ( "import_from", "file", "str" );
		// TODO: Handle Upload
		$replace = Request::hasVar ( "replace" );
		$import_to = Request::getVar ( "import_to", "article", "str" );
		$language = Request::getVar ( "language", getSystemLanguage (), "str" );
		$autor = Request::getVar ( "autor", get_user_id (), "int" );
		$categories = Category::getAll ();
		$category = Request::getVar ( "category", $categories [0], "int" );
		$menu = Request::getVar ( "menu", "none", "str" );
		$parent = Request::getVar ( "parent", null, "int" ) > 0 ? Request::getVar ( "parent", null, "int" ) : null;
		
		$tmpFile = Path::resolve ( "ULICMS_TMP/" . uniqid () );
		if (move_uploaded_file ( $_FILES ['file'] ['tmp_name'], $tmpFile )) {
			$importer = new WordpressXmlImporter ( $tmpFile );
			$posts = $importer->getPosts ();
			foreach ( $posts as $post ) {
				$data = null;
				
				try {
					$newData = ContentFactory::loadBySystemnameAndLanguage ( $post->postSlug, $language );
					if ($newData->id) {
						if ($replace) {
							$data = $newData;
						} else {
							continue;
						}
					}
				} catch ( Exception $e ) {
					switch ($import_to) {
						case "page" :
							$data = new Page ();
							break;
						case "article" :
							$data = new Article ();
							break;
					}
				}
				$data->type = $import_to;
				$data->title = $post->postTitle;
				$data->systemname = $post->postSlug;
				$data->parent = $parent;
				$data->category = $category;
				$data->menu = $menu;
				$data->autor = $autor;
				$data->language = $language;
				$data->content = $post->postContent;
				// Todo convert postDate to Timestamp and set it
				$data->position = $post->menuOrder;
				// Todo map ids from import to our ids.
				$data->parent = $parent;
				$data->created = $post->postDate;
				$data->lastmodified = $post->postDate;
				if ($data instanceof Article or $data->type == "article") {
					$data->excerpt = $post->postDesc;
					$data->article_date = date ( 'Y-m-d H:i:s', $post->postDate );
				}
				$data->save ();
				if ($post->postId > 0) {
					$idMapping [$post->postId] = $data->id;
				}
			}
			foreach ( $posts as $post ) {
				try {
					$data = ContentFactory::loadBySystemnameAndLanguage ( $post->postSlug, $language );
					if ($post->postParent > 0) {
						$data->parent = $idMapping [$post->postParent];
						$data->save ();
					}
				} catch ( Exception $e ) {
					continue;
				}
			}
		}
		
		unlink ( $tmpFile );
		Request::redirect ( ModuleHelper::buildAdminURL ( $this->moduleName ) );
	}
}
