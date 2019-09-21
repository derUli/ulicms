<?php

use UliCMS\Utils\CacheUtil;

class DesignSettingsController extends Controller {

    private $moduleName = "core_settings";
    protected $generatedSCSS;

    public function __construct() {
        parent::__construct();
        // generate scss file for design settings if it doesn't exist.
        $this->generatedSCSS = Path::resolve(
                        "ULICMS_GENERATED/design_variables.scss"
        );
        if (!file_exists($this->generatedSCSS)) {
            $this->generateSCSSToFile();
        }
    }

    public function savePost() {
        if (!isset($_REQUEST["disable_custom_layout_options"])) {
            Settings::set(
                    "disable_custom_layout_options",
                    "disable"
            );
        } else {
            Settings::delete("disable_custom_layout_options");
        }

        if (isset($_REQUEST["no_mobile_design_on_tablet"])) {
            Settings::set(
                    "no_mobile_design_on_tablet",
                    "no_mobile_design_on_tablet"
            );
        } else {
            Settings::delete("no_mobile_design_on_tablet");
        }

        if (isset($_REQUEST["video_width_100_percent"])) {
            Settings::set("video_width_100_percent", "width");
        } else {
            Settings::delete("video_width_100_percent");
        }

        if ($_REQUEST["additional_menus"] !== $additional_menus) {
            Settings::set("additional_menus", $_REQUEST["additional_menus"]);
        }



        // Wenn Formular abgesendet wurde, Wert Speichern
        $themes = getAllThemes();
        if (faster_in_array($_REQUEST["theme"], $themes)) {
            Settings::set("theme", $_REQUEST["theme"]);
            $theme = $_REQUEST["theme"];
        }


        // Wenn Formular abgesendet wurde, Wert Speichern
        $themes = getAllThemes();
        if (empty($_REQUEST["mobile_theme"]))
            Settings::delete("mobile_theme");
        else if (faster_in_array($_REQUEST["mobile_theme"], $themes)) {
            Settings::set("mobile_theme", $_REQUEST["mobile_theme"]);
            $mobile_theme = $_REQUEST["mobile_theme"];
        }



        if ($_REQUEST["default_font"] != Settings::get("default_font")) {
            if (!empty($_REQUEST["custom-font"])) {
                $font = $_REQUEST["custom-font"];
            } else {
                $font = $_REQUEST["default_font"];
            }

            $font = $font;

            Settings::set("default_font", $font);
        }

        if (!empty($_REQUEST["google-font"])) {
            $font = $_REQUEST["google-font"];
            $font = $font;
            Settings::set("google-font", $font);
        }

        Settings::set("font-size", $_REQUEST["font-size"]);
        Settings::set("ckeditor_skin", $_REQUEST["ckeditor_skin"]);

        if (Settings::get("header-background-color") != $_REQUEST["header-background-color"]
        ) {
            Settings::set(
                    "header-background-color",
                    $_REQUEST["header-background-color"]
            );
        }

        if (Settings::get("body-text-color") != $_REQUEST["body-text-color"]) {
            Settings::set("body-text-color", $_REQUEST["body-text-color"]);
        }

        if (Settings::get("title_format") != $_REQUEST["title_format"]) {
            Settings::set("title_format", $_REQUEST["title_format"]);
        }

        if (Settings::get("body-background-color") != $_REQUEST["body-background-color"]
        ) {
            Settings::set(
                    "body-background-color",
                    $_REQUEST["body-background-color"]
            );
        }

        CacheUtil::clearPageCache();

        $this->generateSCSSToFile();
        sureRemoveDir(Path::resolve("ULICMS_CACHE/stylesheets"), false);

        HTTPStatusCodeResult(HttpStatusCode::OK);
    }

    public function getFontFamilys() {
        global $fonts;
        $fonts = [];
        $fonts["Times New Roman"] = "TimesNewRoman, 'Times New Roman', Times, Baskerville, Georgia, serif";
        $fonts["Georgia"] = "Georgia, Times, 'Times New Roman', serif";
        $fonts["Sans Serif"] = "sans-serif";
        $fonts["Arial"] = "Arial, 'Helvetica Neue', Helvetica, sans-serif";
        $fonts["Comic Sans MS"] = "Comic Sans MS";
        $fonts["Helvetica"] = "Helvetica, Arial, 'lucida grande',tahoma,verdana,arial,sans-serif;";
        $fonts["Tahoma"] = "Tahoma, Verdana, Segoe, sans-serif";
        $fonts["Verdana"] = "Verdana, Geneva, sans-serif";
        $fonts["Trebuchet MS"] = "'Trebuchet MS'";
        $fonts["Lucida Grande"] = "'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Geneva, Verdana, sans-serif";
        $fonts["monospace"] = "monospace";
        $fonts["Courier"] = "Courier";
        $fonts["Courier New"] = "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace";
        $fonts["Lucida Console"] = "'Lucida Console', 'Lucida Sans Typewriter', Monaco, 'Bitstream Vera Sans Mono', monospace";
        $fonts["fantasy"] = "fantasy";
        $fonts["cursive"] = "cursive";
        $fonts["Calibri"] = "Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif";
        $fonts["Brush Script MT"] = "'Brush Script MT',Phyllis,'Lucida Handwriting',cursive";
        $fonts["Zapf Chancery"] = "'Zapf Chancery', cursive";
        $fonts["Calibri"] = "Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif";
        $fonts["Segoe"] = "'wf_SegoeUI', 'Segoe UI', 'Segoe','Segoe WP', 'Tahoma', 'Verdana', 'Arial', 'sans-serif'";
        $fonts["Google Fonts"] = "google";
        $fonts = apply_filter($fonts, "fonts_filter");

        // Hier bei Bedarf weitere Fonts einfügen
        // $fonts["Meine Font 1"] = "myfont1";
        // $fonts["Meine Font 2"] = "myfont2";
        // $fonts["Meine Font 3"] = "myfont3";
        // Weitere Fonts Ende
        uksort($fonts, "strnatcasecmp");

        return $fonts;
    }

    public function getGoogleFonts() {
        $retval = [];
        $file = ModuleHelper::buildModuleRessourcePath(
                        $this->moduleName,
                        "data/webFontNames.opml"
        );
        $content = file_get_contents($file);
        $xml = new SimpleXMLElement($content);
        foreach ($xml->body->outline as $outline) {
            $retval[] = $outline["text"];
        }
        return $retval;
    }

    public function getThemePreview() {
        $theme = Request::getVar("theme", null, "str");

        if (!$theme) {
            HTTPStatusCodeResult(HttpStatusCode::UNPROCESSABLE_ENTITY);
        }

        $screenshotFiles = array(
            "screenshot.jpg",
            "screenshot.gif"
        );
        foreach ($screenshotFiles as $file) {
            $fullPath = getTemplateDirPath($theme) . $file;
            if (file_exists($fullPath)) {
                HTMLResult(UliCMS\HTML\imageTag($fullPath));
            }
        }
        HTTPStatusCodeResult(HttpStatusCode::NOT_FOUND);
    }

    public function generateSCSS() {
        $settings = [
            "header-background-color" => Settings::get("header-background-color"),
            "body-text-color" => Settings::get("body-text-color"),
            "body-background-color" => Settings::get("body-background-color"),
            "default-font" => Settings::get("default_font") !== "google" ?
            Settings::get("default_font") : Settings::get("google-font"),
            "font-size" => Settings::get("font-size")
        ];

        if (Settings::get("disable_custom_layout_options")) {
            return null;
        }
        $output = "/*\n\tThis file is autogenerated\n";
        $output .= "\tDON'T EDIT THIS FILE BECAUSE ALL CHANGES ARE GETTING OVERWRITTEN\n";
        $output .= "\tIf you want to customize these values\n\tchange it at the Design Settings User Interface.\n*/\n\n";

        foreach ($settings as $var => $value) {
            $output .= "\${$var}: {$value};\n";
        }
        return $output;
    }

    public function generateSCSSToFile() {
        $scss = $this->generateSCSS();

        if ($scss) {
            $outputFile = $this->generatedSCSS;
            file_put_contents($outputFile, $scss);
            return $outputFile;
        }
        return null;
    }

}
