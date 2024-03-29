<?php

use Spatie\Snapshots\MatchesSnapshots;

class SimpleSettingsControllerTest extends \PHPUnit\Framework\TestCase {
    use MatchesSnapshots;

    /**
     * @var array<string, ?string>
     */
    private array $defaultSettings;

    protected function setUp(): void {
        $this->defaultSettings = [
            'homepage_owner' => Settings::get('homepage_owner'),
            'language' => Settings::get('language'),
            'visitors_can_register' => Settings::get('visitors_can_register'),
            'maintenance_mode' => Settings::get('maintenance_mode'),
            'email' => Settings::get('email'),
            'timezone' => Settings::get('timezone'),
            'robots' => Settings::get('robots'),
            'disable_password_reset' => Settings::get('disable_password_reset'),
        ];
    }

    protected function tearDown(): void {
        $_POST = [];

        foreach ($this->defaultSettings as $key => $value) {
            Settings::set($key, $value);
        }
    }

    public function testSavePostAllSet(): void {
        $_POST = $this->getPost();
        $_POST['disable_password_reset'] = '1';

        $controller = new SimpleSettingsController();
        $controller->_savePost();

        $this->assertEquals(
            'Jane Doe',
            Settings::get('homepage_owner')
        );

        $this->assertEquals(
            'en',
            Settings::get('language')
        );

        $this->assertEquals(
            '1',
            Settings::get('visitors_can_register')
        );

        $this->assertEquals(
            '1',
            Settings::get('maintenance_mode')
        );

        $this->assertEquals(
            'foobar@example.org',
            Settings::get('email')
        );

        $this->assertEquals(
            'Asia/Tokyo',
            Settings::get('timezone')
        );

        $this->assertEquals(
            'index, nofollow',
            Settings::get('robots')
        );

        $this->assertNull(
            Settings::get('disable_password_reset')
        );
    }

    public function testSavePostNothingSet(): void {
        $_POST = $this->getPost();

        $controller = new SimpleSettingsController();
        $controller->_savePost();

        $this->assertEquals(
            'Jane Doe',
            Settings::get('homepage_owner')
        );

        $this->assertEquals(
            'en',
            Settings::get('language')
        );

        $this->assertEquals(
            '1',
            Settings::get('visitors_can_register')
        );

        $this->assertEquals(
            '1',
            Settings::get('maintenance_mode')
        );

        $this->assertEquals(
            'foobar@example.org',
            Settings::get('email')
        );

        $this->assertEquals(
            'Asia/Tokyo',
            Settings::get('timezone')
        );

        $this->assertEquals(
            'index, nofollow',
            Settings::get('robots')
        );

        $this->assertEquals(
            'disable_password_reset',
            Settings::get('disable_password_reset')
        );

        $this->assertEquals(
            'disable_password_reset',
            Settings::get('disable_password_reset')
        );
    }

    public function testGetTimezones(): void {
        $controller = new SimpleSettingsController();
        $timezones = $controller->getTimezones();
        $this->assertStringContainsString('<option value="Asia/Tokyo">', $timezones);

        $this->assertGreaterThan(400, substr_count($timezones, '<option'));
        $this->assertEquals(10, substr_count($timezones, '<optgroup'));
    }

    protected function getPost(): array {
        return [
            'homepage_owner' => 'Jane Doe',
            'language' => 'en',
            'visitors_can_register' => '1',
            'maintenance_mode' => '1',
            'email' => 'foobar@example.org',
            'timezone' => 'Asia/Tokyo',
            'robots' => 'index, nofollow',
        ];
    }
}
