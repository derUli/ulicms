<?php

declare(strict_types=1);

namespace App\Security;

defined('ULICMS_ROOT') || exit('no direct script access allowed');

use App\HTML\Input;
use Request;
use Settings;
use Template;
use ViewBag;

/**
 * Handling GDPR / DSGVO privacy checkbox
 */
class PrivacyCheckbox
{
    public const CHECKBOX_NAME = 'accept_privacy_policy';

    private $language;

    /**
     * Constructor
     * @param string $language
     */
    public function __construct(string $language)
    {
        $this->language = $language;
    }

    /**
     * Check if the GDPR checkbox is enabled
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)Settings::get(
                    "privacy_policy_checkbox_enable_{$this->language}",
                    'bool'
                );
    }

    /**
     * Get the name of the checkbox input
     * @return string
     */
    public function getCheckboxName(): string
    {
        return self::CHECKBOX_NAME;
    }

    /**
     * Check if the checkbox is checked
     * @return bool
     */
    public function isChecked(): bool
    {
        $value = Request::getVar(
            $this->getCheckboxName(),
            '',
            'str'
        );
        return ! empty($value);
    }

    /**
     * Check if the checkbox is checked and execute callback
     * @param callable|null $success
     * @param callable|null $failed
     * @return void
     */
    public function check(
        ?callable $success = null,
        ?callable $failed = null
    ): void {
        if ($this->isChecked()) {
            if ($success != null) {
                $success();
            }
        } else {
            if ($failed != null) {
                $failed();
            } else {
                ViewBag::set('exception', get_translation('please_accept_privacy_conditions'));
                echo Template::executeDefaultOrOwnTemplate('exception.php');
                exit();
            }
        }
    }

    /**
     * Render the GDPR checkbox input
     * // The GDPR accept text can be written in a html editor.
     * // This method replaces the [checkbox] placeholder with the checkbox input
     * @return string
     */
    public function render(): string
    {
        $checkboxHtml = Input::checkBox(
            $this->getCheckboxName(),
            false,
            '✔',
            [
                'required' => 'required',
                'id' => $this->getCheckboxName()
            ]
        );
        $fullHtml = Settings::get(
            "privacy_policy_checkbox_text_{$this->language}"
        );
        if (! $this->isEnabled() ||
                empty($fullHtml)
        ) {
            return '';
        }
        $fullHtml = str_ireplace(
            '[checkbox]',
            $checkboxHtml,
            $fullHtml
        );
        return $fullHtml;
    }
}
