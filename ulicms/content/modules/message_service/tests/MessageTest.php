<?php
use PHPUnit\Framework\TestCase;
use UliCMS\Exceptions\NotImplementedException;

class ArrayHelperTest extends TestCase
{

    public function testCreateUpdateAndDeleteMessage()
    {
        // Create
        $message = new Message();
        // FIXME. receiver and sender should be different users
        $message->setSenderId(1);
        $message->setReceiverId(1);
        $message->setMessage("Foo Bar");
        $message->save();
        
        $this->assertNotNull($message->getID());
        
        $id = $message->getID();
        
        $message = new Message($id);
        $this->assertNotNull($message->getID());
        $this->assertEquals("Foo Bar", $message->getMessage());
        
        $this->assertEquals(1, $message->getSenderId());
        $this->assertEquals(1, $message->getReceiverId());
        
        $this->assertEquals(1, $message->getSender()
            ->getId());
        $this->assertEquals(1, $message->getReceiver()
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