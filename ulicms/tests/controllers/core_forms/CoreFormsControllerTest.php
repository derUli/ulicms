<?php

class CoreFormsControllerTest extends \PHPUnit\Framework\TestCase {

    public function testIncSpamCount() {
        $controller = new CoreFormsController();
        $initialCount = intval(Settings::get("contact_form_refused_spam_mails"));

        for ($i = 1; $i <= 3; $i++) {
            $oldCount = intval(Settings::get("contact_form_refused_spam_mails"));
            $newCount = $controller->_incSpamCount();
            $this->assertIsInt($newCount);
            $this->assertGreaterThan($oldCount, $newCount);
        }

        $this->assertEquals(
                $initialCount + 3,
                intval(Settings::get("contact_form_refused_spam_mails")));
    }

}
