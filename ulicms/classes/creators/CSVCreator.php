<?php

namespace UliCMS\Creators;

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

    private function httpHeader() {
        header("Content-type: text/csv; charset=UTF-8");
    }

    public function output() {
        $uid = CacheUtil::getCurrentUid();
        $adapter = CacheUtil::getAdapter();
        if ($adapter and $adapter->has($uid)) {
            $adapter->get($uid);
        }


        $data = array();
        $data[] = array(
            "Title",
            "Content",
            "Meta Description",
            "Meta Keywords",
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

        $this->httpHeader();
        echo $csv_string;
        if ($adapter) {
            $adapter->set($uid, $csv_string, CacheUtil::getCachePeriod());
        }
        exit();
    }

}
