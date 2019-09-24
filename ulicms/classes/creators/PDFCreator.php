<?php

declare(strict_types=1);

namespace UliCMS\Creators;

use Template;
use Mpdf\Mpdf;
use UliCMS\Utils\CacheUtil;

// this class renders a page as pdf using mPDF
class PDFCreator {

    public $content = null;

    // renders the content html to class variable
    protected function renderContent(): void {
        ob_start();
        echo "<h1>" . get_title() . "</h1>";
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

    // renders the pdf and returns the pdf binary data as string
    public function render(): string {

        $cacheUid = CacheUtil::getCurrentUid();

        // if cache is enabled and the page is stored in cache return it
        $adapter = CacheUtil::getAdapter();
        if ($adapter and $adapter->has($cacheUid)) {
            return $adapter->get($cacheUid);
        }
        // if the page is not in cache, generate html and render it to pdf
        $this->renderContent();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

        $mpdf->WriteHTML($this->content);
        $output = $mpdf->Output('foobar.pdf',
                \Mpdf\Output\Destination::STRING_RETURN);

        if ($adapter) {
            $adapter->set($cacheUid, $output, CacheUtil::getCachePeriod());
        }
        return $output;
    }

}
