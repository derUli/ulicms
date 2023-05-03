<?php

use App\Translations\JSTranslation;
use Spatie\Snapshots\MatchesSnapshots;

class JSTranslationTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    protected function setUp(): void {
        require_once getLanguageFilePath('en');
    }

    public function testConstructorWithKeys(): void {
        $keys = [
            'help',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys);
        $this->assertEquals($keys, $translation->getKeys());
    }

    public function testGetJs(): void {
        $keys = [
            'help',
            'TRANSLATION_PAGES',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys);

        $this->assertMatchesHtmlSnapshot($translation->getJS());
    }

    public function testGetJsWithVarname(): void {
        $keys = [
            'pages_count',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys, 'ThisIsNotGoogleTranslator');

        $this->assertMatchesHtmlSnapshot($translation->getJS());
    }

    public function testGetJsWithVarnameAndWrap(): void {
        $keys = [
            'pages_count',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys, 'ThisIsNotGoogleTranslator');

        $this->assertMatchesHtmlSnapshot($translation->getJS('<script id="my-script">{code}</script>'));
    }

    public function testRender(): void {
        $keys = [
            'help',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys);
        ob_start();
        $translation->render();
        $output = ob_get_clean();

        $this->assertMatchesHtmlSnapshot($output);
    }

    public function testRenderJs(): void {
        $keys = [
            'help',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys);
        ob_start();
        $translation->renderJS();
        $output = ob_get_clean();

        $this->assertMatchesHtmlSnapshot($output);
    }

    public function testRenderJsWithVarname(): void {
        $keys = [
            'pages_count',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys, 'ThisIsNotGoogleTranslator');
        ob_start();
        $translation->renderJS();
        $output = ob_get_clean();

        $this->assertMatchesHtmlSnapshot($output);
    }

    public function testRenderJsWithVarnameAndWrap(): void {
        $keys = [
            'pages_count',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys, 'ThisIsNotGoogleTranslator');

        ob_start();
        $translation->renderJS('<script id="my-script">{code}</script>');
        $output = ob_get_clean();

        $this->assertMatchesHtmlSnapshot($output);
    }

    public function testAddKey(): void {
        $keys = [
            'pages_count',
            'pages',
            'gibts_nicht'
        ];

        $translation = new JSTranslation($keys);
        $translation->addKey('category');

        $this->assertEquals(
            [
                'pages_count',
                'pages',
                'gibts_nicht',
                'category'
            ],
            $translation->getKeys()
        );
    }

    public function testAddKeys(): void {
        $keys = [
            'help',
            'pages',
            'gibts_nicht'
        ];

        $translation = new JSTranslation($keys);
        $translation->addKeys(
            ['category',
                'images']
        );

        $this->assertEquals(
            [
                'help',
                'pages',
                'gibts_nicht',
                'category',
                'images'
            ],
            $translation->getKeys()
        );
    }

    public function testGetVarName(): void {
        $keys = [
            'pages_count',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys, 'UniversalTranslator');
        $this->assertEquals('UniversalTranslator', $translation->getVarName());
    }

    public function testSetVarName(): void {
        $keys = [
            'pages_count',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys, 'UniversalTranslator');
        $translation->setVarName('CrapTranslation');
        $this->assertEquals('CrapTranslation', $translation->getVarName());
    }

    public function testRemoveKey(): void {
        $keys = [
            'pages_count',
            'pages',
            'gibts_nicht'
        ];
        $translation = new JSTranslation($keys, 'UniversalTranslator');
        $translation->removeKey('pages_count');
        $this->assertEquals(['pages', 'gibts_nicht'], $translation->getKeys());
    }

    public function testRemoveKeys(): void {
        $keys = [
            'pages_count',
            'pages',
            'gibts_nicht',
            'foo',
            'bar'
        ];
        $translation = new JSTranslation($keys, 'UniversalTranslator');
        $translation->removeKeys(['foo', 'gibts_nicht']);
        $this->assertEquals(
            ['pages_count', 'pages', 'bar'],
            $translation->getKeys()
        );
    }
}
