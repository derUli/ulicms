<?php
use Jaybizzle\CrawlerDetect\CrawlerDetect;
class CrawlerDetect_Main extends controller {
	public function isCrawlerFilter($useragent) {
		$CrawlerDetect = new CrawlerDetect ();
		return $CrawlerDetect->isCrawler ( $useragent );
	}
}
