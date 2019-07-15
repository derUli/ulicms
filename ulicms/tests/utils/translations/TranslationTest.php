<?php

class TranslationTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        require_once getLanguageFilePath("en");
    }

    public function testGetTranslation() {
        $this->assertEquals("Type your password",
                get_translation("enter_pass"));
    }

    public function testGetTranslationWithPlaceholders() {
        $this->assertEquals("Hello John Doe!",
                get_translation("hello_name",
                        [
                            "%firstname%" => "John",
                            "%lastname%" => "Doe"
        ]));
    }

    public function testGetSecureTranslation() {
        $this->assertEquals(normalizeLN(_esc("<p>Um die Performance der Website zu verbessern,
bietet das UliCMS eine Cache-Funktion.<br/>
Statische Seiten, die keine Module enthalten, werden einmalig generiert und dann im cache-Ordner zwischengespeichert.
Anschließend werden statt die Inhalte immer wieder aus der Datenbank zu laden, die Inhalte aus den gespeicherten HTML-Dateien geladen.</p>")),
                normalizeLN(get_secure_translation("CACHE_TEXT1")));
    }

    public function testGetSecureTranslationWithPlaceholders() {

        $this->assertEquals(_esc("Hello <script>alert('xss');</script>John " .
                        "Doe<script>alert('xss');</script>!"),
                get_secure_translation("hello_name",
                        [
                            "%firstname%" => "<script>alert('xss');</script>John",
                            "%lastname%" => "Doe<script>alert('xss');</script>"
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
                            "%firstname%" => "John",
                            "%lastname%" => "Doe"
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
                    "%firstname%" => "John",
                    "%lastname%" => "Doe"
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

}
