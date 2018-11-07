<?php

class UrlExtractor
{

    public function fromString($string)
    {
        $regex = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
        preg_match_all($regex, $string, $matches);
        $urls = array_unique($matches[0]);
        $urls = array_values($urls);
        return $urls;
    }
}