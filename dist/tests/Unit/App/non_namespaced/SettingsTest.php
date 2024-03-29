<?php

class SettingsTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Settings::delete('my_setting');
        Settings::delete('my_setting_de');
        Settings::delete('my_setting_en');
    }

    public function testSettingsNew(): void {
        Settings::delete('example_setting');
        $this->assertEquals(false, Settings::get('example_setting'));

        Settings::register('example_setting', 'hello');
        $this->assertEquals('hello', Settings::get('example_setting'));

        Settings::register('example_setting', 'bye');
        $this->assertEquals('hello', Settings::get('example_setting'));

        Settings::set('example_setting', 'bye');
        $this->assertEquals('bye', Settings::get('example_setting'));

        Settings::delete('example_setting');
        $this->assertEquals(false, Settings::get('example_setting'));
    }

    public function testMappingStringToArray(): void {
        $mappingString = "company.de => de\r\n" .
                "#This is a comment => This should be ignored\r\n" .
                "company.co.uk => en \r\n" .
                "company.fr=>fr\r\n" .
                'foobar';
        $mapped = Settings::mappingStringToArray($mappingString);
        $this->assertEquals(3, count($mapped));
        $this->assertEquals('de', $mapped['company.de']);
        $this->assertEquals('en', $mapped['company.co.uk']);
        $this->assertEquals('fr', $mapped['company.fr']);
        $this->assertFalse(isset($mapped['#This is a comment']));
    }

    public function testGetAndSetLang(): void {
        $manager = new \App\Models\Users\UserManager();
        $users = $manager->getAllUsers();
        $firstUser = $users[0];

        $firstUser->registerSession(false);

        Settings::setLanguageSetting('my_setting', 'Lampukisch');
        Settings::setLanguageSetting('my_setting', 'Germanisch', 'de');
        Settings::setLanguageSetting('my_setting', 'Angelsächisch', 'en');

        $this->assertEquals('Lampukisch', Settings::getLang('my_setting'));
        $this->assertEquals('Lampukisch', Settings::getLang('my_setting', 'fr'));
        $this->assertEquals('Germanisch', Settings::getLang('my_setting', 'de'));
        $this->assertEquals('Angelsächisch', Settings::getLang('my_setting', 'en'));
    }

    public function testGetAndSetLanguageSetting(): void {
        Settings::setLanguageSetting('my_setting', 'Lampukisch');
        Settings::setLanguageSetting('my_setting', 'Germanisch', 'de');
        Settings::setLanguageSetting('my_setting', 'Angelsächisch', 'en');

        $this->assertEquals('Lampukisch', Settings::getLanguageSetting('my_setting'));
        $this->assertEquals('Lampukisch', Settings::getLanguageSetting('my_setting', 'fr'));
        $this->assertEquals('Germanisch', Settings::getLanguageSetting('my_setting', 'de'));
        $this->assertEquals('Angelsächisch', Settings::getLanguageSetting('my_setting', 'en'));
    }

    public function testConvertVarTypeStr(): void {
        $this->assertIsString(Settings::convertVar(2.12, 'str'));
    }

    public function testConvertVarTypeInt(): void {
        $this->assertIsInt(Settings::convertVar(2.12, 'int'));
        $this->assertEquals(2, Settings::convertVar(2.12, 'int'));
    }

    public function testConvertVarTypeFloat(): void {
        $this->assertIsFloat(Settings::convertVar(666, 'float'));
        $this->assertIsFloat(Settings::convertVar(0, 'float'));
    }

    public function testConvertVarTypeBool(): void {
        $this->assertEquals(1, Settings::convertVar(666, 'bool'));
        $this->assertEquals(0, Settings::convertVar(0, 'bool'));

        $this->assertEquals(1, Settings::convertVar('true', 'bool'));
        $this->assertEquals(0, Settings::convertVar('false', 'bool'));

        $this->assertEquals(1, Settings::convertVar('wuff', 'bool'));
        $this->assertEquals(0, Settings::convertVar('', 'bool'));
    }

    public function testGetAllSettings(): void {
        $settings = Settings::getAll();

        $this->assertGreaterThanOrEqual(50, count($settings));

        foreach ($settings as $setting) {
            $this->assertIsNumeric($setting->id);
            $this->assertNotEmpty($setting->name);
            $this->assertTrue(isset($setting->value));
        }
    }
}
