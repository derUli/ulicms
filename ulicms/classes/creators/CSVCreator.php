<?php

declare(strict_types=1);

namespace UliCMS\Creators;

use Template;
use UliCMS\Utils\CacheUtil;

class CSVCreator {

    public $target_file = null;
    public $content = null;
    public $title = null;

    public function __construct() {
        $this->title = get_title();
        ob_start();

        $text_position = get_text_position();
        if ($text_position == "after") {
            Template::outputContentElement();
        }
        content();
        if ($text_position == "before") {
            Template::outputContentElement();
        }

        $this->content = ob_get_clean();
    }

    public function render(): string {
        $uid = CacheUtil::getCurrentUid();
        $adapter = CacheUtil::getAdapter();
        if ($adapter and $adapter->has($uid)) {
            return $adapter->get($uid);
        }

        $data = [];
        $data[] = array(
            "Title",
            "Content",
            "Description",
            "Tags",
            "Author"
        );
        $this->content = str_replace("\r\n", "\n", $this->content);
        $this->content = str_replace("\r", "\n", $this->content);
        $this->content = str_replace("\n", " ", $this->content);
        $data[] = array(
            $this->title,
            $this->content,
            get_meta_description(),
            get_meta_keywords()
        );
        $csv_string = getCSV($data[0]);
        $csv_string .= getCSV($data[1]);

        if ($adapter) {
            $adapter->set($uid, $csv_string, CacheUtil::getCachePeriod());
        }
        return $csv_string;
    }

}
