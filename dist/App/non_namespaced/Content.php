<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('no direct script access allowed');

abstract class Content extends Model
{

    public $show_headline = true;
    
    public $title = '';

    public $alternate_title = '';


    /**
     * Get if the headline is shown
     * @return bool
     */
    public function getShowHeadline(): bool
    {
        return (bool)$this->show_headline;
    }

    /**
     * Do a hard delete of all soft deleted content
     * @return void
     */
    public static function emptyTrash(): void
    {
        Database::deleteFrom(
            'content',
            'deleted_at IS NOT NULL'
        );
    }

    /**
     * Get title or alternate title
     * @return string|null
     */
    public function getHeadline(): ?string
    {
        return empty($this->alternate_title) ?
                $this->title : $this->alternate_title;
    }

    /**
     * Check if this is content is regular
     * Regular means that it is a accessible page
     * This applies to any default contents except Link, Language_Link and Node
     * @return bool
     */
    public function isRegular(): bool
    {
        return true;
    }

    /**
     * Get children of this content
     * @param string $order
     * @return array
     */
    public function getChildren(string $order = 'id'): array
    {
        if (! $this->getID()) {
            return [];
        }

        return ContentFactory::getAllByParent($this->getID(), $order);
    }

    /**
     * Check if the content has children
     * @return bool
     */
    public function hasChildren(): bool
    {
        return count($this->getChildren()) > 0;
    }

     /**
      * Get css classes for Font Awesome icon
      * @return string
      */
    public function getIcon(): string
    {
        return 'far fa-file-alt';
    }

    abstract protected function loadBySlugAndLanguage($name, $language);
}
