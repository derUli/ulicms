<?php

class ArticleTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        Database::query("delete from {prefix}content where title like 'Unit Test%'", true);
    }

    public function testSetArticle()
    {
        $article = new Article();
        $article->title = 'Unit Test Article';
        $article->slug = 'unit test';
        $article->menu = 'none';
        $article->language = 'de';
        $article->article_date = 1413821696;
        $article->author_id = 1;
        $article->group_id = 1;
        $article->save();
        $id = $article->id;

        $article = ContentFactory::getByID($id);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals(1413821696, $article->article_date);

        $article->title = 'Unit Test Article 2';
        $article->save();
    }

    public function testSetArticleWithStringDate()
    {
        $article = new Article();
        $article->title = 'Unit Test Article';
        $article->slug = 'unit test';
        $article->menu = 'none';
        $article->language = 'de';
        $article->article_date = '2019-04-07';
        $article->author_id = 1;
        $article->group_id = 1;
        $article->save();

        $id = $article->id;

        $article = ContentFactory::getByID($id);

        $this->assertEquals(1554588000, $article->article_date);
    }

    public function testUpdateWithoutIdReturnsFalse()
    {
        $article = new Article();
        $this->assertFalse($article->update());
        $this->assertFalse($article->isPersistent());
    }
}
