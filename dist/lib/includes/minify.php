<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Exceptions\SCSSCompileException;
use App\HTML\Script;
use App\HTML\Style;
use App\Security\Hash;
use MatthiasMullie\Minify;
use ScssPhp\ScssPhp\Compiler;
use zz\Html\HTMLMinify;

/**
 * Clears the Javascript queue
 *
 * @return void
 */
function resetScriptQueue(): void {
    \App\Storages\Vars::set('script_queue', []);
}

/**
 * Minify HTML
 *
 * @param string $html
 * @param int $level
 *
 * @return string
 */
function optimizeHtml(
    string $html,
    int $level = HTMLMinify::OPTIMIZATION_SIMPLE
): string {
    if (Database::isConnected() && Settings::get('minify_html')) {
        $options = [
            'optimizationLevel' => $level
        ];
        $minifier = new HTMLMinify($html, $options);
        $html = $minifier->process();
    }

    $html = normalizeLN($html, "\n");
    return $html;
}

function enqueueScriptFile(string $path): void {
    if (! \App\Storages\Vars::get('script_queue')) {
        resetScriptQueue();
    }
    $script_queue = \App\Storages\Vars::get('script_queue');
    $script_queue[] = $path;

    \App\Storages\Vars::set('script_queue', $script_queue);
}

/**
 * Set SCSS import paths
 *
 * @param ?string[] $importPaths
 *
 * @return void
 */
function setSCSSImportPaths(?array $importPaths = null): void {
    if ($importPaths == null) {
        $importPaths = [
            Path::resolve('ULICMS_ROOT')
        ];
    }
    \App\Storages\Vars::set('css_include_paths', $importPaths);
}

/**
 * Get SCSS import paths
 *
 * @return ?string[]
 */
function getSCSSImportPaths(): ?array {
    return \App\Storages\Vars::get('css_include_paths');
}

/**
 * Unset SCSS import paths
 *
 * @return void
 */
function unsetSCSSImportPaths(): void {
    \App\Storages\Vars::delete('css_include_paths');
}

function minifyJs(): string {
    $scripts = \App\Storages\Vars::get('script_queue');
    $lastmod = 0;

    $minifier = new Minify\JS();

    // TODO: Methode erstellen: getLatestMtime()
    // returns the updated timestamp of the last changed file
    foreach ($scripts as $script) {
        $script = ltrim($script, '/');
        if (is_file($script)
                && pathinfo($script, PATHINFO_EXTENSION) == 'js'
                && filemtime($script) > $lastmod) {
            $lastmod = filemtime($script);
        }
    }

    $cacheId = Hash::hashCacheIdentifier((implode(';', $scripts)) . $lastmod);
    $jsDir = Path::resolve('ULICMS_GENERATED_PUBLIC/scripts');

    if (! is_dir($jsDir)) {
        mkdir($jsDir, 0777, true);
    }
    $jsUrl = ! is_admin_dir() ?
            'content/generated/public/scripts' : '../content/generated/public/scripts';

    $bundleFile = "{$jsDir}/{$cacheId}.js";
    $bundleUrl = "{$jsUrl}/{$cacheId}.js";

    $output = '';
    if (! is_file($bundleFile)) {
        foreach ($scripts as $script) {
            $script = ltrim($script, '/');
            if (is_file($script) &&
                    pathinfo($script, PATHINFO_EXTENSION) == 'js') {
                $minifier->add($script);
            }
        }

        $output = $minifier->minify();

        file_put_contents($bundleFile, $output);
    }
    resetScriptQueue();
    return $bundleUrl;
}

function minifyCSS(): string {
    $stylesheets = \App\Storages\Vars::get('stylesheet_queue');
    $lastmod = 0;

    $minifier = new Minify\CSS();

    // TODO: Methode erstellen: getLatestMtime()
    // returns the updated timestamp of the last changed file
    foreach ($stylesheets as $stylesheet) {
        $stylesheet = ltrim($stylesheet, '/');
        $type = pathinfo($stylesheet, PATHINFO_EXTENSION);
        if (is_file($stylesheet) && ($type == 'css' || $type == 'scss')
                && filemtime($stylesheet) > $lastmod) {
            $lastmod = filemtime($stylesheet);
        }
    }

    $cacheId = Hash::hashCacheIdentifier((implode(';', $stylesheets)) . $lastmod);

    $cssDir = Path::resolve('ULICMS_GENERATED_PUBLIC/stylesheets');

    if (! is_dir($cssDir)) {
        mkdir($cssDir, 0777, true);
    }

    $cssUrl = ! is_admin_dir() ?
            'content/generated/public/stylesheets' : '../content/generated/public/stylesheets';

    $bundleFile = "{$cssDir}/{$cacheId}.css";
    $bundleUrl = "{$cssUrl}/{$cacheId}.css";

    $output = '';

    if (! is_file($bundleFile)) {
        foreach ($stylesheets as $stylesheet) {
            $stylesheet = ltrim($stylesheet, '/');
            $type = pathinfo($stylesheet, PATHINFO_EXTENSION);
            if (is_file($stylesheet) && $type == 'css') {
                $minifier->add($stylesheet);
            } elseif (is_file($stylesheet) && $type == 'scss') {
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

function compileSCSS(string $stylesheet): string {
    $scss = new Compiler();

    $importPaths = getSCSSImportPaths();
    $scssInput = file_get_contents($stylesheet) ?: '';

    if (is_array($importPaths)) {
        $scss->setImportPaths($importPaths);
    } else {
        $scss->setImportPaths(dirname($stylesheet));
    }

    try {
        $scssOutput = $scss->compileString($scssInput)->getCSS();
    } catch (Exception $e) {
        throw new SCSSCompileException("Compilation of {$stylesheet} failed: "
                        . "{$e->getMessage()}");
    }
    return $scssOutput;
}

function compileSCSSToFile(string $stylesheet): string {
    $cssDir = Path::resolve('ULICMS_GENERATED_PUBLIC/stylesheets');

    if (! is_dir($cssDir)) {
        mkdir($cssDir, 0777, true);
    }

    $output = compileSCSS($stylesheet);

    $cacheId = Hash::hashCacheIdentifier($stylesheet . filemtime($stylesheet));

    $cssUrl = ! is_admin_dir() ?
            'content/generated/public/stylesheets' : '../content/generated/public/stylesheets';

    $bundleFile = "{$cssDir}/{$cacheId}.css";
    $bundleUrl = "{$cssUrl}/{$cacheId}.css";

    file_put_contents($bundleFile, $output);
    return $bundleUrl;
}

function combinedScriptHtml(): void {
    echo getCombinedScriptHtml();
}

function getCombinedScriptHtml(): string {
    $noMinify = isset($_ENV['NO_MINIFY']) && $_ENV['NO_MINIFY'];
    $html = '';

    if ($noMinify) {
        foreach (\App\Storages\Vars::get('script_queue') as $script) {
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
function resetStylesheetQueue(): void {
    \App\Storages\Vars::set('stylesheet_queue', []);
}

function enqueueStylesheet(string $path): void {
    if (! \App\Storages\Vars::get('stylesheet_queue')) {
        resetStylesheetQueue();
    }
    $stylesheet_queue = \App\Storages\Vars::get('stylesheet_queue');
    $stylesheet_queue[] = $path;

    \App\Storages\Vars::set('stylesheet_queue', $stylesheet_queue);
}

function getCombinedStylesheetHTML(): ?string {
    $html = '';

    if (! \App\Storages\Vars::get('stylesheet_queue')) {
        return null;
    }

    $noMinify = isset($_ENV['NO_MINIFY']) && $_ENV['NO_MINIFY'];

    if ($noMinify) {
        foreach (\App\Storages\Vars::get('stylesheet_queue') as $stylesheet) {
            $type = pathinfo($stylesheet, PATHINFO_EXTENSION);

            if ($type == 'css') {
                $html .= Style::fromExternalFile($stylesheet);
            } elseif ($type == 'scss') {
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

function combinedStylesheetHtml(): void {
    echo getCombinedStylesheetHTML();
}
