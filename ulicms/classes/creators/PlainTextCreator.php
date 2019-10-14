<?php

declare(strict_types=1);

namespace UliCMS\Creators;

use Template;
use UliCMS\Utils\CacheUtil;

// this class renders a page as plain text
class PlainTextCreator {

    public $target_file = null;
    public $content = null;
    public $title = null;

    public function __construct() {
        ob_start();
        echo get_title();
        echo "\r\n";
        echo "\r\n";
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

        // clean up html content
        $this->content = br2nlr($this->content);
        $this->content = strip_tags($this->content);
        $this->content = str_replace("\r\n", "\n", $this->content);
        $this->content = str_replace("\r", "\n", $this->content);
        $this->content = str_replace("\n", "\r\n", $this->content);
        $this->content = unhtmlspecialchars($this->content);
        $this->content = preg_replace_callback('/&#([0-9a-fx]+);/mi', 'replace_num_entity', $this->content);


        if ($adapter) {
            $adapter->set($uid, $this->content, CacheUtil::getCachePeriod());
        }
        return $this->content;
    }

}
