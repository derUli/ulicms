<?php

declare(strict_types=1);

namespace UliCMS\Security\SpamChecker;

use StringHelper;
use AntiSpamHelper;
use Request;
use UliCMS\Models\Content\Comment;

class CommentSpamChecker implements ISpamChecker {

    private $comment;
    private $spamFilterConfiguration;

    // Constructor takes the Comment to check and
    // the SpamFilterFonuration
    public function __construct(
            Comment $comment,
            SpamFilterConfiguration $spamFilterConfiguration
    ) {
        $this->comment = $comment;
        $this->spamFilterConfiguration = $spamFilterConfiguration;
    }

    private $errors = [];

    public function clearErrors(): void {
        $this->errors = [];
    }

    public function isSpam(): bool {
        return count($this->errors) > 0;
    }

    public function doSpamCheck(): bool {
        $this->clearErrors();

        // Abort here if the spam filter is disabled
        if (!$this->spamFilterConfiguration->getSpamFilterEnabled()) {
            return self::isSpam();
        }

        $badwords = $this->spamFilterConfiguration->getBadwords();

        // The fields to check for spam
        $fields = array(
            "author_name" => $this->comment->getAuthorName(),
            "author_url" => $this->comment->getAuthorUrl(),
            "author_email" => $this->comment->getAuthorEmail(),
            "comment_text" => $this->comment->getText()
        );

        // check if Antispam Honeypot is not empty
        if (StringHelper::isNotNullOrEmpty(Request::getVar("my_homepage_url"))) {
            $this->errors[] = new SpamDetectionResult(
                    get_translation("honeypot"),
                    get_translation("honeypot_is_not_empty")
            );
        }

        // Check if any of the $fields contains at least one badword
        foreach ($fields as $field => $value) {
            if ($value != null) {
                $badword = AntispamHelper::containsBadwords($value, $badwords);
                if ($badword !== null) {
                    $message = get_translation(
                            "comment_contains_badword",
                            [
                                "%field%" => get_translation($field),
                                "%word%" => $badword
                            ]
                    );
                    $this->errors[] = new SpamDetectionResult(
                            get_translation($field),
                            $message
                    );
                }
            }
        }

        // If the option "Reject Requests from Bots" is enabled
        // check the useragent
        $useragent = $this->comment->getUserAgent();
        $rejectRequestsFromBots = $this->spamFilterConfiguration->getRejectRequestsFromBots();
        if (StringHelper::isNotNullOrWhitespace($useragent) and
                $rejectRequestsFromBots) {
            if (AntiSpamHelper::checkForBot($useragent)) {
                $this->errors[] = new SpamDetectionResult(
                        get_translation("useragent"),
                        get_translation("bots_are_not_allowed", array(
                            "%useragent%" => $useragent
                        ))
                );
            }
        }

        // If the option "Check DNS MX Entry of email addresses" is enabled check the mail domain
        $email = $this->comment->getAuthorEmail();
        $checkMxOfEmailAddress = $this->spamFilterConfiguration->getCheckMxOfMailAddress();
        if (StringHelper::isNotNullOrWhitespace($email) &&
                $checkMxOfEmailAddress) {
            if (!AntiSpamHelper::checkMailDomain($email)) {
                $this->errors[] = new SpamDetectionResult(
                        get_translation("author_email"),
                        get_translation("mail_address_has_invalid_mx_entry")
                );
            }
        }
        // If the option "Disallow Chinese Chars" is enabled,
        // check $fields contains chinese chars
        if ($this->spamFilterConfiguration->getDisallowChineseChars()) {
            foreach ($fields as $field => $value) {
                if ($value != null) {
                    if (AntiSpamHelper::isChinese($value)) {
                        $message = get_translation(
                                "chinese_chars_not_allowed",
                                [
                                    "%field%" => get_translation($field)
                                ]
                        );
                        $this->errors[] = new SpamDetectionResult(
                                get_translation($field),
                                $message
                        );
                    }
                }
            }
        }
        // If the option "Disallow Cyrillic Chars" is enabled, check if $fields contains chinese chars
        if ($this->spamFilterConfiguration->getDisallowCyrillicChars()) {
            foreach ($fields as $field => $value) {
                if ($value != null) {
                    if (AntiSpamHelper::isCyrillic($value)) {
                        $message = get_translation(
                                "cyrillic_chars_not_allowed",
                                [
                                    "%field%" => get_translation($field)
                                ]
                        );
                        $this->errors[] = new SpamDetectionResult(
                                get_translation($field),
                                $message
                        );
                    }
                }
            }
        }
        // If the option "Disallow Right-To-Left Languages" is enabled,
        // check if $fields contains arabic, hebrew or persian chars
        if ($this->spamFilterConfiguration->getDisallowRtlChars()) {
            foreach ($fields as $field => $value) {
                if ($value != null) {
                    if (AntiSpamHelper::isRtl($value)) {
                        $this->errors[] = new SpamDetectionResult(
                                get_translation($field),
                                get_translation("rtl_chars_not_allowed", [
                                    "%field%" => get_translation($field)
                                ])
                        );
                    }
                }
            }
        }
        // If there are blocked countries set, do a reverse lookup
        // for the ip address of the comment author and check the domain ending.
        $countries = $this->spamFilterConfiguration->getBlockedCountries();
        $ip = $this->comment->getIp();

        if (!is_null($ip) && AntiSpamHelper::isCountryBlocked($ip, $countries)) {
            $hostname = @gethostbyaddr($ip);
            $message = get_translation(
                    "your_country_is_blocked",
                    [
                        "%hostname%" => $hostname
                    ]
            );
            $this->errors[] = new SpamDetectionResult(
                    get_translation("ip_address"),
                    $message
            );
        }
        // return true if the comment is detected as spam
        return $this->isSpam();
    }

    public function getErrors(): array {
        return $this->errors;
    }

}
