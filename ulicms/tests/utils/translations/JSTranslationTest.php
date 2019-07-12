<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JSTranslationTest
 *
 * @author deruli
 */
class JsTranslationTest extends \PHPUnit\Framework\TestCase {

    public function setUp() {
        require_once getLanguageFilePath("en");
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
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys);


        $this->assertEquals(
                $translation->getJS(),
                file_get_contents(ULICMS_ROOT . "/tests/fixtures/JSTranslation/JSTranslation1.js")
        );
    }

    public function testGetJsWithVarname() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "ThisIsNotGoogleTranslator");

        $this->assertEquals(
                $translation->getJS(),
                file_get_contents(ULICMS_ROOT . "/tests/fixtures/JSTranslation/JSTranslation2.js")
        );
    }

    public function testGetJsWithVarnameAndWrap() {
        $keys = [
            "pages_count",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "ThisIsNotGoogleTranslator");

        $this->assertEquals(
                $translation->getJS('<script id="my-script">{code}</script>'),
                file_get_contents(ULICMS_ROOT . "/tests/fixtures/JSTranslation/JSTranslation3.js")
        );
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

        $this->assertEquals(
                $output,
                file_get_contents(ULICMS_ROOT . "/tests/fixtures/JSTranslation/JSTranslation1.js")
        );
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
        $this->assertEquals(
                $output,
                file_get_contents(ULICMS_ROOT . "/tests/fixtures/JSTranslation/JSTranslation2.js")
        );
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

        $this->assertEquals(
                $output,
                file_get_contents(ULICMS_ROOT . "/tests/fixtures/JSTranslation/JSTranslation3.js")
        );
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
                $translation->getKeys());
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
                ], $translation->getKeys());
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
        $this->assertEquals(["pages_count", "pages", "bar"],
                $translation->getKeys());
    }

}
