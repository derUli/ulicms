<?php
use UliCMS\HTML\Style;
use UliCMS\HTML\Script;

// FIXME: don't modify $_SERVER use the Vars class as replacement

// Javascript Minify Funktionen
function resetScriptQueue()
{
    $_SERVER["script_queue"] = array();
}

function enqueueScriptFile($path)
{
    if (! isset($_SERVER["script_queue"])) {
        $_SERVER["script_queue"] = array();
    }
    $_SERVER["script_queue"][] = $path;
}

function setSCSSImportPaths($importPaths = null)
{
    if ($importPaths == null) {
        $importPaths = array(
            Path::resolve("ULICMS_ROOT")
        );
    }
    $_SERVER["css_include_paths"] = $importPaths;
}

function getSCSSImportPaths()
{
    return is_array($_SERVER["css_include_paths"]) ? $_SERVER["css_include_paths"] : null;
}

function getCombinedScripts()
{
    $lastmod = intval($_GET["time"]);
    
    $output = "";
    if (isset($_GET["output_scripts"])) {
        $scripts = explode(";", $_GET["output_scripts"]);
        $adapter = CacheUtil::getAdapter(true);
        $cacheId = md5(get_request_uri());
        if ($adapter->has($cacheId)) {
            $output = $adapter->get($cacheId);
        } else {
            foreach ($scripts as $script) {
                $script = ltrim($script, "/");
                if (is_file($script)) {
                    $ext = pathinfo($script, PATHINFO_EXTENSION);
                    if ($ext == "js") {
                        $content = @file_get_contents($script);
                        if ($content) {
                            $content = normalizeLN($content, "\n");
                            $content = trim($content);
                            $content = \JShrink\Minifier::minify($content, array(
                                'flaggedComments' => false
                            ));
                            $lines = StringHelper::linesFromString($content, true, true, false);
                            $content = implode("\n", $lines);
                            $output .= $content;
                            $output .= "\n";
                            if (filemtime($script) > $lastmod) {
                                $lastmod = filemtime($script);
                            }
                        }
                    }
                }
            }
            $adapter->set($cacheId, $output);
        }
    }
    
    $output = trim($output);
    
    header("Content-Type: text/javascript");
    $len = mb_strlen($output, 'binary');
    header("Content-Length: " . $len);
    set_eTagHeaders($_GET["output_scripts"], $lastmod);
    echo $output;
    exit();
}

function combinedScriptHtml()
{
    echo getCombinedScriptHtml();
}

function combined_script_html()
{
    trigger_error("combined_script_html is deprecated", E_USER_DEPRECATED);
    echo getCombinedScriptHtml();
}

function getCombinedScriptHtml()
{
    $html = "";
    $cfg = new CMSConfig();
    if (is_true($cfg->no_minify)) {
        foreach ($_SERVER["script_queue"] as $script) {
            $html .= Script::fromFile($script);
        }
        resetScriptQueue();
        return $html;
    }
    
    if (isset($_SERVER["script_queue"]) and is_array($_SERVER["script_queue"]) and count($_SERVER["script_queue"]) > 0) {
        $html = Script::fromFile(getCombinedScriptURL());
    }
    resetScriptQueue();
    return $html;
}

function get_combined_script_html()
{
    trigger_error("combined_script_html is deprecated", E_USER_DEPRECATED);
    return getCombinedScriptHtml();
}

function getCombinedScriptURL()
{
    $output = "";
    
    $lastmod = 0;
    foreach ($_SERVER["script_queue"] as $file) {
        if (is_file($file) and endsWith($file, ".js", $needle) and filemtime($file) > $lastmod) {
            $lastmod = filemtime($file);
        }
    }
    
    if (isset($_SERVER["script_queue"]) and is_array($_SERVER["script_queue"])) {
        $files = implode(";", $_SERVER["script_queue"]);
        $url = "?output_scripts=" . $files;
    } else {
        $url = "index.php?scripts=";
    }
    
    $url .= "&time=" . $lastmod;
    
    return $url;
}

// Ab hier Stylesheet Funktionen
function resetStylesheetQueue()
{
    $_SERVER["stylesheet_queue"] = array();
}

function enqueueStylesheet($path)
{
    if (! isset($_SERVER["stylesheet_queue"])) {
        $_SERVER["stylesheet_queue"] = array();
    }
    $_SERVER["stylesheet_queue"][] = $path;
}

// FIXME: Seperate getter and output methods
function getCombinedStylesheets($doReturn = false)
{
    $output = "";
    
    $lastmod = intval($_GET["time"]);
    
    if (isset($_GET["output_stylesheets"])) {
        $stylesheets = explode(";", $_GET["output_stylesheets"]);
        $adapter = CacheUtil::getAdapter(true);
        $cacheId = md5(get_request_uri());
        if ($adapter->has($cacheId)) {
            $output = $adapter->get($cacheId);
        } else {
            $scss = new scssc();
            foreach ($stylesheets as $stylesheet) {
                $stylesheet = ltrim($stylesheet, "/");
                if (is_file($stylesheet)) {
                    $ext = pathinfo($stylesheet, PATHINFO_EXTENSION);
                    if ($ext == "css" || $ext == "scss") {
                        $content = @file_get_contents($stylesheet);
                        if ($content) {
                            $content = normalizeLN($content, "\n");
                            if ($ext == "scss") {
                                $importPaths = getSCSSImportPaths();
                                if (is_array($importPaths)) {
                                    $scss->setImportPaths($importPaths);
                                } else {
                                    $scss->setImportPaths(dirname($stylesheet));
                                }
                                $content = $scss->compile($content);
                            }
                            
                            $content = minifyCss($content);
                            $output .= $content;
                            $output .= "\r\n";
                            $output .= "\r\n";
                            if (filemtime($stylesheet) > $lastmod) {
                                $lastmod = filemtime($stylesheet);
                            }
                        }
                    }
                }
            }
            $adapter->set($cacheId, $output);
        }
    }
    
    $output = trim($output);
    
    // Remove comments
    $output = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $output);
    // Remove space after colons
    $output = str_replace(': ', ':', $output);
    // Remove whitespace
    $output = str_replace(array(
        "\r\n",
        "\r",
        "\n",
        "\t",
        '  ',
        '    ',
        '    '
    ), '', $output);
    
    if (! $doReturn) {
        header("Content-Type: text/css");
        $len = mb_strlen($output, 'binary');
        header("Content-Length: " . $len);
        
        set_eTagHeaders($_GET["output_stylesheets"], $lastmod);
        
        echo $output;
        exit();
    }
    return $output;
}

/**
 * This function takes a css-string and compresses it, removing
 * unneccessary whitespace, colons, removing unneccessary px/em
 * declarations etc.
 *
 * @param string $css
 * @return string compressed css content
 * @author Steffen Becker
 */
function minifyCss($css)
{
    // some of the following functions to minimize the css-output are directly taken
    // from the awesome CSS JS Booster: https://github.com/Schepp/CSS-JS-Booster
    // all credits to Christian Schaefer: http://twitter.com/derSchepp
    // remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // backup values within single or double quotes
    preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER);
    for ($i = 0; $i < count($hit[1]); $i ++) {
        $css = str_replace($hit[1][$i], '##########' . $i . '##########', $css);
    }
    // remove traling semicolon of selector's last property
    $css = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css);
    // remove any whitespace between semicolon and property-name
    $css = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css);
    // remove any whitespace surrounding property-colon
    $css = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css);
    // remove any whitespace surrounding selector-comma
    $css = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css);
    // remove any whitespace surrounding opening parenthesis
    $css = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css);
    // remove any whitespace between numbers and units
    $css = preg_replace('/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css);
    // shorten zero-values
    $css = preg_replace('/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css);
    // constrain multiple whitespaces
    $css = preg_replace('/\p{Zs}+/ims', ' ', $css);
    // remove newlines
    $css = str_replace(array(
        "\r\n",
        "\r",
        "\n"
    ), '', $css);
    // Restore backupped values within single or double quotes
    for ($i = 0; $i < count($hit[1]); $i ++) {
        $css = str_replace('##########' . $i . '##########', $hit[1][$i], $css);
    }
    return $css;
}

function combinedStylesheetHtml()
{
    echo getCombinedStylesheetHtml();
}

function combined_stylesheet_html()
{
    trigger_error("combined_stylesheel_html is deprecated", E_USER_DEPRECATED);
    echo combinedStylesheetHtml();
}

function getCombinedStylesheetHtml()
{
    $html = "";
    
    $cfg = new CMSConfig();
    if (is_true($cfg->no_minify)) {
        foreach ($_SERVER["stylesheet_queue"] as $stylesheet) {
            $html .= Style::FromExternalFile($stylesheet);
        }
        resetStylesheetQueue();
        return $html;
    }
    
    if (isset($_SERVER["stylesheet_queue"]) and is_array($_SERVER["stylesheet_queue"]) and count($_SERVER["stylesheet_queue"]) > 0) {
        $html = Style::FromExternalFile(getCombinedStylesheetURL());
    }
    resetStylesheetQueue();
    return $html;
}

function get_combined_stylesheet_html()
{
    trigger_error("get_combined_stylesheet_html is deprecated", E_USER_DEPRECATED);
    return getCombinedStylesheetHtml();
}

function getCombinedStylesheetURL()
{
    $lastmod = 0;
    foreach ($_SERVER["stylesheet_queue"] as $file) {
        if (is_file($file) and (endsWith($file, ".css", $needle) or endsWith($file, ".scss", $needle)) and filemtime($file) > $lastmod) {
            $lastmod = filemtime($file);
        }
    }
    
    $output = "";
    if (isset($_SERVER["stylesheet_queue"]) and is_array($_SERVER["stylesheet_queue"])) {
        $files = implode(";", $_SERVER["stylesheet_queue"]);
        $url = "?output_stylesheets=" . $files;
    } else {
        $url = "index.php?stylesheets=";
    }
    $url .= "&time=$lastmod";
    return $url;
}