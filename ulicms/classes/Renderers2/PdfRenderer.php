<?php

declare(strict_types=1);

namespace UliCMS\Renderers;

use Template;
use Mpdf\Mpdf;
use UliCMS\Utils\CacheUtil;
use StringHelper;
use ContentFactory;
use UliCMS\Exceptions\DatasetNotFoundException;

// this class renders a page as pdf using mPDF
class PdfRenderer {

    public $isMpdfInstalled = false;

    public function __construct() {
        $this->isMpdfInstalled = $this->checkIfMpdfIsInstalled();
    }

    protected function checkIfMpdfIsInstalled(): bool {
        return class_exists('Mpdf\Mpdf');
    }

    // renders the content html to class variable
    protected function renderContent(): void {
        ob_start();

        try {
            $content = ContentFactory::getCurrentPage();
            $showHeadline = $content->getShowHeadline();
        } catch (DatasetNotFoundException $e) {
            $showHeadline = true;
        }

        // print headline only if it is enabled for the current page
        if ($showHeadline) {
            echo "<h1>" . get_title() . "</h1>";
        }

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

    public function outputMpdfNotInstalled(): string {
        $clickableLink = StringHelper::makeLinksClickable(
                        "https://extend.ulicms.de/mPDF.html"
        );

        $message = get_translation(
                "mpdf_not_installed",
                [
                    "%link%" => $clickableLink
                ]
        );
        ExceptionResult($message);
        return $message;
    }

    // renders the pdf and returns the pdf binary data as string
    public function render(): string {

        // The Mpdf module is required to render pdf files
        // if it is not installed shown an error message to the user
        if (!$this->isMpdfInstalled) {
            return $this->outputMpdfNotInstalled();
        }

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
        $output = $mpdf->Output(
                'foobar.pdf',
                \Mpdf\Output\Destination::STRING_RETURN
        );

        if ($adapter) {
            $adapter->set($cacheUid, $output);
        }
        return $output;
    }

}
