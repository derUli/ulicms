<?php

class SpellCheckerTest extends \PHPUnit\Framework\TestCase
{

    public function testAutoCorrectCommonTypos()
    {
        $this->assertEquals("Der Quasi-Standard", SpellChecker::autoCorrectCommonTypos("Der Quasi-Standart"));
        $this->assertEquals("Er spielt im Verein Billard.", SpellChecker::autoCorrectCommonTypos("Er spielt im Verein Billiard."));
        $this->assertEquals("Meine ersten Versuche waren dilettantisch.", SpellChecker::autoCorrectCommonTypos("Meine ersten Versuche waren dilletantisch."));
        $this->assertEquals("Grießbrei mit Apfelmus", SpellChecker::autoCorrectCommonTypos("Griesbrei mit Apfelmus"));
    }
}