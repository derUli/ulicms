<?php

namespace UliCMS\Creators;

$mpdf = ULICMS_ROOT . "/lib/MPDF60/mpdf.php";
if (is_file($mpdf)) {
    require_once ($mpdf);
}

class PDFCreator {

    public $target_file = null;
    public $content = null;
    public $language = null;
    public $paper_format = "A4";

    public function __construct() {
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

    private function httpHeader() {
        header("Content-type: application/pdf");
    }

    public function output() {
        $hasModul = containsModule(get_requested_pagename());

        if (!class_exists("mPDF")) {
            echo "mPDF is not installed. Please install <a href=\"http://www.ulicms.de/mpdf_supplement.html\" target=\"_blank\">mPDF supplement</a>.";
            die();
        }

        // TODO: Reimplement Caching of generated pdf files

        $mpdf = new mPDF(getCurrentLanguage(true), 'A4');
        $mpdf->WriteHTML($this->content);
        $this->httpHeader();
        $mpdf->output();
        exit();
    }

}
