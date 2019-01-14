<?php

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

    public function executeSql()
    {
        $sql = Request::getVar("sql_code", "", "str");
        if (StringHelper::isNullOrWhitespace($sql)) {
            HtmlResult("");
        }
        $result = @Database::query($sql, true);
        if (! $result || Database::getError($result)) {
            ViewBag::set("error", Database::getError());
            $html = Template::executeModuleTemplate(self::MODULE_NAME, "error.php");
            HTMLResult($html);
        }
        ViewBag::set("result", $result);
        // Mock, TODO: Split sql statements, show multiple tables
        $html = Template::executeModuleTemplate(self::MODULE_NAME, "table.php");
        HtmlResult($html);
    }
}