<?php

class GoSquaredFlags extends Controller
{

    private $moduleName = "gosquared_flags";

    public function getSettingsLinkText()
    {
        return get_translation("info");
    }

    public function getSettingsHeadline()
    {
        return "Flag Icons by GoSquared (http://www.gosquared.com/)";
    }

    public function settings()
    {
        $changelogFile = ModuleHelper::buildModuleRessourcePath($this->moduleName, "Hello.txt");
        $text = file_get_contents($changelogFile);
        $text = htmlspecialchars($text);
        $text = nl2br($text);
        return $text;
    }

    public function getPathByIsoCode($isoCode, $size, $style = "flat")
    {
        $path = "flags-iso/" . $style . "/" . $size . "/" . $isoCode;
        $path = ModuleHelper::buildModuleRessourcePath($this->moduleName, $path);
        if (! file_exists($path)) {
            return null;
        }
        return $path;
    }

    public function getPathByCountryName($name, $size, $style = "flat")
    {
        $path = "flags/" . $style . "/" . $size . "/" . $name;
        
        $path = ModuleHelper::buildModuleRessourcePath($this->moduleName, $path);
        if (! file_exists($path)) {
            return null;
        }
        return $path;
    }
}