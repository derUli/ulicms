<?php


class RequestFunctionsTest extends \PHPUnit\Framework\TestCase {
    public function testGetActionIsSet(): void {
        $_REQUEST['action'] = 'pages';
        $this->assertEquals('pages', get_action());
        unset($_REQUEST['action']);
    }

    public function testGetActionIsNotSet(): void {
        $this->assertEquals('home', get_action());
    }

        public function testCheckFormTimestampReturnsTrue(): void {
            Settings::set('min_time_to_fill_form', 3);
            $_POST['form_timestamp'] = time() - 4;
            $this->assertTrue(_check_form_timestamp());
        }

    public function testCheckFormTimestampReturnsFalse(): void {
        Settings::set('min_time_to_fill_form', 3);
        $_POST['form_timestamp'] = time() - 1;
        $this->assertFalse(_check_form_timestamp());
    }
}
