<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Exceptions\NotImplementedException;

class RegistrationController extends Controller {

    // TODO: move user registration code to this controller
    public function registerPost(): void {
        throw new NotImplementedException(
                        "This Controller will handle user registrations in future"
        );
    }

}
