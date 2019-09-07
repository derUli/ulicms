<?php

use MediaEmbed\MediaEmbed;

class CoreMediaController extends MainClass {

    public function beforeContentFilter($input) {
        $data = CustomData::get();

        $mediaEmbedEnabled = !($data and is_true($data["disable_media_embed"]));

        return $mediaEmbedEnabled ? $this->replaceLinks($input) : $input;
    }

    public function replaceLinks($input) {
        $content = mb_convert_encoding($input, 'HTML-ENTITIES', "UTF-8");

        $dom = new DOMDocument();
        @$dom->loadHTML($content);

        $elements = $dom->getElementsByTagName("a");

        $linksToReplace = [];

        foreach ($elements as $oldNode) {
            $href = $oldNode->getAttribute('href');

            $embedCode = $this->embedCodeFromUrl($href);

            if ($embedCode) {
                $newNode = $this->createElementFromHTML($embedCode, $dom);
                $importNode = $dom->importNode($newNode, true);
                $link = new stdClass();
                $link->oldNode = $oldNode;
                $link->newNode = $importNode;
                $linksToReplace[] = $link;
            }
        }
        foreach ($linksToReplace as $link) {
            $link->oldNode->parentNode->replaceChild($link->newNode, $link->oldNode);
        }
        return $this->getBodyContent($dom->saveHTML());
    }

    private function getBodyContent($html) {
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
                        ), $html));
    }

    public function embedCodeFromUrl($url) {
        $mediaEmbed = new MediaEmbed();
        $mediaObject = $mediaEmbed->parseUrl($url);
        if ($mediaObject) {
            $mediaObject->setAttribute([
                'class' => 'embed-media',
            ]);
            return $mediaObject->getEmbedCode();
        }
        return null;
    }

    private function createElementFromHTML($str, $dom) {
        $element = $dom->createElement("span");
        $this->appendHTML($element, $str);
        return $element;
    }

    private function appendHTML(DOMNode $parent, $html) {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML($html);
        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node, true);
            $parent->appendChild($node);
        }
    }

}
