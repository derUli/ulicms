<?php

class LogoUploadControllerTest extends \PHPUnit\Framework\TestCase {

    public function testBuildFileName() {
        $controller = new LogoUploadController();

        $fileName = $controller->_buildFileName(
                Path::Resolve("ULICMS_ROOT/tests/fixtures/cat.jpg"),
                "cat.jpg"
        );
        $this->assertEquals("94f7fbd93d43a9f6b026f4b712d48be7.jpg", $fileName);
    }

    public function testBuildFilePath() {
        $controller = new LogoUploadController();

        $filePath = $controller->_buildFilePath(
                Path::Resolve("ULICMS_ROOT/tests/fixtures/cat.jpg"),
                "cat.jpg"
        );
        $this->assertStringEndsWith(
                "/content/images/94f7fbd93d43a9f6b026f4b712d48be7.jpg",
                $filePath
        );
    }

}
