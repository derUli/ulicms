<?php
namespace UliCMS\Security\SpamChecker;

use UliCMS\Exceptions\NotImplementedException;
use AntiSpamHelper;
use UliCMS\Security\SpamChecker\SpamDetectionResult;

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
            "content" => $this->comment->getContent()
        );
        
        foreach ($fields as $field->$value) {
            $badword = AntispamHelper::containsBadwords($value, $badwords);
            if ($badword != null) {
                $this->errors[] = new SpamDetectionResult($field, $badword);
            }
        }
        
        throw new NotImplementedException();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}