<?php

class DesignTest extends \PHPUnit\Framework\TestCase {
    public function testGetThemeMeta(): void {
        $meta = getThemeMeta('impro17');
        $this->assertIsArray($meta);
        $this->assertEquals('2.1.7', $meta['version']);
    }

    public function testGetThemeMetaWithAttribute(): void {
        $version = getThemeMeta('impro17', 'version');
        $this->assertIsString($version);
        $this->assertEquals('2.1.7', $version);
    }
}
