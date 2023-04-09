<?php

defined('ULICMS_ROOT') || exit('no direct script access allowed');

class Article extends Page
{
    // FIXME: Variablen alle private machen
    // und getter und setter implementieren
    public $article_author_name = '';
    public $article_author_email = '';
    public $article_date = null;
    public $article_image = '';
    public $excerpt = '';
    public $type = 'article';

    protected function fillVars($result = null)
    {
        parent::fillVars($result);

        // article metadata
        $this->article_author_email = $result->article_author_email;
        $this->article_author_name = $result->article_author_name;
        $this->article_image = $result->article_image;
        $this->article_date = $result->article_date ? strtotime($result->article_date) : null;
        $this->excerpt = $result->excerpt;
    }

    public function save()
    {
        $retval = null;
        if ($this->id === null) {
            $retval = $this->create();
            $retval = $this->update();
        } else {
            $retval = $this->update();
        }
        return $retval;
    }

    public function update()
    {
        if ($this->id === null) {
            return false;
        }
        parent::update();

        $article_date = null;
        if (is_numeric($this->article_date)) {
            $article_date = (int)$this->article_date;
        } elseif (is_string($this->article_date)) {
            $article_date = $this->article_date ? strtotime($this->article_date) : null;
        }

        $sql = 'update {prefix}content set article_author_email = ?,
article_author_name = ?,
article_image = ?,
article_date = from_unixtime(?),
excerpt = ? where id = ?';
        $args = [
            $this->article_author_email,
            $this->article_author_name,
            $this->article_image,
            $article_date,
            $this->excerpt,
            $this->id
        ];
        return Database::pQuery($sql, $args, true);
    }

     /**
     * Get css classes for Font Awesome icon
     * @return string
     */
    public function getIcon(): string
    {
        return 'far fa-newspaper';
    }
}
