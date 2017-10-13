<?php

class MyCustomFunctions extends Controller
{

    const EMPTY_PHP_FILE = "<?php\r\n";

    private $moduleName = "my_custom_functions";

    private $path = null;

    public function __construct()
    {
        $this->path = Path::resolve("ULICMS_ROOT/content/custom_code/functions.php");
    }

    public function beforeInit()
    {
        if (! file_exists(dirname($this->path))) {
            mkdir(dirname($this->path));
        }
    }

    public function afterInit()
    {
        // FIXME: Fehlerbehandlung
        // Dem User sollte angezeigt werden, wenn sein Code Fehler enthält.
        // Fehlerbehandlung mit try/catch und eigenem error_handler
        // var_dump ( $this->path );
        if (file_exists($this->path)) {
            if (function_exists("php_check_syntax")) {
                php_check_syntax($this->path, $error_message);
                if ($error_message) {
                    trigger_error($error_message, E_USER_WARNING);
                    return;
                }
            }
            include_once $this->path;
        }
    }

    public function getSettingsLinkText()
    {
        return get_translation("edit");
    }

    public function getSettingsHeadline()
    {
        return get_translation("my_custom_functions");
    }

    public function save()
    {
        $data = Request::getVar("code", self::EMPTY_PHP_FILE, "str");
        $data = trim($data);
        File::write($this->path, $data);
        Request::redirect(ModuleHelper::buildAdminURL($this->moduleName));
    }

    public function settings()
    {
        ViewBag::set("code", file_exists($this->path) ? file_get_contents($this->path) : self::EMPTY_PHP_FILE);
        return Template::executeModuleTemplate($this->moduleName, "form.php");
    }
}