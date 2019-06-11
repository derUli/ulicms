<?php

use UliCMS\Utils\CacheUtil;
use function UliCMS\HTML\stringContainsHtml;

class LanguageController extends Controller {

    public function createPost() {
        // Fix for security issue CVE-2019-11398
        if (stringContainsHtml($_POST["name"])
                or stringContainsHtml($_POST["language_code"])) {
            ExceptionResult(get_translation("no_html_allowed"));
        }
        $name = db_escape($_POST["name"]);
        $language_code = db_escape($_POST["language_code"]);
        do_event("before_create_language");
        db_query("INSERT INTO " . tbname("languages") . "(name, language_code)
      VALUES('$name', '$language_code')");
        do_event("after_create_language");
        CacheUtil::clearPageCache();
        Request::redirect(ModuleHelper::buildActionURL("languages"));
    }

    public function setDefaultLanguage() {
        do_event("before_set_default_language");
        setconfig("default_language", db_escape($_GET["default"]));
        setconfig("system_language", db_escape($_GET["default"]));
        do_event("after_set_default_language");
        CacheUtil::clearPageCache();
        Request::redirect(ModuleHelper::buildActionURL("languages"));
    }

    public function deletePost() {
        do_event("before_delete_language");
        db_query("DELETE FROM " . tbname("languages") . " WHERE id = " . intval($_GET["id"]));
        do_event("after_delete_language");
        CacheUtil::clearPageCache();
        Request::redirect(ModuleHelper::buildActionURL("languages"));
    }

}
