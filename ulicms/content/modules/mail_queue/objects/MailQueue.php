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

}
//var_dump(MailQueue::getInstance());
