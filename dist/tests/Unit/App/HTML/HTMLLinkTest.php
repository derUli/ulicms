<?php

use App\HTML\Link as Link;

class HTMLLinkTest extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        chdir(\App\Utils\Path::resolve('ULICMS_ROOT/admin'));
    }

    protected function tearDown(): void {
        chdir(\App\Utils\Path::resolve('ULICMS_ROOT'));
    }

    public function testLink(): void {
        $this->assertEquals('<a href="https://www.google.com">Google</a>', Link::link('https://www.google.com', 'Google'));
    }

    public function testLinkWithAdditionalAttribute(): void {
        $this->assertEquals('<a href="https://www.google.com" target="_blank">Google</a>', Link::link('https://www.google.com', 'Google', [
            'target' => '_blank'
        ]));
    }

    public function testActionLink(): void {
        $this->assertEquals('<a href="?action=pages">Pages</a>', Link::actionLink('pages', 'Pages'));
    }

    public function testActionLinkWithSuffix(): void {
        $this->assertEquals('<a href="?action=pages&amp;hello=world">Pages</a>', Link::actionLink('pages', 'Pages', 'hello=world'));
    }

    public function testLinkWithAdditionalAttributes(): void {
        $this->assertEquals('<a href="?action=pages" target="_blank" class="btn btn-primary">Pages</a>', Link::actionLink('pages', 'Pages', null, [
            'target' => '_blank',
            'class' => 'btn btn-primary'
        ]));
    }
}
