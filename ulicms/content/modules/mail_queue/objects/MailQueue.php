<?php
namespace MailQueue;
class MailQueue{
    private static $instance;
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getAllMails(){
        throw new NotImplementedException();
    }
    public function getNextMail(){
        throw new NotImplementedException();
    }
    public function flushMailQueue(){
        throw new NotImplementedException();
    }
    public function addMail($mail){
        throw new NotImplementedException();
    }
    public function removeMail($mail){
        throw new NotImplementedException();
    }
}
//var_dump(MailQueue::getInstance());
