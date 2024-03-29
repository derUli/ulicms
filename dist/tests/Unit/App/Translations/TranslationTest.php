<?php

use App\Translations\Translation;

class TranslationTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        require_once getLanguageFilePath('en');
        Translation::loadAllModuleLanguageFiles('en');
        Translation::loadCurrentThemeLanguageFiles('en');
    }

    public function testGetTranslation(): void {
        $this->assertEquals(
            'Type your password',
            get_translation('enter_pass')
        );
    }

    public function testGetTranslationWithPlaceholders(): void {
        $this->assertEquals(
            'Hello John Doe!',
            get_translation(
                'hello_name',
                [
                    '%name%' => 'John Doe'
                ]
            )
        );
    }

    public function testGetSecureTranslation(): void {
        $this->assertEquals(
            normalizeLN(_esc('<p>Um die Performance der Website zu verbessern,
bietet das UliCMS eine Cache-Funktion.<br/>
Statische Seiten, die keine Module enthalten, werden einmalig generiert und dann im cache-Ordner zwischengespeichert.
Anschließend werden statt die Inhalte immer wieder aus der Datenbank zu laden, die Inhalte aus den gespeicherten HTML-Dateien geladen.</p>')),
            normalizeLN(get_secure_translation('CACHE_TEXT1'))
        );
    }

    public function testSecureTranslation(): void {
        ob_start();
        secure_translation('CACHE_TEXT1');

        $this->assertEquals(
            normalizeLN(_esc('<p>Um die Performance der Website zu verbessern,
bietet das UliCMS eine Cache-Funktion.<br/>
Statische Seiten, die keine Module enthalten, werden einmalig generiert und dann im cache-Ordner zwischengespeichert.
Anschließend werden statt die Inhalte immer wieder aus der Datenbank zu laden, die Inhalte aus den gespeicherten HTML-Dateien geladen.</p>')),
            normalizeLN(ob_get_clean())
        );
    }

    public function testSecureTranslate(): void {
        ob_start();
        secure_translate('CACHE_TEXT1');

        $this->assertEquals(
            normalizeLN(_esc('<p>Um die Performance der Website zu verbessern,
bietet das UliCMS eine Cache-Funktion.<br/>
Statische Seiten, die keine Module enthalten, werden einmalig generiert und dann im cache-Ordner zwischengespeichert.
Anschließend werden statt die Inhalte immer wieder aus der Datenbank zu laden, die Inhalte aus den gespeicherten HTML-Dateien geladen.</p>')),
            normalizeLN(ob_get_clean())
        );
    }

    public function testGetSecureTranslationWithPlaceholders(): void {
        $this->assertEquals(
            _esc("Hello <script>alert('xss');</script>John " .
                    'Doe!'),
            get_secure_translation(
                'hello_name',
                [
                    '%name%' => "<script>alert('xss');</script>John Doe",
                ]
            )
        );
    }

    public function test_T(): void {
        $this->assertEquals(
            'Type your password',
            _t('enter_pass')
        );
    }

    public function test_TWithPlaceholders(): void {
        $this->assertEquals(
            'Hello John Doe!',
            _t(
                'hello_name',
                [
                    '%name%' => 'John Doe'
                ]
            )
        );
    }

    public function testT(): void {
        ob_start();
        t('enter_pass');
        $output = ob_get_clean();
        $this->assertEquals('Type your password', $output);
    }

    public function testTWithPlaceholders(): void {
        ob_start();
        t(
            'hello_name',
            [
                '%name%' => 'John Doe'
            ]
        );
        $output = ob_get_clean();
        $this->assertEquals('Hello John Doe!', $output);
    }

    public function testSingularOrPluralWith0ExpectSingular(): void {
        $this->assertEquals(
            '0 Katzen',
            singularOrPlural(0, '%number% Katze', '%number% Katzen')
        );
    }

    public function testSingularOrPluralWith1ExpectSingular(): void {
        $this->assertEquals('1 Hund', singularOrPlural(1, '%number% Hund', '%number% Hunde'));
    }

    public function testSingularOrPluralWith3ExpectSingular(): void {
        $this->assertEquals('3 Biere', singularOrPlural(3, '%number% Bier', '%number% Biere'));
    }

    public function testGetTranslationNotFoundReturnsKey(): void {
        $this->assertEquals('not_a_translation', get_translation('not_a_translation'));
    }

    public function testTranslate(): void {
        ob_start();
        translate('pages');
        $this->assertEquals('Pages', ob_get_clean());
    }

    public function testTranslation(): void {
        ob_start();
        translation('pages');
        $this->assertEquals('Pages', ob_get_clean());
    }
}
