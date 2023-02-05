<?php

declare(strict_types=1);

namespace UliCMS\Renderers;

use Template;
use UliCMS\Utils\CacheUtil;

// this class renders a content as csv
class CsvRenderer
{
    public $content = null;
    public $title = null;

    protected function renderContent()
    {
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
        $this->content = trim($this->content);
        $this->content = normalizeLN($this->content, "\n");
        $this->content = str_replace("\r\n", "\\r\\n", $this->content);
    }
    

    // get data nested array for csv lines
    private function getData(): array
    {
        $data = [];
        $data[] = [
            "Title",
            "Content",
            "Description",
            "Author"
        ];
        $data[] = [
            $this->title,
            $this->content,
            get_meta_description()
        ];
        return $data;
    }

    public function render(): string
    {
        $cacheUid = CacheUtil::getCurrentUid();
        $adapter = CacheUtil::getAdapter();
        
        // if it is in cache return it from there
        if ($adapter and $adapter->get($cacheUid)) {
            return $adapter->get($cacheUid);
        }

        $this->renderContent();

        $data = $this->getData();

        $csv_string = getCSV($data[0]);
        $csv_string .= getCSV($data[1]);

        if ($adapter) {
            $adapter->set($cacheUid, $csv_string);
        }
        return trim($csv_string);
    }
}
