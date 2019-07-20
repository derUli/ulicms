<?php

namespace UliCMS\Creators;

use Template;

$mpdf = ULICMS_ROOT . "/lib/MPDF60/mpdf.php";
if (is_file($mpdf)) {
    require_once ($mpdf);
}

use Mpdf\Mpdf;

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

        if (!class_exists("\\Mpdf\\Mpdf")) {
            ExceptionResult("Mpdf is not installed. Please install <a href=\"http://www.ulicms.de/mpdf_supplement.html\" target=\"_blank\">mPDF supplement</a>.");
        }

        // TODO: Reimplement Caching of generated pdf files
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

        $mpdf->WriteHTML($this->content);
        $output = $mpdf->Output('foobar.pdf', \Mpdf\Output\Destination::STRING_RETURN);
        return $output;
    }

}
