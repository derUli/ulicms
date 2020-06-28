<?php

declare(strict_types=1);

function get_action(): string
{
    return BackendHelper::getAction();
}

function set_format(string $format): void
{
    $_GET["format"] = trim($format, ".");
}

function get_format(): string
{
    return (isset($_GET["format"]) and is_present($_GET["format"])) ? $_GET["format"] : "html";
}


function check_form_timestamp(): void
{
    if (Settings::get("spamfilter_enabled") != "yes") {
        return;
    }

    $original_timestamp = Request::getVar("form_timestamp", 0, "int");
    $min_time_to_fill_form = Settings::get("min_time_to_fill_form", "int");
    if (time() - $original_timestamp < $min_time_to_fill_form) {
        setconfig(
            "contact_form_refused_spam_mails",
            getconfig("contact_form_refused_spam_mails") + 1
        );
        HTMLResult("Spam detected based on timestamp.", 400);
    }
}
