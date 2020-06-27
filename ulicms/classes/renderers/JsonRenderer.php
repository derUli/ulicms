<?php

declare(strict_types=1);

namespace UliCMS\Renderers;

use stdClass;
use ContentFactory;
use UliCMS\Utils\CacheUtil;
use UliCMS\Exceptions\DatasetNotFoundException;
use Page;

// this class renders a content as csv
class JsonRenderer
{
    public $target_file = null;
    public $content = null;
    public $title = null;

    protected function renderContent()
    {
        $this->title = get_title();
        ob_start();
        content();
        $content = normalizeLN(ob_get_clean());
        $this->content = trim($content);
    }

    public function render(): string
    {
        $cacheUid = CacheUtil::getCurrentUid();

        $adapter = CacheUtil::getAdapter();
        // if it is in cache return it from cache
        if ($adapter and $adapter->get($cacheUid)) {
            return $adapter->get($cacheUid);
        }

        // generate the json
        $this->renderContent();

        $data = new stdClass;
        $data->title = $this->title;
        $data->content = $this->content;
        $data->meta_description = get_meta_description();
        $data->meta_keywords = get_meta_keywords();

        try {
            $page = ContentFactory::getBySlugAndLanguage(
                get_slug(),
                getCurrentLanguage(true)
            );
        } catch (DatasetNotFoundException $e) {
            $page = new Page();
        }
        
        $data->data = $page->custom_data ? json_decode(
            json_encode(
                    $page->custom_data,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                )
        ) : new stdClass();

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
