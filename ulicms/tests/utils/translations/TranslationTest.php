<?php

class TranslationTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        Translation::init();
        require_once getLanguageFilePath("en");
        Translation::loadAllModuleLanguageFiles("en");
        Translation::loadCurrentThemeLanguageFiles("en");
        Translation::includeCustomLangFile("en");
    }

    public function testGetTranslation() {
        Translation::init();
        $this->assertEquals("Type your password",
                get_translation("enter_pass"));
    }

    public function testGetTranslationWithPlaceholders() {
        $this->assertEquals("Hello John Doe!",
                get_translation("hello_name",
                        [
                            "%name%" => "John Doe"
                        ]
                )
        );
    }

    public function testGetSecureTranslation() {
        $this->assertEquals(normalizeLN(_esc("<p>Um die Performance der Website zu verbessern,
bietet das UliCMS eine Cache-Funktion.<br/>
Statische Seiten, die keine Module enthalten, werden einmalig generiert und dann im cache-Ordner zwischengespeichert.
Anschließend werden statt die Inhalte immer wieder aus der Datenbank zu laden, die Inhalte aus den gespeicherten HTML-Dateien geladen.</p>")),
                normalizeLN(get_secure_translation("CACHE_TEXT1")));
    }

    public function testSecureTranslation() {
        ob_start();
        secure_translation("CACHE_TEXT1");

        $this->assertEquals(normalizeLN(_esc("<p>Um die Performance der Website zu verbessern,
bietet das UliCMS eine Cache-Funktion.<br/>
Statische Seiten, die keine Module enthalten, werden einmalig generiert und dann im cache-Ordner zwischengespeichert.
Anschließend werden statt die Inhalte immer wieder aus der Datenbank zu laden, die Inhalte aus den gespeicherten HTML-Dateien geladen.</p>")),
                normalizeLN(ob_get_clean()));
    }

    public function testSecureTranslate() {
        ob_start();
        secure_translate("CACHE_TEXT1");

        $this->assertEquals(normalizeLN(_esc("<p>Um die Performance der Website zu verbessern,
bietet das UliCMS eine Cache-Funktion.<br/>
Statische Seiten, die keine Module enthalten, werden einmalig generiert und dann im cache-Ordner zwischengespeichert.
Anschließend werden statt die Inhalte immer wieder aus der Datenbank zu laden, die Inhalte aus den gespeicherten HTML-Dateien geladen.</p>")),
                normalizeLN(ob_get_clean()));
    }

    public function testGetSecureTranslationWithPlaceholders() {

        $this->assertEquals(_esc("Hello <script>alert('xss');</script>John " .
                        "Doe!"),
                get_secure_translation("hello_name",
                        [
                            "%name%" => "<script>alert('xss');</script>John Doe",
        ]));
    }

    public function test_T() {
        $this->assertEquals("Type your password",
                _t("enter_pass"));
    }

    public function test_TWithPlaceholders() {
        $this->assertEquals("Hello John Doe!",
                _t("hello_name",
                        [
                            "%name%" => "John Doe"
        ]));
    }

    public function testT() {
        ob_start();
        t("enter_pass");
        $output = ob_get_clean();
        $this->assertEquals("Type your password", $output);
    }

    public function testTWithPlaceholders() {
        ob_start();
        t("hello_name",
                [
                    "%name%" => "John Doe"
        ]);
        $output = ob_get_clean();
        $this->assertEquals("Hello John Doe!", $output);
    }

    public function testSingularOrPluralWith0ExpectSingular() {
        $this->assertEquals("0 Katzen", singularOrPlural(0, "%number% Katze", "%number% Katzen"));
    }

    public function testSingularOrPluralWith1ExpectSingular() {
        $this->assertEquals("1 Hund", singularOrPlural(1, "%number% Hund", "%number% Hunde"));
    }

    public function testSingularOrPluralWith3ExpectSingular() {
        $this->assertEquals("3 Biere", singularOrPlural(3, "%number% Bier", "%number% Biere"));
    }

    public function testGetTranslationReturnsCustomTranslation() {
        Translation::set("pages", "Parchments");
        $this->assertEquals("Parchments", get_translation("pages"));

        Translation::override("pages", "Papers");
        $this->assertEquals("Papers", get_translation("pages"));

        Translation::delete("pages");
        $this->assertEquals("Pages", get_translation("pages"));
    }

    public function testGetTranslationNotFoundReturnsKey() {
        $this->assertEquals("not_a_translation", get_translation("not_a_translation"));
    }

    public function testTranslate() {
        ob_start();
        translate("pages");
        $this->assertEquals("Pages", ob_get_clean());
    }

    public function testTranslation() {
        ob_start();
        translation("pages");
        $this->assertEquals("Pages", ob_get_clean());
    }

}
