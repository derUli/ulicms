<?php

use UliCMS\Security\PermissionChecker;

class SeoCheckController extends Controller
{

    public function adminMenuEntriesFilter($entries)
    {
		$checker = new PermissionChecker(get_user_id());
		if(!$checker->hasPermission("seo_check")){
			return $entries;
		}
		
        $entry = new MenuEntry('<i class="fas fa-chart-line"></i> SEO Check', $this->getUrl(), "pagespeed", null, array(), true);
        $entries = ArrayHelper::insertBefore($entries, count($entries) - 2, $entry);
				
        return $entries;
    }
	
	private function getUrl(){
		return "https://freetools.seobility.net/de/seocheck/check?url=".urlencode(get_protocol_and_domain())."&crawltype=1";
	}
	
	public function getSettingsHeadline(){
		return "SEO Check";
	}
	
	public function getSettingsLinkText(){
		return get_translation("open");
	}
	
	public function settings(){
		Response::javascriptRedirect($this->getURL());
	}
}