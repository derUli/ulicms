<?php

declare(strict_types=1);

function get_action(): string
{
    return BackendHelper::getAction();
}

/**
 * Time trap for trapping spam bots:
 * https://www.stefan.lu/blog/time-trap-anti-spam-technique/
 * @return bool
 */
function _check_form_timestamp(): bool
{
    $original_timestamp = Request::getVar('form_timestamp', 0, 'int');
    $min_time_to_fill_form = Settings::get('min_time_to_fill_form', 'int');
    return ! (time() - $original_timestamp < $min_time_to_fill_form);
}

/**
 * Time trap for trapping spam bots:
 * https://www.stefan.lu/blog/time-trap-anti-spam-technique/
 * @return bool
 */
function check_form_timestamp(): void
{
    if (Settings::get('spamfilter_enabled') !== 'yes') {
        return;
    }

    if (! _check_form_timestamp()) {
        Settings::set(
            'contact_form_refused_spam_mails',
            Settins::get('contact_form_refused_spam_mails') + 1
        );
        HTMLResult('Spam detected based on timestamp.', 400);
    }
}
