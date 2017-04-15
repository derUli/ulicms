<?php
class Article extends Page {
	// @FIXME: Variablen alle private machen und getter und setter implementieren
	public $article_author_name = "";
	public $article_author_email = "";
	public $article_date = null;
	public $article_image = "";
	public $excerpt = "";
	public $type = "article";
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->article_author_email = $result->article_author_email;
		$this->article_author_name = $result->article_author_name;
		$this->article_image = $result->article_image;
		$this->article_date = $result->article_date;
		$this->excerpt = $result->expert;
	}
	public function save() {
		$retval = null;
		if ($this->id === null) {
			$retval = $this->create ();
			$this->update ();
		} else {
			$retval = $this->update ();
		}
		return $retval;
	}
	public function update() {
		if (is_null ( $this->id )) {
			return false;
		}
		parent::update ();
		$sql = "update {prefix} content set article_author_email = ?, article_author_name = ?, article_image = ?, article_date = ?, excerpt = ? where id = ?";
		$args = array (
				$this->article_author_email,
				$this->article_author_name,
				$this->article_image,
				$this->article_date,
				$this->excerpt,
				$this->id 
		);
	}
}
