<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Controllers\MainClass;
use MediaEmbed\MediaEmbed;

/**
 * This controller handles media replacement
 */
class CoreMediaController extends MainClass
{
    /**
     * This is applied to content by event
     *
     * @param string $input HTML input string
     *
     * @return string Processed HTML string
     */
    public function beforeContentFilter(string $input): string
    {
        $data = CustomData::get();

        // Check if media replacement is not disabled in CustomData JSON
        $mediaEmbedEnabled = ! (
            $data && isset($data['disable_media_embed']) &&
            $data['disable_media_embed']
        );

        // Replace links with embedded media
        $input = $mediaEmbedEnabled && ! empty($input) ?
                $this->_replaceLinks($input) : $input;

        // Add loading="lazy" to HTML elements
        $input = $this->_addLazyLoad($input);

        return $input;
    }

    /**
     * Replace links with embedded codes
     *
     * @param string $input HTML input string
     *
     * @return string Processed HTML string
     */
    public function _replaceLinks(string $input): string
    {
        if (empty($input)) {
            return $input;
        }

        $content = mb_convert_encoding($input, 'HTML-ENTITIES', 'UTF-8');

        $dom = new DOMDocument();
        @$dom->loadHTML($content);

        $linksToReplace = $this->collectLinks($dom);

        foreach ($linksToReplace as $link) {
            $link->oldNode->parentNode->replaceChild($link->newNode, $link->oldNode);
        }

        $savedHtml = $dom->saveHTML() ?: $input;
        return $this->getBodyContent($savedHtml);
    }

    /**
     * Add HTML5 lazy loading to elements
     *
     * @param string $input HTML input string
     *
     * @return string Processed HTML string
     */
    protected function _addLazyload(string $input): string
    {
        if (empty($input)) {
            return $input;
        }

        $input = mb_convert_encoding($input, 'HTML-ENTITIES', 'UTF-8');
        $dom = new DOMDocument();
        @$dom->loadHTML($input);

        // Apply lazy loading to images if enabled
        if (Settings::get('lazy_loading_img', 'bool')) {
            foreach ($dom->getElementsByTagName('img') as $node) {
                if (! $node->getAttribute('loading')) {
                    $node->setAttribute('loading', 'lazy');
                }
            }
        }

        // Apply lazy loading to iframes if enabled
        if (Settings::get('lazy_loading_iframe', 'bool')) {
            foreach ($dom->getElementsByTagName('iframe') as $node) {
                if (! $node->getAttribute('loading')) {
                    $node->setAttribute('loading', 'lazy');
                }
            }
        }

        $savedHtml = $dom->saveHTML() ?: $input;

        $newHtml = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace([
            '<html>',
            '</html>',
            '<body>',
            '</body>'
        ], [
            '',
            '',
            '',
            ''
        ], $savedHtml));
        return $newHtml ?? '';
    }

    /**
     * Collect all links in DOMDocument
     *
     * @param DOMDocument $dom
     *
     * @return array
     */
    protected function collectLinks(DOMDocument $dom): array
    {
        $elements = $dom->getElementsByTagName('a');
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

    /**
     *  saveHTML() on DOMDocument returns a full valid html document
     *  However we need only the content of <body>
     *
     * @param string $html HTML input
     *
     * @return string content of <body>
     */
    protected function getBodyContent(string $html): string
    {
        $match = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace([
            '<html>',
            '</html>',
            '<body>',
            '</body>'
        ], [
            '',
            '',
            '',
            ''
        ], $html));

        return $match ? $match : $html;
    }

    /**
     * Generated embed code from Url
     *
     * @param string $url
     *
     * @return ?string HTML embed code or null
     */
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

    /**
     * This method converts a HTML string to a DOMElement
     *
     * @param string $str HTML string
     * @param DOMDocument $dom DomDocument to use
     *
     * @return DOMElement
     */
    protected function createElementFromHTML(
        string $str,
        DOMDocument $dom
    ): DOMElement {
        // Since a root element is required, create a <span> and append the embed HTML code inside
        $element = $dom->createElement('span');
        $this->appendHTML($element, $str);

        return $element;
    }

    /**
     * Append raw HTML code to a DOMDocument
     *
     * @param DOMNode $parent DOMNode to use
     * @param string $html HTML String to apply
     *
     * @return void
     */
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
