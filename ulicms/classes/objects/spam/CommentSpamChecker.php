<?php
namespace UliCMS\Security\SpamChecker;

use StringHelper;
use AntiSpamHelper;
use Request;

class CommentSpamChecker implements ISpamChecker
{

    private $comment;

    private $spamFilterSettings;

    public function __construct($comment, $spamFilterSettings)
    {
        $this->comment = $comment;
        $this->spamFilterSettings = $spamFilterSettings;
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
        if (! $this->spamFilterSettings->getSpamFilterEnabled()) {
            return self::isSpam();
        }
        
        $badwords = $this->spamFilterSettings->getBadwords();
        
        $fields = array(
            "author_name" => $this->comment->getAuthorName(),
            "author_url" => $this->comment->getAuthorUrl(),
            "author_email" => $this->comment->getAuthorEmail(),
            "comment_text" => $this->comment->getContent()
        );
        
        // check if Antispam Honeypot is not empty
        if (Request::hasVar("my_homepage_url")) {
            $this->errors[] = new SpamDetectionResult(get_translation("honeypot"), get_translation("honeypot_is_not_empty"));
        }
        
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
        $useragent = $this->comment->getUserAgent();
        $rejectRequestsFromBots = $this->spamFilterSettings->getRejectRequestsFromBots();
        if (StringHelper::isNotNullOrWhitespace($useragent) && $rejectRequestsFromBots) {
            if (AntiSpamHelper::checkForBot($useragent)) {
                $this->errors[] = new SpamDetectionResult(get_translation("useragent"), get_translation("bots_are_not_allowed", array(
                    "%useragent%" => $useragent
                )));
            }
        }
        
        $email = $this->comment->getAuthorEmail();
        $checkMxOfEmailAddress = $this->spamFilterSettings->getCheckMxOfMailAddress();
        
        if (StringHelper::isNotNullOrWhitespace($email) && $checkMxOfEmailAddress) {
            if (! AntiSpamHelper::checkMailDomain($email)) {
                $this->errors[] = new SpamDetectionResult(get_translation("author_email"), get_translation("mail_address_has_invalid_mx_entry", array(
                    "%hostname%" => $hostname
                )));
            }
        }
        
        if ($this->spamFilterSettings->getDisallowChineseChars()) {
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
        
        if ($this->spamFilterSettings->getDisallowCyrillicChars()) {
            foreach ($fields as $field => $value) {
                if ($value != null) {
                    if (AntiSpamHelper::isCyrillic($value)) {
                        $this->errors[] = new SpamDetectionResult(get_translation($field), get_translation("cyrillic_charts_not_allowed", array(
                            "%field%" => get_translation($field)
                        )));
                    }
                }
            }
        }
        
        $countries = $this->spamFilterSettings->getBlockedCountries();
        $ip = $this->comment->getIp();
        
        if (! is_null($ip) && AntiSpamHelper::isCountryBlocked($ip, $countries)) {
            $hostname = @gethostbyaddr($ip);
            $this->errors[] = new SpamDetectionResult(get_translation("ip_address"), get_translation("your_country_is_blocked", array(
                "%hostname%" => $hostname
            )));
        }
        
        return $this->isSpam();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}