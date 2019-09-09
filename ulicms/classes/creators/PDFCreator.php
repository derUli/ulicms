<?php

namespace UliCMS\Creators;

use Template;
use Mpdf\Mpdf;

// this class renders a page as pdf using mPDF
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

    public function render() {
        // TODO: Implement Caching of generated pdf files
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

        $mpdf->WriteHTML($this->content);
        $output = $mpdf->Output('foobar.pdf', \Mpdf\Output\Destination::STRING_RETURN);
        return $output;
    }

}
