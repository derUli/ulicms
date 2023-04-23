<?php

use App\Exceptions\NotImplementedException;

class RegistrationsControllerTest extends \PHPUnit\Framework\TestCase {
    // Not implemented yet.
    public function testRegisterPost() {
        $this->expectException(NotImplementedException::class);
        $controller = new RegistrationController();
        $controller->registerPost();
    }
}
