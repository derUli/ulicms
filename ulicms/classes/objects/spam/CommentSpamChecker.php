<?php
namespace UliCMS\Security\SpamChecker;

use StringHelper;
use AntiSpamHelper;

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
            return;
        }
        
        $badwords = $this->spamFilterSettings->getBadwords();
        
        $fields = array(
            "author_name" => $this->comment->getAuthorName(),
            "author_url" => $this->comment->getAuthorUrl(),
            "author_email" => $this->comment->getAuthorEmail(),
            "comment_text" => $this->comment->getContent()
        );
        
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
                $this->errors[] = new SpamDetectionResult(get_translation($field), get_translation("comment_useragent_is_a_bot", array(
                    "%hostname%" => $useragent
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
        return $this->isSpam();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}