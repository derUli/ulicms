<?php

class ComicSansFtw extends MainClass
{

    const MODULE_NAME = "comic_sans_ftw";

    public function adminHead()
    {
        $css = ModuleHelper::buildModuleRessourcePath(self::MODULE_NAME, "comic_sans.css");
        enqueueStylesheet($css);
        combinedStylesheetHtml();
    }
}