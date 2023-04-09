<?php

use App\Security\PrivacyCheckbox;

class PrivacyCheckboxTest extends \PHPUnit\Framework\TestCase
{
    private $privacy_policy_checkbox_enable_de;

    private $privacy_policy_checkbox_enable_en;

    private $privacy_policy_checkbox_text_de;

    private $privacy_policy_checkbox_text_en;

    protected function setUp(): void
    {
        $this->privacy_policy_checkbox_enable_de = Settings::get('privacy_policy_checkbox_enable_de');
        $this->privacy_policy_checkbox_enable_en = Settings::get('privacy_policy_checkbox_enable_en');
        $this->privacy_policy_checkbox_text_de = Settings::get('privacy_policy_checkbox_text_de');
        $this->privacy_policy_checkbox_text_en = Settings::get('privacy_policy_checkbox_text_en');
    }

    protected function tearDown(): void
    {
        if ($this->privacy_policy_checkbox_enable_de) {
            Settings::set('privacy_policy_checkbox_enable_de', '1');
        } else {
            Settings::delete('privacy_policy_checkbox_enable_de');
        }
        if ($this->privacy_policy_checkbox_enable_en) {
            Settings::set('privacy_policy_checkbox_enable_en', '1');
        } else {
            Settings::delete('privacy_policy_checkbox_enable_en');
        }
        Settings::set('privacy_policy_checkbox_text_de', $this->privacy_policy_checkbox_text_de);
        Settings::set('privacy_policy_checkbox_text_de', $this->privacy_policy_checkbox_text_en);

        unset($_POST[PrivacyCheckbox::CHECKBOX_NAME]);
        unset($_GET[PrivacyCheckbox::CHECKBOX_NAME]);
    }

    public function testIsEnabled()
    {
        $languages = [
            'de',
            'en'
        ];
        foreach ($languages as $language) {
            Settings::set("privacy_policy_checkbox_enable_{$language}", 1);
            $checkbox = new PrivacyCheckbox($language);
            $this->assertTrue($checkbox->isEnabled());
            Settings::delete("privacy_policy_checkbox_enable_{$language}");
            $this->assertFalse($checkbox->isEnabled());
        }
    }

    public function testRender()
    {
        $values = [
            'de' => '<div class="checkbox"><label>[checkbox] Lorem Ipsum</label></div>',
            'en' => '<div class="checkbox"><label>[checkbox] Sit dor amet</label></div>'
        ];
        $expectedResults = [
            'de' => '<div class="checkbox"><label><input type="checkbox" name="accept_privacy_policy" value="✔" required="required" id="accept_privacy_policy"> Lorem Ipsum</label></div>',
            'en' => '<div class="checkbox"><label><input type="checkbox" name="accept_privacy_policy" value="✔" required="required" id="accept_privacy_policy"> Sit dor amet</label></div>'
        ];
        foreach ($values as $language => $html) {
            Settings::delete("privacy_policy_checkbox_text_{$language}");
            $checkbox = new PrivacyCheckbox($language);
            $this->assertEmpty($checkbox->render());

            Settings::set("privacy_policy_checkbox_enable_{$language}", 1);
            Settings::set("privacy_policy_checkbox_text_{$language}", $html);

            $this->assertNotEmpty($checkbox->render());
            $this->assertEquals($expectedResults[$language], $checkbox->render());
        }
    }

    public function testIsCheckboxCheckedPost()
    {
        $checkbox = new PrivacyCheckbox('en');
        $this->assertFalse($checkbox->isChecked());
        $_POST[PrivacyCheckbox::CHECKBOX_NAME] = '1';
        $this->assertTrue($checkbox->isChecked());
        unset($_POST[PrivacyCheckbox::CHECKBOX_NAME]);
    }

    public function testIsCheckboxCheckedGet()
    {
        $checkbox = new PrivacyCheckbox('en');
        $this->assertFalse($checkbox->isChecked());
        $_GET[PrivacyCheckbox::CHECKBOX_NAME] = '1';
        $this->assertTrue($checkbox->isChecked());
        unset($_GET[PrivacyCheckbox::CHECKBOX_NAME]);
    }
}
