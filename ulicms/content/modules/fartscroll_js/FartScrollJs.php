<?php

class FartScrollJs extends MainClass
{

    const MODULE_NAME = "fartscroll_js";

    public function frontendFooter()
    {
        $jsFile = ModuleHelper::buildModuleRessourcePath(self::MODULE_NAME, "fartscroll.js");
        enqueueScriptFile($jsFile);
        combinedScriptHtml();
    }
}