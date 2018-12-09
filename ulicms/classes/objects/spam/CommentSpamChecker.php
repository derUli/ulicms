<?php
namespace UliCMS\Security\SpamChecker;

use StringHelper;
use AntiSpamHelper;
use Request;

class CommentSpamChecker implements ISpamChecker
{

    private $comment;

    private $spamFilterConfiguration;

    // Constructor takes the Comment to check and
    // the SpamFilterFonuration
    public function __construct($comment, $spamFilterConfiguration)
    {
        $this->comment = $comment;
        $this->spamFilterConfiguration = $spamFilterConfiguration;
    }

    private $errors = array();

    public function clearErrors()
    {
        $this->errors = array();
    }

    public function isSpam()
    {
        return count($this->errors) > 0;
    }

    public function doSpamCheck()
    {
        $this->clearErrors();
        
        // Abort here if the spam filter is disabled
        if (! $this->spamFilterConfiguration->getSpamFilterEnabled()) {
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
            $this->errors[] = new SpamDetectionResult(get_translation("honeypot"), get_translation("honeypot_is_not_empty"));
        }
        
        // Check if any of the $fields contains at least one badword
        foreach ($fields as $field => $value) {
            if ($value != null) {
                $badword = AntispamHelper::containsBadwords($value, $badwords);
                if ($badword != null) {
                    $this->errors[] = new SpamDetectionResult(get_translation($field), get_translation("comment_contains_badword", array(
                        "%field%" => get_translation($field),
                        "%word%" => $badword
                    )));
                }
            }
        }
        
        // If the option "Reject Requests from Bots" is enabled
        // check the useragent
        $useragent = $this->comment->getUserAgent();
        $rejectRequestsFromBots = $this->spamFilterConfiguration->getRejectRequestsFromBots();
        if (StringHelper::isNotNullOrWhitespace($useragent) && $rejectRequestsFromBots) {
            if (AntiSpamHelper::checkForBot($useragent)) {
                $this->errors[] = new SpamDetectionResult(get_translation("useragent"), get_translation("bots_are_not_allowed", array(
                    "%useragent%" => $useragent
                )));
            }
        }
        
        // If the option "Check DNS MX Entry of email addresses" is enabled check the mail domain
        $email = $this->comment->getAuthorEmail();
        $checkMxOfEmailAddress = $this->spamFilterConfiguration->getCheckMxOfMailAddress();
        
        if (StringHelper::isNotNullOrWhitespace($email) && $checkMxOfEmailAddress) {
            if (! AntiSpamHelper::checkMailDomain($email)) {
                $this->errors[] = new SpamDetectionResult(get_translation("author_email"), get_translation("mail_address_has_invalid_mx_entry", array(
                    "%hostname%" => $hostname
                )));
            }
        }
        // If the option "Disallow Chinese Chars" is enabled, check $fields contains chinese chars
        if ($this->spamFilterConfiguration->getDisallowChineseChars()) {
            foreach ($fields as $field => $value) {
                if ($value != null) {
                    if (AntiSpamHelper::isChinese($value)) {
                        $this->errors[] = new SpamDetectionResult(get_translation($field), get_translation("chinese_chars_not_allowed", array(
                            "%field%" => get_translation($field)
                        )));
                    }
                }
            }
        }
        // If the option "Disallow Cyrillic Chars" is enabled, check if $fields contains chinese chars
        if ($this->spamFilterConfiguration->getDisallowCyrillicChars()) {
            foreach ($fields as $field => $value) {
                if ($value != null) {
                    if (AntiSpamHelper::isCyrillic($value)) {
                        $this->errors[] = new SpamDetectionResult(get_translation($field), get_translation("cyrillic_chars_not_allowed", array(
                            "%field%" => get_translation($field)
                        )));
                    }
                }
            }
        }
        // If the option "Disallow Right-To-Left Languages" is enabled, check if $fields contains arabic, hebrew or persian chars
        if ($this->spamFilterConfiguration->getDisallowRtlChars()) {
            foreach ($fields as $field => $value) {
                if ($value != null) {
                    if (AntiSpamHelper::isRtl($value)) {
                        $this->errors[] = new SpamDetectionResult(get_translation($field), get_translation("rtl_chars_not_allowed", array(
                            "%field%" => get_translation($field)
                        )));
                    }
                }
            }
        }
        // If there are blocked countries set, do a reverse lookup for the ip address of the comment author and check the domain ending.
        $countries = $this->spamFilterConfiguration->getBlockedCountries();
        $ip = $this->comment->getIp();
        
        if (! is_null($ip) && AntiSpamHelper::isCountryBlocked($ip, $countries)) {
            $hostname = @gethostbyaddr($ip);
            $this->errors[] = new SpamDetectionResult(get_translation("ip_address"), get_translation("your_country_is_blocked", array(
                "%hostname%" => $hostname
            )));
        }
        // return true if the comment is detected as spam
        return $this->isSpam();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}