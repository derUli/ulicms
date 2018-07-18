<?php
class MobileDetectJs extends Controller{
	private $moduleName = "mobile_detect_js";

	public function frontendFooter(){
		enqueueScriptFile(ModuleHelper::buildRessourcePath($this->moduleName, "js/mobile-detect.min.js"));
		combinedScriptHTML();
	}	
	public function backendFooter(){
		$this->frontendFooter();
	}
}