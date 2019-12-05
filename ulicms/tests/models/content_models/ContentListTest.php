
<?php

class ContentListTest extends \PHPUnit\Framework\TestCase {

	public function tearDown() {
		Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
	}

	public function testSetArticle() {
		$contentList = new Content_List ();
		$contentList->title = "Unit Test Article";
		$contentList->slug = "unit test";
		$contentList->menu = "none";
		$contentList->language = "de";
		$contentList->article_date = 1413821696;
		$contentList->author_id = 1;
		$contentList->group_id = 1;
		$contentList->save();

		$loadedContentList = new Content_List($contentList->getId());

		$this->assertInstanceOf(List_Data::class, $loadedContentList->listData);
	}

}
