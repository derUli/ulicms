#!/usr/bin/env php
<?php

function usage()
{
    echo "mail_queue - Apply database migrations\n";
    echo "UliCMS Version " . cms_version() . "\n";
    echo "Copyright (C) 2018 by Ulrich Schmidt";
    echo "\n\n";
    echo "Usage php -f mail_queue.php [list|flush|delete] id,id,id \n";
    exit();
}

if (php_sapi_name() != "cli") {
    die("This script can be run from command line only.");
}
$parent_path = dirname(__file__) . "/../";
include $parent_path . "init.php";

array_shift($argv);

if (count($argv) == 0) {
    usage();
}

$action = $argv[0];
$queue = \MailQueue\MailQueue::getInstance();

switch ($action) {
    case "list":
        $mails = $queue->getAllMails();
        echo "mails in queue:\t" . count($mails) . "\n\n";
        echo "ID\t";
        echo "Recipient\t";
        echo "Subject\t";
        echo "Date\t";
        echo "\n";
        echo "\n";
        foreach ($mails as $mail) {
            echo $mail->getID() . "\t";
            echo $mail->getRecipient() . "\t";
            echo $mail->getSubject() . "\t";
            echo \date('Y-m-d H:i:s', $mail->getCreated()) . "\t";
            echo "\n";
        }
        break;
    case "delete":
        if (count($argv) < 2) {
            usage();
        }
        
        $ids = explode(",", $argv[1]);
        $ids = array_map("trim", $ids);
        $ids = array_filter($ids, "strlen");
        $ids = array_map("intval", $ids);
        if (count($ids) <= 0) {
            usage();
        }
        foreach ($ids as $id) {
            try {
                $mail = new \MailQueue\Mail($id);
                echo "Delete mail with ID=$id\n";
                $mail->delete();
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }
    break;
    case "flush":
        $queue->flushMailQueue();
        break;
    default:
        usage();
        break;
}

exit();