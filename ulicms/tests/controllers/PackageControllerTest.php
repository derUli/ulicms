<?php

class PackageControllerTest extends \PHPUnit\Framework\TestCase {

	public function testGetPackageDownloadUrlReturnsUrl() {
		$controller = ControllerRegistry::get(PackageController::class);

		$this->assertEquals(
				"https://extend.ulicms.de/fortune2.html",
				$controller->_getPackageDownloadUrl("fortune2")
		);


		$this->assertEquals(
				"https://extend.ulicms.de/mail_queue.html",
				$controller->_getPackageDownloadUrl("mail_queue")
		);
	}

	public function testGetPackageDownloadUrlReturnsNull() {

		$controller = ControllerRegistry::get(PackageController::class);

		$this->assertNull($controller->_getPackageDownloadUrl("gibts_nicht"));
		$this->assertNull($controller->_getPackageDownloadUrl(""));
	}

}
