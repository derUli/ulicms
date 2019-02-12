<?php

use WebPConvert\WebPConvert;

class ImagesWebPController extends MainClass {

    public function afterContentFilter($html) {
        if ($this->checkForWebPSupport()) {
            $input = $this->replaceImagesWithWebP($html);
        }
        return $input;
    }

    public function checkForWebPSupport($http_accept = null) {
        if ($http_accept == null) {
            $http_accept = $_SERVER['HTTP_ACCEPT'];
        }
        return strpos($http_accept, 'image/webp') !== false;
    }

    public function replaceImagesWithWebP($content) {
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        foreach ($dom->getElementsByTagName('img') as $node) {
            $oldSrc = $node->getAttribute('src');
            if (startsWith($oldSrc, "/") or ! startsWith($oldSrc, "http")) {
                $extension = file_extension($oldSrc);
                if (in_array($extension, ["jpg", "jpeg", "png", "gif"])) {
                    $newSrc = ModuleHelper::buildMethodCallUrl(self::class,
                                    "convertToWebP",
                                    ModuleHelper::buildQueryString(
                                            array("file" => $oldSrc)));
                    $node->setAttribute("src", $newSrc);
                }
            }
        }

        $newHtml = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(array(
            '<html>',
            '</html>',
            '<body>',
            '</body>'
                        ), array(
            '',
            '',
            '',
            ''
                        ), $dom->saveHTML()));
        return $newHtml;
    }

    public function convertToWebP() {
        $inputFile = Request::getVar("file");
        $inputFile = remove_prefix($inputFile, "/");
        $inputFile = remove_prefix($inputFile, ".");
        $inputFile = remove_prefix($inputFile, "..");
        $inputFile = remove_prefix($inputFile, "http://");
        $inputFile = remove_prefix($inputFile, "https://");
        $inputFile = remove_prefix($inputFile, "ftp://");

        $inputFile = Path::resolve("ULICMS_ROOT/{$inputFile}");

        $hash = md5($inputFile);

        $outputDir = Path::resolve("ULICMS_CACHE/webp-images");
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }


        $outputFile = Path::resolve("{$outputDir}/{$hash}.webp");


        $webPConvertSettings = [
            // It is not required that you set any options - all have sensible defaults.
            // We set some, for the sake of the example.
            'quality' => 'auto',
            'max-quality' => 85,
            'converters' => ['cwebp', 'gd', 'imagick'] // Specify conversion methods to use, and their order
        ];

        $webPConvertSettings = array_filter("images_webp_settings",
                "webp_converter_settings");

        $success = WebPConvert::convertAndServe($inputFile, $outputFile,
                        $webPConvertSettings);
        // if conversion failed, serve original file
        if (!$success) {
            if (file_exists($inputFile)) {
                $mime = mime_content_type($inputFile);
                if (startsWith($mime, "image/")) {
                    Result(file_get_contents($inputFile), HttpStatusCode::OK, $mime);
                }
            } else {
                ExceptionResult("Image file not found", HttpStatusCode::NOT_FOUND);
            }
        }
    }

}
