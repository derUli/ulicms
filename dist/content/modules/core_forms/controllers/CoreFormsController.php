<?php

declare(strict_types=1);

use App\Helpers\AntiSpamHelper;

class CoreFormsController extends Controller
{
    public function _incSpamCount(): int
    {
        $newCount = Settings::get('contact_form_refused_spam_mails') + 1;
        Settings::set('contact_form_refused_spam_mails', $newCount);
        return $newCount;
    }

    public function _spamCheck(): ?string
    {
        if (Settings::get('spamfilter_enabled') == 'yes') {
            // check if honeypot field is filled
            if (! empty($_POST['my_homepage_url'])) {
                $this->_incSpamCount();
                return get_translation('honeypot_is_not_empty');
            }

            foreach ($_POST as $key => $value) {
                if (Settings::get('disallow_chinese_chars')
                        && AntiSpamHelper::isChinese($_POST[$key])) {
                    $this->_incSpamCount();
                    return get_translation('chinese_chars_not_allowed');
                }

                if (Settings::get('disallow_cyrillic_chars')
                        && AntiSpamHelper::isCyrillic($_POST[$key])) {
                    $this->_incSpamCount();
                    return get_translation('cyrillic_chars_not_allowed');
                }

                if (Settings::get('disallow_rtl_chars')
                        && AntiSpamHelper::isRtl($_POST[$key])) {
                    $this->_incSpamCount();
                    return get_translation('rtl_chars_not_allowed');
                }

                $badwordsCheck = AntiSpamHelper::containsBadwords(
                    $_POST[$key]
                );

                if ($badwordsCheck) {
                    $this->_incSpamCount();
                    return get_translation(
                        'request_contains_badword',
                        [
                            '%word%' => $badwordsCheck
                        ]
                    );
                }
            }

            if (AntiSpamHelper::isCountryBlocked()) {
                $this->_incSpamCount();
                $hostname = @gethostbyaddr(get_ip());
                return get_translation('your_country_is_blocked', [
                    '%hostname%' => $hostname
                ]);
            }

            if (Settings::get('reject_requests_from_bots')
                    && AntiSpamHelper::checkForBot(get_useragent())) {
                $this->_incSpamCount();
                return get_translation('bots_are_not_allowed');
            }
        }
        return null;
    }

    public function beforeHttpHeader(): void
    {
        if (! empty(
            Request::getVar('submit-cms-form')
        ) && Request::isPost()) {
            // apply spam filter if enabled
            $spamCheck = $this->_spamCheck();

            if ($spamCheck) {
                HTMLResult($spamCheck, HttpStatusCode::FORBIDDEN);
            }

            $form_id = Request::getVar('submit-cms-form', null, 'int');
            Forms::submitForm($form_id);
        }
    }
}
