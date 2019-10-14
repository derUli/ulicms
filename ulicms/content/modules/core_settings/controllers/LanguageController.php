<?php

use UliCMS\Utils\CacheUtil;
use UliCMS\Models\Content\Language;
use Rakit\Validation\Validator;
use function UliCMS\HTML\stringContainsHtml;

class LanguageController extends Controller {

    public function createPost() {
        $this->validateInput();

        $name = Request::getVar("name", null, "str");
        $language_code = Request::getVar("language_code", null, "str");

        do_event("before_create_language");

        $language = new Language();
        $language->setName($name);
        $language->setLanguageCode($language_code);
        $language->save();

        do_event("after_create_language");
        CacheUtil::clearPageCache();
        Request::redirect(ModuleHelper::buildActionURL("languages"));
    }

    public function setDefaultLanguage() {
        do_event("before_set_default_language");

        $default = Request::getVar("default", null, "str");

        Settings::set("default_language", $default);
        Settings::set("system_language", $default);

        do_event("after_set_default_language");

        CacheUtil::clearPageCache();
        Request::redirect(ModuleHelper::buildActionURL("languages"));
    }

    public function deletePost() {
        $id = Request::getVar("id", null, "int");
        do_event("before_delete_language");

        $language = new Language($id);
        if (!$language->getName()) {
            ExceptionResult(get_translation("not_found"),
                    HttpStatusCode::NOT_FOUND);
        }
        $language->delete();

        do_event("after_delete_language");
        CacheUtil::clearPageCache();
        Request::redirect(ModuleHelper::buildActionURL("languages"));
    }

    protected function validateInput() {
        // Fix for security issue CVE-2019-11398
        if (stringContainsHtml($_POST["name"])
                or stringContainsHtml($_POST["language_code"])) {
            ExceptionResult(get_translation("no_html_allowed"));
        }

        $validator = new Validator;
        $validation = $validator->make($_POST + $_FILES, [
            'name' => 'required',
            'language_code' => 'required',
        ]);
        $validation->validate();

        $errors = $validation->errors()->all('<li>:message</li>');

        if ($validation->fails()) {
            $html = '<ul>';
            foreach ($errors as $error) {
                $html .= $error;
            }
            $html .= '</ul>';
            ExceptionResult($html, HttpStatusCode::UNPROCESSABLE_ENTITY);
        }
    }

}
