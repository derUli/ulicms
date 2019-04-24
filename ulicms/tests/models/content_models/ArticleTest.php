<?php

class ArticleTest extends \PHPUnit\Framework\TestCase {

    public function tearDown() {
        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
    }

    public function testSetArticle() {
        $article = new Article();
        $article->title = "Unit Test Article";
        $article->systemname = "unit test";
        $article->menu = "none";
        $article->language = "de";
        $article->article_date = 1413821696;
        $article->author_id = 1;
        $article->group_id = 1;
        $article->save();
        $id = $article->id;

        $article = ContentFactory::getByID($id);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals(1413821696, $article->article_date);
    }

}
