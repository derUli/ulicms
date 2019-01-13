<?php
use PHPUnit\Framework\TestCase;
use UliCMS\Exceptions\NotImplementedException;

class ArrayHelperTest extends TestCase
{

    public function setUp()
    {
        $this->clean();
    }

    public function tearDown()
    {
        $this->clean();
    }

    public function clean()
    {
        Database::deleteFrom("users", "username like 'testuser%'");
        Database::truncateTable("messages");
    }

    private function setUpTestuser1()
    {
        $user = new User();
        $user->setUsername("testuser1");
        $user->setFirstname("Max");
        $user->setLastname("Muster");
        $user->setGroupId(1);
        $user->setPassword("password123");
        $user->setEmail("max@muster.de");
        $user->setHomepage("http://www.google.de");
        $user->setSkypeId("deruliimnetz");
        $user->setDefaultLanguage("fr");
        $user->setHTMLEditor("ckeditor");
        $user->setTwitter("ulicms");
        $user->setAboutMe("hello world");
        $user->setLastLogin(time());
        $user->save();
        return $user;
    }

    private function setUpTestuser2()
    {
        $user = new User();
        $user->setUsername("testuser2");
        $user->setFirstname("John");
        $user->setLastname("Doe");
        $user->setGroupId(1);
        $user->setPassword("password123");
        $user->setEmail("max@muster.de");
        $user->setHomepage("http://www.google.de");
        $user->setSkypeId("deruliimnetz");
        $user->setDefaultLanguage("fr");
        $user->setHTMLEditor("ckeditor");
        $user->setTwitter("ulicms");
        $user->setAboutMe("hello world");
        $user->setLastLogin(time());
        $user->save();
        return $user;
    }

    private function setUpTestuser3()
    {
        $user = new User();
        $user->setUsername("testuser3");
        $user->setFirstname("John");
        $user->setLastname("Doe");
        $user->setGroupId(1);
        $user->setPassword("password123");
        $user->setEmail("max@muster.de");
        $user->setHomepage("http://www.google.de");
        $user->setSkypeId("deruliimnetz");
        $user->setDefaultLanguage("fr");
        $user->setHTMLEditor("ckeditor");
        $user->setTwitter("ulicms");
        $user->setAboutMe("hello world");
        $user->setLastLogin(time());
        $user->save();
        return $user;
    }

    public function testCreateUpdateAndDeleteMessage()
    {
        $testUser1 = $this->setUpTestuser1();
        $testUser2 = $this->setUpTestuser2();
        $testUser3 = $this->setUpTestuser3();
        
        // Create
        $message = new Message();
        // FIXME. receiver and sender should be different users
        $message->setSenderId($testUser1->getId());
        $message->setReceiverId($testUser2->getId());
        $message->setMessage("Foo Bar");
        $message->send();
        
        $this->assertNotNull($message->getID());
        
        $id = $message->getID();
        
        $message = new Message($id);
        $this->assertNotNull($message->getID());
        $this->assertEquals("Foo Bar", $message->getMessage());
        
        $this->assertEquals($testUser1->getId(), $message->getSenderId());
        $this->assertEquals($testUser2->getId(), $message->getReceiverId());
        
        $this->assertEquals($testUser1->getId(), $message->getSender()
            ->getId());
        $this->assertEquals($testUser2->getId(), $message->getReceiver()
            ->getId());
        
        // Update
        
        $message->setSenderId($testUser2->getId());
        $message->setReceiverId($testUser3->getId());
        $message->setMessage("Another Test");
        $message->send();
        
        $message = new Message($id);
        
        $this->assertEquals("Another Test", $message->getMessage());
        
        $this->assertEquals($testUser2->getId(), $message->getSenderId());
        $this->assertEquals($testUser3->getId(), $message->getReceiverId());
        
        $this->assertEquals($testUser2->getId(), $message->getSender()
            ->getId());
        $this->assertEquals($testUser3->getId(), $message->getReceiver()
            ->getId());
        
        // Delete
        $message->delete();
        $this->assertNull($message->getID());
        
        $message = new Message($id);
        $this->assertNull($message->getID());
        $this->assertNull($message->getMessage());
        $this->assertNull($message->getSenderId());
        $this->assertNull($message->getReceiverId());
        $this->assertNull($message->getSender());
        $this->assertNull($message->getReceiver());
        
        $this->clean();
    }

    public function testGetAll()
    {
        $testUser1 = $this->setUpTestuser1();
        $testUser2 = $this->setUpTestuser2();
        
        for ($i = 1; $i <= 4; $i ++) {
            // Create
            $message = new Message();
            // FIXME. receiver and sender should be different users
            $message->setSenderId($testUser1->getId());
            $message->setReceiverId($testUser2->getId());
            $message->setMessage("Message $i");
            $message->send();
        }
        $allMessages = Message::getAll();
        
        $this->assertCount(4, $allMessages);
        
        $this->clean();
    }

    public function testGetAllWithReceiver()
    {
        $testUser1 = $this->setUpTestuser1();
        $testUser2 = $this->setUpTestuser2();
        $testUser3 = $this->setUpTestuser3();
        
        for ($i = 1; $i <= 4; $i ++) {
            // Create
            $message = new Message();
            // FIXME. receiver and sender should be different users
            $message->setSenderId($testUser1->getId());
            $message->setReceiverId($testUser2->getId());
            $message->setMessage("Message $i");
            $message->send();
        }
        
        for ($i = 1; $i <= 2; $i ++) {
            // Create
            $message = new Message();
            // FIXME. receiver and sender should be different users
            $message->setSenderId($testUser2->getId());
            $message->setReceiverId($testUser3->getId());
            $message->setMessage("Message $i");
            $message->send();
        }
        $allMessages = Message::getAllWithReceiver($testUser3->getId());
        
        $this->assertCount(2, $allMessages);
        
        $this->clean();
    }
    // TODO: Test javascript generate for alerts
}