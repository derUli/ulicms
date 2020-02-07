<?php

declare(strict_types=1);

namespace UliCMS\Creators;

use UliCMS\Utils\CacheUtil;

// this class renders a content as csv
class JSONCreator {

    public $target_file = null;
    public $content = null;
    public $title = null;

    protected function renderContent() {
        $this->title = get_title();
        ob_start();
        content();
        $this->content = ob_get_clean();
    }

    public function render(): string {
        $cacheUid = CacheUtil::getCurrentUid();

        $adapter = CacheUtil::getAdapter();
        // if it is in cache return it from cache
        if ($adapter and $adapter->get($cacheUid)) {
            return $adapter->get($cacheUid);
        }

        // generate the json
        $this->renderContent();

        $data = [];
        $data["title"] = $this->title;
        $data["content"] = trim($this->content);
        $data["meta_description"] = get_meta_description();
        $data["meta_keywords"] = get_meta_keywords();
        $json_string = json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
        if ($adapter) {
            $adapter->set($cacheUid, $json_string);
        }
        return trim($json_string);
    }

}
