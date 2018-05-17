<?php
use MailQueue;
use PHPUnit_Framework_TestCase;
class MailQueueTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$queue = MailQueue\MailQueue::getInstance();
		$queue->flushMailQueue();
	}
	public function testSendMails(){
		$queue = MailQueue\MailQueue::getInstance();
		for($i=1; $i <= 100; $i++){
			$mail = new MailQueue\Mail();
			$mail->setRecipient("receiver{$i}@example.org");
			$mail->setSubject("Subject $i");
			$mail->setMessage("Message $i");
			$mail->setHeaders("From: foo@bar.de");
			$queue->addMail($mail);
		}
	}
}