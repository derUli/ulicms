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
            "help",
            "pages",
            "gibts_nicht"
        ];
        $translation = new JSTranslation($keys, "ThisIsNotGoogleTranslator");
        $this->assertEquals(
                $translation->getJS(),
                file_get_contents(ULICMS_ROOT . "/tests/fixtures/JSTranslation/JSTranslation2.js")
        );
    }

    public function testAddKey() {
        $keys = [
            "help",
            "pages",
            "gibts_nicht"
        ];


        $translation = new JSTranslation($keys);
        $translation->addKey("category");

        $this->assertEquals(
                [
                    "help",
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

}
