<?php

declare(strict_types=1);

namespace UliCMS\Creators;

use UliCMS\Utils\CacheUtil;

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

    public function render(): string {
        $uid = CacheUtil::getCurrentUid();
        $adapter = CacheUtil::getAdapter();
        if ($adapter and $adapter->has($uid)) {
            return $adapter->get($uid);
        }

        $data = [];
        $this->content = str_replace("\r\n", "\n", $this->content);
        $this->content = str_replace("\r", "\n", $this->content);
        $this->content = str_replace("\n", "\r\n", $this->content);
        $data["title"] = $this->title;
        $data["content"] = $this->content;
        $data["meta_description"] = get_meta_description();
        $data["meta_keywords"] = get_meta_keywords();
        $json_string = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($adapter) {
            $adapter->set($uid, $json_string, CacheUtil::getCachePeriod());
        }
        return $json_string;
    }

}
