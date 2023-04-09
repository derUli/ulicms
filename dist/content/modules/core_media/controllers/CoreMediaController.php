<?php

declare(strict_types=1);

use MediaEmbed\MediaEmbed;

class CoreMediaController extends MainClass
{
    public function beforeContentFilter(string $input): string
    {
        $data = CustomData::get();

        $mediaEmbedEnabled = !(
            $data && isset($data["disable_media_embed"]) &&
            $data["disable_media_embed"]
        );

        $input = $mediaEmbedEnabled && !empty($input) ?
                $this->_replaceLinks($input) : $input;
        $input = $this->_addLazyLoad($input);

        return $input;
    }

    protected function _addLazyload($input)
    {
        if (empty($input)) {
            return $input;
        }

        $input = mb_convert_encoding($input, 'HTML-ENTITIES', "UTF-8");
        $dom = new DOMDocument();
        @$dom->loadHTML($input);

        if (Settings::get('lazy_loading_img', 'bool')) {
            foreach ($dom->getElementsByTagName('img') as $node) {
                if (!$node->getAttribute('loading')) {
                    $node->setAttribute('loading', 'lazy');
                }
            }
        }

        if (Settings::get('lazy_loading_iframe', 'bool')) {
            foreach ($dom->getElementsByTagName('iframe') as $node) {
                if (!$node->getAttribute('loading')) {
                    $node->setAttribute('loading', 'lazy');
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

    // This method replaces links to media services like youtube with embedded media
    public function _replaceLinks(string $input): string
    {
        if (empty($input)) {
            return $input;
        }

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
    protected function collectLinks(DOMDocument $dom): array
    {
        $elements = $dom->getElementsByTagName("a");
        $linksToReplace = [];

        foreach ($elements as $oldNode) {
            $href = $oldNode->getAttribute('href');
            $text = $oldNode->textContent;

            $embedCode = $this->embedCodeFromUrl($href);

            if ($href === $text && $embedCode) {
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
    protected function getBodyContent(string $html): string
    {
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
    protected function embedCodeFromUrl(string $url): ?string
    {
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
    protected function createElementFromHTML(
        string $str,
        DOMDocument $dom
    ): DOMElement {
        $element = $dom->createElement("span");
        $this->appendHTML($element, $str);
        return $element;
    }

    // This method appends html code to a DOMElement
    protected function appendHTML(DOMNode $parent, string $html): void
    {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML($html);
        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node, true);
            $parent->appendChild($node);
        }
    }
}
