<?php

use MediaEmbed\MediaEmbed;

class CoreMediaEmbedController extends MainClass {

    public function beforeContentFilter($input) {
        $data = CustomData::get();

        $mediaEmbedEnabled = !($data and is_true($data["disable_media_embed"]));

        return $mediaEmbedEnabled ? $this->replaceLinks($input) : $input;
    }

    public function replaceLinks($input) {
        $content = mb_convert_encoding($input, 'HTML-ENTITIES', "UTF-8");
        $dom = new DOMDocument();
        @$dom->loadHTML($content);

        $linksToReplace = [];

        $elements = $dom->getElementsByTagName("a");

        for ($i = 0; $i < $elements->count(); $i++) {
            $oldNode = $elements->item($i);
            $href = $oldNode->getAttribute('href');
            $text = $oldNode->textContent;

            if ($href !== $text) {
                continue;
            }

            $url = $text;
            $embedCode = $this->embedCodeFromUrl($url);
            if ($embedCode) {
                $newNode = $this->createElementFromHTML($embedCode, $dom);
                $importNode = $dom->importNode($newNode, true);
                $oldNode->parentNode->replaceChild($importNode, $oldNode);
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
            $mediaObject->setAttribute([
                'class' => 'embed-media',
            ]);
            return $mediaObject->getEmbedCode();
        }
        return null;
    }

    public function createElementFromHTML($str, $dom) {
        $element = $dom->createElement("span");
        $this->appendHTML($element, $str);
        return $element;
    }

    function appendHTML(DOMNode $parent, $html) {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML($html);
        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node, true);
            $parent->appendChild($node);
        }
    }

}
