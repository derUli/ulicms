<?php

class UrlExtractorTest extends \PHPUnit\Framework\TestCase
{

    public function testFromStringWithUrls()
    {
        $input = "Das ist Google:
https://www.google.de

Das ist UliCMS:
http://www.ulicms.de

Das ist noch mal Google:
https://www.google.de

Und das ist PHP:
http://php.net/downloads.php

Das ist eine FTP URL:
ftp://ftp.tugraz.at/mirror/centos/
";
        $extractor = new UrlExtractor();
        $urls = $extractor->fromString($input);
        $this->assertCount(4, $urls);
        $this->assertEquals("https://www.google.de", $urls[0]);
        $this->assertEquals("http://www.ulicms.de", $urls[1]);
        $this->assertEquals("http://php.net/downloads.php", $urls[2]);
        $this->assertEquals("ftp://ftp.tugraz.at/mirror/centos/", $urls[3]);
    }

    public function testFromStringWithoutUrls()
    {
        $input = "Dieser String enthält keine URLs
Wirklich nicht";
        $extractor = new UrlExtractor();
        $urls = $extractor->fromString($input);
        $this->assertCount(0, $urls);
    }
}