<?php

class LazyLoadingController extends MainClass
{

    const MODULE_NAME = 'lazy_loading';

    public function afterContentFilter($html)
    {
        if (is_crawler() || AntiSpamHelper::checkForBot()) {
            return $html;
        }
        $html = $this->addLazyload($html);
        return $html;
    }

    public function frontendFooter()
    {
        $js1 = ModuleHelper::buildRessourcePath(self::MODULE_NAME, "js/jquery.lazy.js");
        $js2 = ModuleHelper::buildRessourcePath(self::MODULE_NAME, "js/apply-lazy-loading.js");
        
        enqueueScriptFile($js1);
        enqueueScriptFile($js2);
        
        combinedScriptHtml();
    }

    private function addLazyload($content)
    {
        $placeholderPic = ModuleHelper::buildRessourcePath(self::MODULE_NAME, "img/placeholder.jpg");
        $placeHolderPic = apply_filter($placeholderPic, "lazy_loading_placeholder_pic");
        
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        
        // Convert Images
        $images = [];
        
        foreach ($dom->getElementsByTagName('img') as $node) {
            $images[] = $node;
        }
        
        foreach ($images as $node) {
            $fallback = $node->cloneNode(true);
            
            $oldsrc = $node->getAttribute('src');
            $node->setAttribute('data-src', $oldsrc);
            $newsrc = $placeHolderPic;
            $node->setAttribute('src', $newsrc);
            
            $node->setAttribute('data-lazy', "true");
            
            $oldsrcset = $node->getAttribute('srcset');
            $node->setAttribute('data-srcset', $oldsrcset);
            $newsrcset = '';
            $node->setAttribute('srcset', $newsrcset);
            
            $classes = $node->getAttribute('class');
            $newclasses = $classes . ' lazy lazy-hidden';
            $node->setAttribute('class', $newclasses);
            
            $noscript = $dom->createElement('noscript', '');
            $node->parentNode->insertBefore($noscript, $node);
            $noscript->appendChild($fallback);
        }
        
        // Convert Videos
        $videos = [];
        
        foreach ($dom->getElementsByTagName('iframe') as $node) {
            $videos[] = $node;
        }
        
        foreach ($videos as $node) {
            $fallback = $node->cloneNode(true);
            
            $oldsrc = $node->getAttribute('src');
            $node->setAttribute('data-src', $oldsrc);
            $newsrc = '';
            $node->setAttribute('src', $newsrc);
            
            $classes = $node->getAttribute('class');
            $newclasses = $classes . ' lazy lazy-hidden';
            $node->setAttribute('class', $newclasses);
            
            $noscript = $dom->createElement('noscript', '');
            $node->parentNode->insertBefore($noscript, $node);
            $noscript->appendChild($fallback);
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
}