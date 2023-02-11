<?php

declare(strict_types=1);

use App\HTML\Style;
use App\HTML\Script;
use App\Exceptions\SCSSCompileException;
use ScssPhp\ScssPhp\Compiler;
use zz\Html\HTMLMinify;
use MatthiasMullie\Minify;

function resetScriptQueue(): void
{
    Vars::set("script_queue", []);
}

function optimizeHtml(
    string $html,
    int $level = HTMLMinify::OPTIMIZATION_SIMPLE
): string {
    if (Database::isConnected() and Settings::get("minify_html")) {
        $options = array(
            'optimizationLevel' => $level
        );
        $minifier = new HTMLMinify($html, $options);
        $html = $minifier->process();
    }

    $html = normalizeLN($html, "\n");
    return $html;
}

function enqueueScriptFile($path): void
{
    if (!Vars::get("script_queue")) {
        resetScriptQueue();
    }
    $script_queue = Vars::get("script_queue");
    $script_queue[] = $path;

    Vars::set("script_queue", $script_queue);
}

function setSCSSImportPaths(?array $importPaths = null): void
{
    if ($importPaths == null) {
        $importPaths = array(
            Path::resolve("ULICMS_ROOT")
        );
    }
    Vars::set("css_include_paths", $importPaths);
}

function getSCSSImportPaths(): ?array
{
    return Vars::get("css_include_paths");
}

function unsetSCSSImportPaths(): void
{
    Vars::delete("css_include_paths");
}

function minifyJs(): string
{
    $scripts = Vars::get("script_queue");
    $lastmod = 0;

    $minifier = new Minify\JS();

    // TODO: Methode erstellen: getLatestMtime()
    // returns the updated timestamp of the last changed file
    foreach ($scripts as $script) {
        $script = ltrim($script, "/");
        if (is_file($script)
                and pathinfo($script, PATHINFO_EXTENSION) == "js"
                and filemtime($script) > $lastmod) {
            $lastmod = filemtime($script);
        }
    }

    $cacheId = md5((implode(";", $scripts)) . $lastmod);
    $jsDir = Path::resolve("ULICMS_CACHE/scripts");

    if (!is_dir($jsDir)) {
        mkdir($jsDir, 0777, true);
    }
    $jsUrl = !is_admin_dir() ?
            "content/cache/legacy/scripts" : "../content/cache/legacy/scripts";

    $bundleFile = "{$jsDir}/{$cacheId}.js";
    $bundleUrl = "{$jsUrl}/{$cacheId}.js";

    $output = "";
    if (!is_file($bundleFile)) {
        foreach ($scripts as $script) {
            $script = ltrim($script, "/");
            if (is_file($script)
                    and pathinfo($script, PATHINFO_EXTENSION) == "js") {
                $minifier->add($script);
            }
        }

        $output = $minifier->minify();

        file_put_contents($bundleFile, $output);
    }
    resetScriptQueue();
    return $bundleUrl;
}

function minifyCSS(): string
{
    $stylesheets = Vars::get("stylesheet_queue");
    $lastmod = 0;

    $minifier = new Minify\CSS();

    // TODO: Methode erstellen: getLatestMtime()
    // returns the updated timestamp of the last changed file
    foreach ($stylesheets as $stylesheet) {
        $stylesheet = ltrim($stylesheet, "/");
        $type = pathinfo($stylesheet, PATHINFO_EXTENSION);
        if (is_file($stylesheet) && ($type == "css" or $type == "scss")
                and filemtime($stylesheet) > $lastmod) {
            $lastmod = filemtime($stylesheet);
        }
    }

    $cacheId = md5((implode(";", $stylesheets)) . $lastmod);

    $cssDir = Path::resolve("ULICMS_CACHE/stylesheets");

    if (!is_dir($cssDir)) {
        mkdir($cssDir, 0777, true);
    }
    $cssUrl = !is_admin_dir() ?
            "content/cache/legacy/stylesheets" : "../content/cache/legacy/stylesheets";

    $bundleFile = "{$cssDir}/{$cacheId}.css";
    $bundleUrl = "{$cssUrl}/{$cacheId}.css";

    $output = "";

    if (!is_file($bundleFile)) {
        foreach ($stylesheets as $stylesheet) {
            $stylesheet = ltrim($stylesheet, "/");
            $type = pathinfo($stylesheet, PATHINFO_EXTENSION);
            if (is_file($stylesheet) and $type == "css") {
                $minifier->add($stylesheet);
            } elseif (is_file($stylesheet) and $type == "scss") {
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

function compileSCSS(string $stylesheet): string
{
    $scss = new Compiler();

    $importPaths = getSCSSImportPaths();
    $scssInput = file_get_contents($stylesheet);
    if (is_array($importPaths)) {
        $scss->setImportPaths($importPaths);
    } else {
        $scss->setImportPaths(dirname($stylesheet));
    }

    try {
        $scssOutput = $scss->compileString($scssInput)->getCSS();
    } catch (Exception $e) {
        throw new SCSSCompileException("Compilation of $stylesheet failed: "
                        . "{$e->getMessage()}");
    }
    return $scssOutput;
}

function compileSCSSToFile(string $stylesheet): string
{
    $cssDir = Path::resolve("ULICMS_CACHE/stylesheets");

    if (!is_dir($cssDir)) {
        mkdir($cssDir, 0777, true);
    }

    $output = compileSCSS($stylesheet);

    $cacheId = md5($stylesheet . filemtime($stylesheet)) . ".css";

    $cssUrl = !is_admin_dir() ?
            "content/cache/stylesheets" : "../content/cache/stylesheets";

    $bundleFile = "{$cssDir}/{$cacheId}.css";
    $bundleUrl = "{$cssUrl}/{$cacheId}.css";

    file_put_contents($bundleFile, $output);
    return $bundleUrl;
}

function combinedScriptHtml(): void
{
    echo getCombinedScriptHtml();
}

function getCombinedScriptHtml(): string
{
    $html = "";
    $cfg = new CMSConfig();
    if (isset($cfg->no_minify) and is_true($cfg->no_minify)) {
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

// Ab hier Stylesheet Funktionen
function resetStylesheetQueue(): void
{
    Vars::set("stylesheet_queue", []);
}

function enqueueStylesheet(string $path): void
{
    if (!Vars::get("stylesheet_queue")) {
        resetStylesheetQueue();
    }
    $stylesheet_queue = Vars::get("stylesheet_queue");
    $stylesheet_queue[] = $path;

    Vars::set("stylesheet_queue", $stylesheet_queue);
}

function getCombinedStylesheetHTML(): ?string
{
    $html = "";

    $cfg = new CMSConfig();
    if (!Vars::get("stylesheet_queue")) {
        return null;
    }
    if (isset($cfg->no_minify) and is_true($cfg->no_minify)) {
        foreach (Vars::get("stylesheet_queue") as $stylesheet) {
            $type = pathinfo($stylesheet, PATHINFO_EXTENSION);
            if ($type == "css") {
                $html .= Style::fromExternalFile($stylesheet);
            } elseif ($type == "scss") {
                $html .= Style::fromExternalFile(
                    compileSCSSToFile($stylesheet)
                );
            }
        }
        resetStylesheetQueue();
        return $html;
    }

    $html = Style::fromExternalFile(minifyCSS());

    resetStylesheetQueue();
    return $html;
}

function combinedStylesheetHtml(): void
{
    echo getCombinedStylesheetHTML();
}
