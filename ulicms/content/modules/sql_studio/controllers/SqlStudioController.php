<?php
use UliCMS\Exceptions\NotImplementedException;

class SqlStudioController extends MainClass
{

    const MODULE_NAME = "sql_studio";

    public function settings()
    {
        ViewBag::set("tables", Database::getAllTables());
        return Template::executeModuleTemplate(self::MODULE_NAME, "form.php");
    }

    public function getSettingsHeadline()
    {
        return "SQL Studio";
    }

    public function getSettingsLinkText()
    {
        return get_translation("open");
    }

    public function adminHead()
    {
        enqueueStylesheet(ModuleHelper::buildModuleRessourcePath(self::MODULE_NAME, "css/style.css"));
        combinedStylesheetHtml();
    }

    public function saveSettings()
    {
        throw new NotImplementedException();
    }

    public function executeSql()
    {
        $sql = Request::getVar("sql_code", "", "str");
        if (StringHelper::isNullOrWhitespace($sql)) {
            HtmlResult("");
        }
        
        $replacePlaceholders = boolval(Settings::get("sql_studio/replace_placeholders"));
        
        $result = @Database::query($sql, $replacePlaceholders);
        if (! $result || Database::getError()) {
            ViewBag::set("error", Database::getError());
            $html = Template::executeModuleTemplate(self::MODULE_NAME, "error.php");
            HTMLResult($html);
        }
        $affectedRows = Database::getAffectedRows();
        if (is_bool($result) and $result) {
            ViewBag::set("success", get_translation("x_rows_affected", array(
                "%x" => $affectedRows
            )));
            $html = Template::executeModuleTemplate(self::MODULE_NAME, "success.php");
            HTMLResult($html);
        }
        ViewBag::set("result", $result);
        // Mock, TODO: Split sql statements, show multiple tables
        $html = Template::executeModuleTemplate(self::MODULE_NAME, "table.php");
        HtmlResult($html);
    }
}