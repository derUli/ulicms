<?php
class MinifyTest extends PHPUnit_Framework_TestCase {
	public function testScriptQueue() {
		$filemtime = 0;
		$files = array (
				"admin/scripts/jquery.min.js",
				"admin/scripts/global.js",
				"admin/scripts/url.min.js" 
		);
		foreach ( $files as $file ) {
			enqueueScriptFile ( $file );
			if (filemtime ( $file ) > $filemtime) {
				$filemtime = filemtime ( $file );
			}
		}
		$this->assertCount ( 3, $_SERVER ["script_queue"] );
		$this->assertEquals ( "admin/scripts/jquery.min.js", $_SERVER ["script_queue"] [0] );
		$this->assertEquals ( "admin/scripts/url.min.js", $_SERVER ["script_queue"] [2] );
		
		resetScriptQueue ();
		$this->assertCount ( 0, $_SERVER ["script_queue"] );
		
		foreach ( $files as $file ) {
			enqueueScriptFile ( $file );
		}
		
		$this->assertEquals ( '<script src="?output_scripts=admin/scripts/jquery.min.js;admin/scripts/global.js;admin/scripts/url.min.js&time=' . $filemtime . '" type="text/javascript"></script>', getCombinedScriptHtml () );
		$this->assertCount ( 0, $_SERVER ["script_queue"] );
	}
	public function testStylesheetQueue() {
		$filemtime = 0;
		$files = array (
				"core.min.css",
				"admin/css/bootstrap.css",
				"admin/css/bootstrap-theme.css",
				"admin/css/modern.css" 
		);
		foreach ( $files as $file ) {
			enqueueStylesheet ( $file );
			if (filemtime ( $file ) > $filemtime) {
				$filemtime = filemtime ( $file );
			}
		}
		$this->assertCount ( 4, $_SERVER ["stylesheet_queue"] );
		$this->assertEquals ( "admin/css/bootstrap.css", $_SERVER ["stylesheet_queue"] [1] );
		$this->assertEquals ( "admin/css/modern.css", $_SERVER ["stylesheet_queue"] [3] );
		
		resetStylesheetQueue ();
		$this->assertCount ( 0, $_SERVER ["stylesheet_queue"] );
		
		foreach ( $files as $file ) {
			enqueueStylesheet ( $file );
		}
		
		$this->assertEquals ( '<link rel="stylesheet" type="text/css" href="?output_stylesheets=core.min.css;admin/css/bootstrap.css;admin/css/bootstrap-theme.css;admin/css/modern.css&time=' . $filemtime . '"/>', getCombinedStylesheetHtml () );
		$this->assertCount ( 0, $_SERVER ["stylesheet_queue"] );
	}
}