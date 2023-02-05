<?php

use Spatie\Snapshots\MatchesSnapshots;

class JSTranslationTest extends \PHPUnit\Framework\TestCase {

    use MatchesSnapshots;

    protected function setUp(): void {
        require_once getLanguageFilePath("en");
        require_once ModuleHelper::buildModuleRessourcePath("core_help", "lang/en.php");
    }

    public function testConstructorWithKeys() {
        $keys = [
            "help",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys);
        $this->assertEquals($keys, $translation->getKeys());
    }

    public function testGetJs() {
        $keys = [
            "help",
            "TRANSLATION_PAGES",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys);

        $this->assertMatchesHtmlSnapshot($translation->getJS());
    }

    public function testGetJsWithVarname() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "ThisIsNotGoogleTranslator");

        $this->assertMatchesHtmlSnapshot($translation->getJS());
    }

    public function testGetJsWithVarnameAndWrap() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "ThisIsNotGoogleTranslator");

        $this->assertMatchesHtmlSnapshot(
                $translation->getJS('<script id="my-script">{code}</script>')
        );
    }

    public function testRender() {
        $keys = [
            "help",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys);
        ob_start();
        $translation->render();
        $output = ob_get_clean();

        $this->assertMatchesHtmlSnapshot($output);
    }

    public function testRenderJs() {
        $keys = [
            "help",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys);
        ob_start();
        $translation->renderJS();
        $output = ob_get_clean();

        $this->assertMatchesHtmlSnapshot($output);
    }

    public function testRenderJsWithVarname() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "ThisIsNotGoogleTranslator");
        ob_start();
        $translation->renderJS();
        $output = ob_get_clean();

        $this->assertMatchesHtmlSnapshot($output);
    }

    public function testRenderJsWithVarnameAndWrap() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "ThisIsNotGoogleTranslator");

        ob_start();
        $translation->renderJS('<script id="my-script">{code}</script>');
        $output = ob_get_clean();

        $this->assertMatchesHtmlSnapshot($output);
    }

    public function testAddKey() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];

        $translation = new JSTranslation($keys);
        $translation->addKey("category");

        $this->assertEquals(
                [
                    "pages_count",
                    "pages",
                    "gibts_nicht",
                    "category"
                ],
                $translation->getKeys()
        );
    }

    public function testAddKeys() {
        $keys = [
            "help",
            "pages",
            "gibts_nicht"
        ];

        $translation = new JSTranslation($keys);
        $translation->addKeys(
                ["category",
                    "images"]
        );

        $this->assertEquals(
                [
                    "help",
                    "pages",
                    "gibts_nicht",
                    "category",
                    "images"
                ],
                $translation->getKeys()
        );
    }

    public function testGetVarName() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "UniversalTranslator");
        $this->assertEquals("UniversalTranslator", $translation->getVarName());
    }

    public function testSetVarName() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "UniversalTranslator");
        $translation->setVarName("CrapTranslation");
        $this->assertEquals("CrapTranslation", $translation->getVarName());
    }

    public function testRemoveKey() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "UniversalTranslator");
        $translation->removeKey("pages_count");
        $this->assertEquals(["pages", "gibts_nicht"], $translation->getKeys());
    }

    public function testRemoveKeys() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht",
            "foo",
            "bar"
        ];
        $translation = new JSTranslation($keys, "UniversalTranslator");
        $translation->removeKeys(["foo", "gibts_nicht"]);
        $this->assertEquals(
                ["pages_count", "pages", "bar"],
                $translation->getKeys()
        );
    }

}
