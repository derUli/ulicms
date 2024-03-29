<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Models\Content\Language;
use App\Utils\CacheUtil;
use Rakit\Validation\Validator;

use function App\HTML\stringContainsHtml;

class LanguageController extends \App\Controllers\Controller {
    public function _createPost(): Language {
        $this->validateInput();

        $name = Request::getVar('name', null, 'str');
        $language_code = Request::getVar('language_code', null, 'str');

        do_event('before_create_language');

        $language = new Language();
        $language->setName($name);
        $language->setLanguageCode($language_code);
        $language->save();

        do_event('after_create_language');
        CacheUtil::clearPageCache();
        return $language;
    }

    public function createPost(): void {
        $this->_createPost();
        Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('languages'));
    }

    public function setDefaultLanguage(): void {
        $this->_setDefaultLanguage();
        Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('languages'));
    }

    public function _setDefaultLanguage(): void {
        do_event('before_set_default_language');

        $default = Request::getVar('default', null, 'str');

        Settings::set('default_language', $default);
        Settings::set('system_language', $default);

        do_event('after_set_default_language');

        CacheUtil::clearPageCache();
    }

    public function _deletePost(): bool {
        $id = Request::getVar('id', null, 'int');
        do_event('before_delete_language');

        $language = new Language($id);
        if (! $language->getName()) {
            return false;
        }
        $language->delete();

        do_event('after_delete_language');
        CacheUtil::clearPageCache();
        return ! $language->isPersistent();
    }

    public function deletePost(): void {
        if (! $this->_deletePost()) {
            ExceptionResult(
                get_translation('not_found'),
                \App\Constants\HttpStatusCode::NOT_FOUND
            );
        }

        Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('languages'));
    }

    protected function validateInput(): void {
        // Fix for security issue CVE-2019-11398
        if (stringContainsHtml($_POST['name'])
                || stringContainsHtml($_POST['language_code'])) {
            ExceptionResult(get_translation('no_html_allowed'));
        }

        $validator = new Validator();
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
            ExceptionResult($html, \App\Constants\HttpStatusCode::UNPROCESSABLE_ENTITY);
        }
    }
}
