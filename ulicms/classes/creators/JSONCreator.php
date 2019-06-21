<?php

namespace UliCMS\Creators;

class JSONCreator {

    public $target_file = null;
    public $content = null;
    public $title = null;

    public function __construct() {
        $this->title = get_title();
        ob_start();
        content();
        $this->content = ob_get_clean();
    }

    private function httpHeader() {
        header("Content-type: application/json; charset=UTF-8");
    }

    public function output() {
        $uid = CacheUtil::getCurrentUid();
        $adapter = CacheUtil::getAdapter();
        if ($adapter and $adapter->has($uid)) {
            $adapter->get($uid);
        }


        $data = [];
        $this->content = str_replace("\r\n", "\n", $this->content);
        $this->content = str_replace("\r", "\n", $this->content);
        $this->content = str_replace("\n", "\r\n", $this->content);
        $data["title"] = $this->title;
        $data["content"] = $this->content;
        $data["meta_description"] = get_meta_description();
        $data["meta_keywords"] = get_meta_keywords();
        $data["author"] = $author;
        $json_string = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->httpHeader();
        echo $json_string;
        if ($adapter) {
            $adapter->set($uid, $json_string, CacheUtil::getCachePeriod());
        }
        exit();
    }

}
