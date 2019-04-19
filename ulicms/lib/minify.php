<?php

use UliCMS\HTML\Style;
use UliCMS\HTML\Script;
use UliCMS\Exceptions\SCSSCompileException;
use Leafo\ScssPhp\Compiler;
use zz\Html\HTMLMinify;
use MatthiasMullie\Minify;

function resetScriptQueue() {
    Vars::set("script_queue", array());
}

function optimizeHtml($html) {
    if (Database::isConnected() and Settings::get("minify_html")) {
        $options = array(
            'optimizationLevel' => HTMLMinify::OPTIMIZATION_SIMPLE
        );
        $minifier = new HTMLMinify($html, $options);
        $html = $minifier->process();
    }

    $html = normalizeLN($html, "\n");
    return $html;
}

function enqueueScriptFile($path) {
    if (!Vars::get("script_queue")) {
        resetScriptQueue();
    }
    $script_queue = Vars::get("script_queue");
    $script_queue[] = $path;

    Vars::set("script_queue", $script_queue);
}

function setSCSSImportPaths($importPaths = null) {
    if ($importPaths == null) {
        $importPaths = array(
            Path::resolve("ULICMS_ROOT")
        );
    }
    Vars::set("css_include_paths", $importPaths);
}

function getSCSSImportPaths() {
    return Vars::get("css_include_paths");
}

function unsetSCSSImportPaths() {
    Vars::delete("css_include_paths");
}

function minifyJs() {
    $scripts = Vars::get("script_queue");
    $lastmod = 0;

    $minifier = new Minify\JS();

    // TODO: Methode erstellen: getLatestMtime()
    // returns the updated timestamp of the last changed file
    foreach ($scripts as $script) {
        $script = ltrim($script, "/");
        if (is_file($script) and pathinfo($script, PATHINFO_EXTENSION) == "js"
                and filemtime($script) > $lastmod) {
            $lastmod = filemtime($script);
        }
    }

    $cacheId = md5((implode(";", $scripts)) . $lastmod);
    $jsDir = Path::resolve("ULICMS_ROOT/content/cache/scripts");

    if (!is_dir($jsDir)) {
        mkdir($jsDir, 0777, true);
    }
    $jsUrl = !is_admin_dir() ? "content/cache/scripts" : "../content/cache/scripts";

    $bundleFile = "{$jsDir}/{$cacheId}.js";
    $bundleUrl = "{$jsUrl}/{$cacheId}.js";

    $output = "";
    if (!is_file($bundleFile)) {
        foreach ($scripts as $script) {
            $script = ltrim($script, "/");
            if (is_file($script) and pathinfo($script, PATHINFO_EXTENSION) == "js") {
                $minifier->add($script);
            }
        }

        $output = $minifier->minify();

        file_put_contents($bundleFile, $output);
    }
    resetScriptQueue();
    return $bundleUrl;
}

function minifyCSS() {
    $stylesheets = Vars::get("stylesheet_queue");
    $lastmod = 0;

    $minifier = new Minify\CSS();

    // TODO: Methode erstellen: getLatestMtime()
    // returns the updated timestamp of the last changed file
    foreach ($stylesheets as $stylesheet) {
        $stylesheet = ltrim($stylesheet, "/");
        $type = pathinfo($stylesheet, PATHINFO_EXTENSION);
        if (is_file($stylesheet) and ( $type == "css" or $type == "scss")
                and filemtime($stylesheet) > $lastmod) {
            $lastmod = filemtime($stylesheet);
        }
    }

    $cacheId = md5((implode(";", $stylesheets)) . $lastmod);
    $cssDir = Path::resolve("ULICMS_ROOT/content/cache/stylesheets");

    if (!is_dir($cssDir)) {
        mkdir($cssDir, 0777, true);
    }
    $cssUrl = !is_admin_dir() ? "content/cache/stylesheets" : "../content/cache/stylesheets";

    $bundleFile = "{$cssDir}/{$cacheId}.css";
    $bundleUrl = "{$cssUrl}/{$cacheId}.css";

    $output = "";
    if (!is_file($bundleFile)) {
        foreach ($stylesheets as $stylesheet) {
            $stylesheet = ltrim($stylesheet, "/");
            $type = pathinfo($stylesheet, PATHINFO_EXTENSION);
            if (is_file($stylesheet) and $type == "css") {
                $minifier->add($stylesheet);
            } else if (is_file($stylesheet) and $type == "scss") {
                $scssOutput = compileSCSS($stylesheet);
                $minifier->add($scssOutput);
            }
        }

        $output = $minifier->minify();

        file_put_contents($bundleFile, $output);
    }
    resetStylesheetQueue();
    return $bundleUrl;
}

function compileSCSS($stylesheet) {
    $scss = new Compiler();

    $importPaths = getSCSSImportPaths();
    $scssInput = file_get_contents($stylesheet);
    if (is_array($importPaths)) {
        $scss->setImportPaths($importPaths);
    } else {
        $scss->setImportPaths(dirname($stylesheet));
    }

    try {
        $scssOutput = $scss->compile($scssInput);
    } catch (Exception $e) {
        throw new SCSSCompileException("Compilation of $stylesheet failed: {$e->getMessage()}");
    }
    return $scssOutput;
}

function compileSCSSToFile($stylesheet) {
    $cssDir = Path::resolve("ULICMS_ROOT/content/cache/stylesheets");

    if (!is_dir($cssDir)) {
        mkdir($cssDir, 0777, true);
    }

    $output = compileSCSS($stylesheet);

    $cacheId = md5($stylesheet . filemtime($stylesheet)) . ".css";

    $cssUrl = !is_admin_dir() ? "content/cache/stylesheets" : "../content/cache/stylesheets";

    $bundleFile = "{$cssDir}/{$cacheId}.css";
    $bundleUrl = "{$cssUrl}/{$cacheId}.css";

    file_put_contents($bundleFile, $output);
    return $bundleUrl;
}

function combinedScriptHtml() {
    echo getCombinedScriptHtml();
}

function combined_script_html() {
    trigger_error("combined_script_html is deprecated", E_USER_DEPRECATED);
    echo getCombinedScriptHtml();
}

function getCombinedScriptHtml() {

    $cfg = new CMSConfig();
    if (is_true($cfg->no_minify)) {
        foreach (Vars::get("script_queue") as $script) {
            $html .= Script::fromFile($script);
        }
        resetScriptQueue();
        return $html;
    }

    $html = Script::fromFile(minifyJs());

    resetScriptQueue();
    return $html;
}

function get_combined_script_html() {
    trigger_error("combined_script_html is deprecated", E_USER_DEPRECATED);
    return getCombinedScriptHtml();
}

// Ab hier Stylesheet Funktionen
function resetStylesheetQueue() {
    Vars::set("stylesheet_queue", array());
}

function enqueueStylesheet($path) {
    if (!Vars::get("stylesheet_queue")) {
        resetStylesheetQueue();
    }
    $stylesheet_queue = Vars::get("stylesheet_queue");
    $stylesheet_queue[] = $path;

    Vars::set("stylesheet_queue", $stylesheet_queue);
}

function getCombinedStylesheetHTML() {
    $cfg = new CMSConfig();
    if (is_true($cfg->no_minify)) {
        foreach (Vars::get("stylesheet_queue") as $stylesheet) {
            $type = pathinfo($stylesheet, PATHINFO_EXTENSION);
            if ($type == "css") {
                $html .= Style::FromExternalFile($stylesheet);
            } else if ($type == "scss") {
                $html .= Style::FromExternalFile(compileSCSSToFile($stylesheet));
            }
        }
        resetStylesheetQueue();
        return $html;
    }

    $html = Style::FromExternalFile(minifyCSS());

    resetStylesheetQueue();
    return $html;
}

function combinedStylesheetHtml() {
    echo getCombinedStylesheetHTML();
}

function get_combined_stylesheet_html() {
    trigger_error("get_combined_stylesheet_html is deprecated", E_USER_DEPRECATED);
    return getCombinedStylesheetHTML();
}
