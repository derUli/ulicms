<?php

use App\Helpers\TestHelper;

class RequestFunctionsTest extends \PHPUnit\Framework\TestCase
{
    public function testGetActionIsSet()
    {
        $_REQUEST['action'] = 'pages';
        $this->assertEquals('pages', get_action());
        unset($_REQUEST['action']);
    }

    public function testGetActionIsNotSet()
    {
        $this->assertEquals('home', get_action());
    }

        public function testCheckFormTimestampReturnsTrue()
        {
            Settings::set('min_time_to_fill_form', 3);
            $_POST['form_timestamp'] = time() - 4;
            $this->assertTrue(_check_form_timestamp());
        }

    public function testCheckFormTimestampReturnsFalse()
    {
        Settings::set('min_time_to_fill_form', 3);
        $_POST['form_timestamp'] = time() - 1;
        $this->assertFalse(_check_form_timestamp());
    }
}
