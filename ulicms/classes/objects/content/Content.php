<?php

declare(strict_types=1);

use UliCMS\Exceptions\DatasetNotFoundException;

abstract class Content extends Model {

    abstract protected function loadBySlugAndLanguage($name, $language);

    public function getShowHeadline(): bool {
        return $this->show_headline;
    }

    public static function emptyTrash(): void {
        Database::query("DELETE FROM {prefix}content WHERE deleted_at "
                . "IS NOT NULL", true);
    }

    public function getHeadline(): ?string {
        return StringHelper::isNullOrEmpty($this->alternate_title) ?
                $this->title : $this->alternate_title;
    }

    // returns true if this is regular content
    // regular content is content that contains regular html text
    // non regular content types are for example nodes and links
    public function isRegular(): bool {
        return true;
    }

    public function getChildren(string $order = "id"): array {
        if (!$this->getID()) {
            return [];
        }
        try {
            return ContentFactory::getAllByParent($this->getID(), $order);
        } catch (DatasetNotFoundException $e) {
            return [];
        }
    }

    public function hasChildren(): bool {
        return count($this->getChildren()) > 0;
    }

}
