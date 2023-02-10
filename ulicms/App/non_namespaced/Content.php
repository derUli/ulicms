<?php

declare(strict_types=1);

abstract class Content extends Model {

    abstract protected function loadBySlugAndLanguage($name, $language);

    public function getShowHeadline(): bool {
        return (bool) $this->show_headline;
    }

    public static function emptyTrash(): void {
        Database::deleteFrom(
                "content",
                "deleted_at IS NOT NULL"
        );
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

        return ContentFactory::getAllByParent($this->getID(), $order);
    }

    public function hasChildren(): bool {
        return count($this->getChildren()) > 0;
    }

    public function getIcon(): string {
        return "far fa-file-alt";
    }

}
