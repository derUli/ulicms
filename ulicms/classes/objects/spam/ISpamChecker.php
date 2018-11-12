<?php
namespace UliCMS\Security\SpamChecker;

interface ISpamChecker
{

    // this must be an array which must return an array of
    // SpamDetectionResults
    public function getErrors();

    // this must be a function which return the errors array
    public function clearErrors();

    // this must perform all configured spam checks
    // and fill the errors array with SpamDetectionResults
    public function doSpamCheck();
}