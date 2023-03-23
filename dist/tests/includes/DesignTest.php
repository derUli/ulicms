<?php


class DesignTest extends \PHPUnit\Framework\TestCase
{
    public function testGetThemeMeta()
    {
        $meta = getThemeMeta("impro17");
        $this->assertIsArray($meta);
        $this->assertEquals("2.1.5", $meta["version"]);
    }
      public function testGetThemeMetaWithAttribute()
      {
          $version = getThemeMeta("impro17", "version");
          $this->assertIsString($version);
          $this->assertEquals("2.1.5", $version);
      }
}
