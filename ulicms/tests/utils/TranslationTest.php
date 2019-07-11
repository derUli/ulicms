<?php

/**
 * Description of TranslationTest
 *
 * @author deruli
 */
class TranslationTest extends \PHPUnit\Framework\TestCase {

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
