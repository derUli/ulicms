<?php

use MediaEmbed\MediaEmbed;

class CoreMediaController extends MainClass {
	
    public function beforeContentFilter($input) {
        $data = CustomData::get();

        $mediaEmbedEnabled = !($data and is_true($data["disable_media_embed"]));

        return $mediaEmbedEnabled ? $this->replaceLinks($input) : $input;
    }

    // This method replaces links to media services like youtube with embedded media
    public function replaceLinks($input) {
        $content = mb_convert_encoding($input, 'HTML-ENTITIES', "UTF-8");

        $dom = new DOMDocument();
        @$dom->loadHTML($content);

        $linksToReplace = $this->collectLinks($dom);
        foreach ($linksToReplace as $link) {
            $link->oldNode->parentNode->replaceChild($link->newNode, $link->oldNode);
        }
        return $this->getBodyContent($dom->saveHTML());
    }

    // this method collect all embedable links and return it including
    // a replacement node containg the embed element
    protected function collectLinks($dom) {

        $elements = $dom->getElementsByTagName("a");
        $linksToReplace = [];

        foreach ($elements as $oldNode) {
            $href = $oldNode->getAttribute('href');
            $text = $oldNode->textContent;

            $embedCode = $this->embedCodeFromUrl($href);

            if ($href === $text and $embedCode) {
                $newNode = $this->createElementFromHTML($embedCode, $dom);
                $importNode = $dom->importNode($newNode, true);
                $link = new stdClass();
                $link->oldNode = $oldNode;
                $link->newNode = $importNode;
                $linksToReplace[] = $link;
            }
        }
        return $linksToReplace;
    }

    // saveHTML() on DOMDocument returns a full valid html document
    // This method extracts the content of the body
    protected function getBodyContent($html) {
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

    // This method retrieves the embed code for an URL
    protected function embedCodeFromUrl($url) {
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

    // This method creates a dom node from an html element
    protected function createElementFromHTML($str, $dom) {
        $element = $dom->createElement("span");
        $this->appendHTML($element, $str);
        return $element;
    }

    // This method appends html code to a DOMElement
    protected function appendHTML(DOMNode $parent, $html) {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML($html);
        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node, true);
            $parent->appendChild($node);
        }
    }

}
