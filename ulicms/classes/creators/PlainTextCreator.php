<?php

declare(strict_types=1);

namespace UliCMS\Creators;

use Template;
use UliCMS\Utils\CacheUtil;

// this class renders a page as plain text
class PlainTextCreator {

    public $content = null;

    // render html content to string
    protected function renderContent(): void {
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
        $cacheUid = CacheUtil::getCurrentUid();
        $adapter = CacheUtil::getAdapter();

        // return the rendered text from cache if it exists
        if ($adapter and $adapter->has($cacheUid)) {
            return $adapter->get($cacheUid);
        }

        // if the generated txt is not in cache yet
        // generate it
        $this->renderContent();

        // clean up html stuff
        $this->content = br2nlr($this->content);
        $this->content = strip_tags($this->content);
        $this->content = str_replace("\r\n", "\n", $this->content);
        $this->content = str_replace("\r", "\n", $this->content);
        $this->content = str_replace("\n", "\r\n", $this->content);
        $this->content = unhtmlspecialchars($this->content);
        $this->content = preg_replace_callback(
                '/&#([0-9a-fx]+);/mi',
                'replace_num_entity',
                $this->content
        );

        // save this in cache
        if ($adapter) {
            $adapter->set($cacheUid, $this->content, CacheUtil::getCachePeriod());
        }
        return $this->content;
    }

}
