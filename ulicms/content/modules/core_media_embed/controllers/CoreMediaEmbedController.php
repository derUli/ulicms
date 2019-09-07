<?php

use MediaEmbed\MediaEmbed;

class CoreMediaEmbedController extends MainClass {

    public function beforeContentFilter($input) {
        return $this->replaceLinks($input);
    }

    public function replaceLinks($input) {
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
        $dom = new DOMDocument();
        @$dom->loadHTML($input);


        foreach ($dom->getElementsByTagName('a') as $oldNode) {

            $href = $oldNode->getAttribute('href');
            $text = $oldNode->textContent;

            if ($href !== $text) {
                continue;
            }

            $url = $text;
            $embedCode = $this->embedCodeFromUrl($url);

            if ($embedCode) {
                $newNode = $this->createElementFromHTML($dom, $embedCode);
                var_dump($newNode);

                $dom->replaceChild($newNode, $oldNode); // this line doesn't work
            }
        }
        return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(array(
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
    }

    public function embedCodeFromUrl($url) {
        $mediaEmbed = new MediaEmbed();
        $mediaObject = $mediaEmbed->parseUrl($url);
        if ($mediaObject) {
            return $mediaObject->getEmbedCode();
        }
        return null;
    }

    public function createElementFromHTML($doc, $str) {

        $str = "<span>{$str}</span>";
        $d = new DOMDocument();
        $d->loadHTML($str);
        return $doc->importNode($d->documentElement, false);
    }

}
