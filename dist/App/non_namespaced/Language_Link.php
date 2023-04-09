<?php

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\Models\Content\Language;

// Links to another language
class Language_Link extends Page
{
    public $link_to_language = null;
    public $type = 'language_link';

    public function save()
    {
        $retval = null;
        if ($this->id === null) {
            $retval = $this->create();
            $this->update();
        } else {
            $retval = $this->update();
        }
        return $retval;
    }

    protected function fillVars($result = null)
    {
        parent::fillVars($result);
        $this->link_to_language = $result->link_to_language;
    }

    public function update()
    {
        $result = null;
        if ($this->id === null) {
            return $this->create();
        }
        parent::update();
        $sql = 'update {prefix}content set link_to_language = ? where id = ?';
        $args = [
            $this->link_to_language,
            $this->id
        ];

        $result = Database::pQuery($sql, $args, true);
        return $result;
    }

    public function getLinkedLanguage(): ?Language
    {
        return $this->link_to_language ? new Language($this->link_to_language) : null;
    }

    public function setLinkedLanguage(?Language $language): void
    {
        $this->link_to_language = $language ? $language->getID() : null;
    }

    /**
     * Check if this is content is regular
     * Regular means that it is a accessible page
     * This applies to any default contents except Link, Language_Link and Node
     * @return bool
     */
    public function isRegular(): bool
    {
        return false;
    }


     /**
     * Get css classes for Font Awesome icon
     * @return string
     */
    public function getIcon(): string
    {
        return 'fas fa-language';
    }
}
