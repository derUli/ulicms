<?php

declare(strict_types=1);

use App\Packages\Theme;
use App\Utils\CacheUtil;

class DesignSettingsController extends \App\Controllers\Controller
{
    protected $generatedSCSS;

    private $moduleName = 'core_settings';

    public function __construct()
    {
        parent::__construct();
        // generate scss file for design settings if it doesn't exist.
        $this->generatedSCSS = Path::resolve(
            'ULICMS_GENERATED/design_variables.scss'
        );
        if (! is_file($this->generatedSCSS)) {
            $this->_generateSCSSToFile();
        }
    }

    public function savePost(): void
    {
        if (isset($_REQUEST['no_mobile_design_on_tablet'])) {
            Settings::set(
                'no_mobile_design_on_tablet',
                'no_mobile_design_on_tablet'
            );
        } else {
            Settings::delete('no_mobile_design_on_tablet');
        }

        // Wenn Formular abgesendet wurde, Wert Speichern
        $themes = getAllThemes();
        if (in_array($_REQUEST['theme'], $themes)) {
            Settings::set('theme', $_REQUEST['theme']);
            $theme = $_REQUEST['theme'];
        }

        // Wenn Formular abgesendet wurde, Wert Speichern
        $themes = getAllThemes();
        if (empty($_REQUEST['mobile_theme'])) {
            Settings::delete('mobile_theme');
        } elseif (in_array($_REQUEST['mobile_theme'], $themes)) {
            Settings::set('mobile_theme', $_REQUEST['mobile_theme']);
            $mobile_theme = $_REQUEST['mobile_theme'];
        }

        if ($_REQUEST['default_font'] != Settings::get('default_font')) {
            if (! empty($_REQUEST['custom-font'])) {
                $font = $_REQUEST['custom-font'];
            } else {
                $font = $_REQUEST['default_font'];
            }

            $font = $font;

            Settings::set('default_font', $font);
        }


        Settings::set('font_size', $_REQUEST['font_size']);
        Settings::set('ckeditor_skin', $_REQUEST['ckeditor_skin']);

        if (Settings::get('header_background_color') != $_REQUEST['header_background_color']
        ) {
            Settings::set(
                'header_background_color',
                $_REQUEST['header_background_color']
            );
        }

        if (Settings::get('body_text_color') != $_REQUEST['body_text_color']) {
            Settings::set('body_text_color', $_REQUEST['body_text_color']);
        }

        if (Settings::get('title_format') != $_REQUEST['title_format']) {
            Settings::set('title_format', $_REQUEST['title_format']);
        }

        if (Settings::get('body_background_color') != $_REQUEST['body_background_color']
        ) {
            Settings::set(
                'body_background_color',
                $_REQUEST['body_background_color']
            );
        }

        CacheUtil::clearPageCache();

        $this->_generateSCSSToFile();
        sureRemoveDir(Path::resolve('ULICMS_CACHE/stylesheets'), false);

        HTTPStatusCodeResult(HttpStatusCode::OK);
    }

    /**
     * Get font family selection for design settings
     * @global type $fonts
     * @return array
     */
    public function getFontFamilys(): array
    {
        $fonts = [];

        $fontStackFile = Path::resolve(
            'ULICMS_ROOT/node_modules/system-font-stacks/index.json'
        );

        $fontIndex = json_decode(file_get_contents($fontStackFile), true);

        foreach ($fontIndex as $index => $fontStack) {
            $name = $fontStack[0];

            if (str_starts_with($name, '-')) {
                $name = $index;
            }

            $fonts[$name] = implode(', ', $fontStack);
        }


        $fonts = apply_filter($fonts, 'fonts_filter');

        uksort($fonts, 'strnatcasecmp');

        return $fonts;
    }

    public function themePreview(): void
    {
        $themeName = Request::getVar('theme', null, 'str');

        if (! $themeName) {
            HTTPStatusCodeResult(HttpStatusCode::UNPROCESSABLE_ENTITY);
        }

        $screenshot = $this->_themePreview($themeName);

        if ($screenshot) {
            HTMLResult(
                App\HTML\imageTag(
                    $screenshot,
                    [
                        'class' => 'img-responsive theme-preview'
                    ]
                )
            );
        }

        HTTPStatusCodeResult(HttpStatusCode::NOT_FOUND);
    }

    public function _themePreview(string $themeName): ?string
    {
        $theme = new Theme($themeName);
        $screenshot = $theme->getScreenshotFile();
        return $screenshot;
    }

    public function _generateSCSS(): ?string
    {
        $settings = [
            'header-background-color' => Settings::get('header_background_color'),
            'body-text-color' => Settings::get('body_text_color'),
            'body-background-color' => Settings::get('body_background_color'),
            'default-font' => Settings::get('default_font'),
            'font-size' => Settings::get('font_size')
        ];

        $output = "/*\n\tThis file is autogenerated\n";
        $output .= "\tDON'T EDIT THIS FILE BECAUSE ALL CHANGES ARE GETTING OVERWRITTEN\n";
        $output .= "\tIf you want to customize these values\n\tchange it at the design settings user interface.\n*/\n\n";

        foreach ($settings as $var => $value) {
            $output .= "\${$var}: {$value};\n";
        }
        return $output;
    }

    public function _generateSCSSToFile(): ?string
    {
        $scss = $this->_generateSCSS();

        if ($scss) {
            $outputFile = $this->generatedSCSS;
            file_put_contents($outputFile, $scss);
            return $outputFile;
        }
        return null;
    }

    public function setDefaultTheme(): void
    {
        $theme = Request::getVar('name');
        $this->_setDefaultTheme($theme);
        Settings::set('theme', $theme);

        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            ModuleHelper::buildActionURL('packages')
        );
    }

    public function _setDefaultTheme(?string $theme): void
    {
        Settings::set('theme', $theme);
    }

    public function setDefaultMobileTheme(): void
    {
        $theme = Request::getVar('name');
        $this->_setDefaultMobileTheme($theme);

        Response::sendHttpStatusCodeResultIfAjax(
            HttpStatusCode::OK,
            ModuleHelper::buildActionURL('packages')
        );
    }

    public function _setDefaultMobileTheme(?string $theme): void
    {
        if ($theme !== Settings::get('mobile_theme')) {
            Settings::set('mobile_theme', $theme);
        } else {
            Settings::delete('mobile_theme');
        }
    }

    public function getFontSizes(): array
    {
        $sizes = [];
        for ($i = 6; $i <= 80; $i++) {
            $sizes[] = $i . 'px';
        }
        do_event('custom_font_sizes');

        $sizes = apply_filter($sizes, 'font_sizes');
        return $sizes;
    }
}
