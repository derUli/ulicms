<?php

class Fortune extends MainClass
{
    private $moduleName = "fortune2";

    // Fortune cookie on "welcome" page of UliCMS dashboard
    public function accordionLayout()
    {
        return Template::executeModuleTemplate($this->moduleName, "dashboard");
    }

    // html for frontend output
    public function render()
    {
        return Template::executeModuleTemplate($this->moduleName, "default");
    }

    // filter sample which replaces two placeholders
    public function contentFilter($text)
    {
        $text = str_replace("[fortune]", $this->render(), $text);
        $text = str_replace("[hello]", get_translation("hello_world"), $text);
        return $text;
    }

    // headline of settings page
    public function getSettingsHeadline()
    {
        return get_translation("my_settings_page");
    }

    // settings page content below headline
    public function settings()
    {
        return Template::executeModuleTemplate($this->moduleName, "admin");
    }

    // get a random fortune cookie from files
    // fortune cookies are extracted from Linux "fortune" command.
    public function getRandomFortune()
    {
        if (is_admin_dir()) {
            $lang = getSystemLanguage();
        } else {
            $lang = getCurrentLanguage(true);
        }
        $fortuneDir = getModulePath($this->moduleName) . "cookies/" . $lang . "/";
        if (!is_dir($fortuneDir)) {
            $fortuneDir = getModulePath($this->moduleName) . "cookies/en/";
        }
        $fortuneFiles = scandir($fortuneDir);
        do {
            $file = array_rand($fortuneFiles, 1);
            $file = $fortuneFiles[$file];
            $file = $fortuneDir . $file;
        } while (!is_file($file));

        $fileContent = file_get_contents($file);
        $fileContent = trim($fileContent);
        $fileContent = utf8_encode($fileContent);
        $fileContent = str_replace("\r\n", "\n", $fileContent);
        $fortunes = explode("%\n", $fileContent);
        $text = array_rand($fortunes, 1);
        $text = $fortunes[$text];
        return $text;
    }

    public function doSomething()
    {
        ViewBag::set("sample_text", get_translation("unknown_request_type"));
    }

    public function doSomethingPost()
    {
        ViewBag::set("sample_text", get_translation("post_request_type"));
    }

    public function doSomethingGet()
    {
        ViewBag::set("sample_text", get_translation("get_request_type"));
    }

    public function showFortune()
    {
        ActionResult("fortune", $this->getRandomFortune());
    }

    public function helloWorld()
    {
        echo "Hello World!";
    }
    
    // Thia is executed before uninstalling this module
    // Use this to clean up data (e.g. drop database tables, delete files)
    public function uninstall()
    {
        Settings::set("fortune2_uninstalled_at", time());
    }
}
