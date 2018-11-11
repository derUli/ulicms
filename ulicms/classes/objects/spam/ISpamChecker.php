<?php
namespace UliCMS\Security\SpamChecker;

interface ISpamChecker
{

    public function getErrors();

    public function clearErrors();

    public function doSpamCheck();
}