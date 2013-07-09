<?php 
function getSearchString($strUrl) {
    $strUrl = rawurldecode($strUrl);
    $arrSUMA = array(
                'google' => 'q',
                'alltheweb' => 'query',
                'altavista' => 'q',
                'aol' => 'query',
                'excite' => 'search',
                'hotbot' => 'query',
                'lycos' => 'query',
                'yahoo' => 'p',
                't-online' => 'q',
                'msn' => 'q',
                'bing' => 'q',
                'netscape' => 'search',
                'web' => 'su'
               );
    $arrUrl = parse_url($strUrl);
    if (!isset($arrUrl['host']) || !trim(@$arrUrl['host'])) {
        return null;
    }
    $strSearchWord = '';
    foreach ($arrSUMA AS $strKey => $strValue) {
        if (preg_match('#'.$strKey.'#i',$arrUrl['host'])) {
            $arrQuery = parse_str($arrUrl['query'], $arrQueryData);
            if (isset($arrQueryData[$strValue])) {
                $strSearchWord = mysql_escape_string($arrQueryData[$strValue]);
            }
        }
    }
    if (isset($arrUrl['path']) && trim($arrUrl['path']) && isset($arrUrl['query']) && trim($arrUrl['query'])) {
        $arrUrl['query'] = '?'.$arrUrl['query'];
    }
    if (isset($arrUrl['query']) && preg_match('#Ãƒ#i',$arrUrl['query'])) {
        $arrUrl['query'] = utf8_decode($arrUrl['query']);
    }
    $arrUrl['query'] = mysql_escape_string($arrUrl['query']);
    if (preg_match('#Ãƒ#i',$strSearchWord)) {
        $strSearchWord = utf8_decode($strSearchWord);
    }
    return $strSearchWord;
}

// Funktionsaufruf
echo getSearchString($_SERVER['HTTP_REFERER']);
?>