<?php

use Michelf\MarkdownExtra;

class InfoController extends MainClass
{
    public const CHANGELOG_URL = 'https://raw.githubusercontent.com/derUli/ulicms/master/doc/changelog.txt';

    public function _fetchChangelog()
    {
        $lines = $this->_getChangelogContent();
        $lines = array_map('trim', $lines);
        $lines = array_map('_esc', $lines);
        $lines = array_map('make_links_clickable', $lines);

        $lines = array_map(function ($line) {
            if (str_starts_with($line, '+') || str_starts_with($line, '*') ||
                    str_starts_with($line, '-') || str_starts_with($line, '# ') ||
                    str_starts_with($line, '*')) {
                $line = '&bull;&nbsp;' . substr($line, 1);
            } elseif (str_starts_with($line, '=') && str_ends_with($line, '=')) {
                $line = '<h3>' . trim(trim($line, '=')) . '</h3>';
            } elseif (str_ends_with($line, ':')) {
                $line = "<strong>{$line}</strong>";
            }
            if (! str_contains($line, '<h')) {
                $line .= "\n";
            }

            return $line;
        }, $lines);

        $lines = array_filter($lines, 'trim');
        $lines = array_filter($lines, 'strlen');
        $text = nl2br(implode('', $lines));
        return $text ? trim($text) : get_translation('fetch_failed');
    }

    public function _getChangelogContent(): array
    {
        $file = ModuleHelper::buildModuleRessourcePath(
            'core_info',
            'changelog.txt'
        );

        $content = is_file($file) ?
                file_get_contents($file) :
                file_get_contents_wrapper(self::CHANGELOG_URL);

        return explode("\n", $content);
    }

    public function _getComposerLegalInfo(): string
    {
        $legalFile = Path::resolve('ULICMS_ROOT/licenses.md');
        $lastModified = filemtime($legalFile);

        $cacheFile = Path::resolve("ULICMS_CACHE/legal-{$lastModified}.html");

        if (is_file($cacheFile)) {
            return file_get_contents($cacheFile);
        }

        $legalText = file_get_contents($legalFile);

        $parser = new MarkdownExtra();
        $parser->hard_wrap = true;
        $parsed = $parser->transform($legalText);

        $parsed = str_replace(
            '<h1>Project Licenses</h1>',
            '<h1>' . get_translation('legal_composer') . '</h1>',
            $parsed
        );

        // Strip new lines from response
        $parsed = str_replace("\r\n", '', $parsed);
        $parsed = str_replace("\n", '', $parsed);

        file_put_contents($cacheFile, $parsed);

        return $parsed;
    }

    public function _getNpmLegalInfo(): array
    {
        $legalJson = file_get_contents(
            Path::resolve('ULICMS_ROOT/licenses.json')
        );

        return json_decode($legalJson);
    }
}
