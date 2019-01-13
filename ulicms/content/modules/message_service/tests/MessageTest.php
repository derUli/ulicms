<?php
use PHPUnit\Framework\TestCase;
use UliCMS\Exceptions\NotImplementedException;

class ArrayHelperTest extends TestCase
{

    public function tearDown()
    {
        Database::deleteFrom("users", "username like 'testuser%'");
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

    public function testCreateUpdateAndDeleteMessage()
    {
        $testUser1 = $this->setUpTestuser1();
        $testUser2 = $this->setUpTestuser2();
        
        // Create
        $message = new Message();
        // FIXME. receiver and sender should be different users
        $message->setSenderId($testUser1->getId());
        $message->setReceiverId($testUser2->getId());
        $message->setMessage("Foo Bar");
        $message->save();
        
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
        
        // TODO: do update and compare values
        
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
        
        $this->tearDown();
    }

    public function testGetAll()
    {
        throw new NotImplementedException();
    }

    public function testGetAllWithReceiver()
    {
        throw new NotImplementedException();
    }
}