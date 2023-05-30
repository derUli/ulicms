<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Database\DBMigrator;
use App\Helpers\ModuleHelper;
use content\modules\convert_to_seconds\ConvertToSeconds;
use content\modules\convert_to_seconds\TimeUnit;
use PHPUnit\Framework\TestCase;

class BetterCronTest extends TestCase {
    protected function setUp(): void {
        $migrator = new DBMigrator(
            'package/better_cron',
            ModuleHelper::buildRessourcePath('better_cron', 'sql/up')
        );
        $migrator->migrate();
    }

    protected function tearDown(): void {
        $migrator = new DBMigrator(
            'package/better_cron',
            ModuleHelper::buildRessourcePath('better_cron', 'sql/down')
        );
        $migrator->rollback();
    }

    public function testTableExists() {
        $this->assertTrue(Database::tableExists('cronjobs'));
    }

    public function testAfterHTML() {
        $this->assertFalse(defined('CRONJOBS_REGISTERED'));

        $controller = new BetterCron();
        $controller->afterHtml();

        $this->assertTrue(defined('CRONJOBS_REGISTERED'));
    }

    public function testSeconds() {
        $this->doTest(TimeUnit::SECONDS);
    }

    public function testMinutes() {
        $this->doTest(TimeUnit::MINUTES);
    }

    public function testHours() {
        $this->doTest(TimeUnit::HOURS);
    }

    public function testDays() {
        $this->doTest(TimeUnit::DAYS);
    }

    public function testWeeks() {
        $this->doTest(TimeUnit::WEEKS);
    }

    public function testMonths() {
        $this->doTest(TimeUnit::MONTHS);
    }

    public function testYears() {
        $this->doTest(TimeUnit::YEARS);
    }

    public function testDecades() {
        $this->doTest(TimeUnit::DECADES);
    }

    public function testWithControllerCallback() {
        $testIdentifier = 'phpunit/' . uniqid();

        ob_start();
        $this->callBetterCron(
            $testIdentifier,
            TimeUnit::SECONDS,
            'BetterCron::testCallback'
        );
        $this->assertEquals('foo', ob_get_clean());
    }

    public function testWithGlobalMethod() {
        $testIdentifier = 'phpunit/' . uniqid();
        ob_start();
        $this->callBetterCron(
            $testIdentifier,
            TimeUnit::SECONDS,
            'year'
        );

        $this->assertGreaterThanOrEqual(2020, (int)(ob_get_clean()));
    }

    public function testWithNonExistingControllerMethodThrowsException() {
        $testIdentifier = 'phpunit/' . uniqid();
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(
            "Callback method NoController::noMethod for the job {$testIdentifier} doesn't exist"
        );

        $this->callBetterCron(
            $testIdentifier,
            TimeUnit::SECONDS,
            'NoController::noMethod'
        );
    }

    public function testWithNonExistingMethodThrowsException() {
        $testIdentifier = 'phpunit/' . uniqid();

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(
            "Callback method no_method for the job {$testIdentifier} doesn't exist"
        );

        $this->callBetterCron(
            $testIdentifier,
            TimeUnit::SECONDS,
            'no_method'
        );
    }

    public function testWithNotCallableArgumentThrowsException() {
        $testIdentifier = 'phpunit/' . uniqid();

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(
            "Callback of job {$testIdentifier} is not callable"
        );
        $this->callBetterCron(
            $testIdentifier,
            TimeUnit::SECONDS,
            123
        );
    }

    public function callBetterCron(
        string $name,
        TimeUnit $unit,
        $callable,
        $timespan = 2
    ): void {
        $unitMethodName = strtolower($unit->name);
        $methodName = "BetterCron::{$unitMethodName}";
        call_user_func(
            $methodName,
            $name,
            $unit == TimeUnit::SECONDS ? 5 : 2,
            $callable
        );
    }

    public function testGetSettingsHeadline() {
        $controller = new BetterCron();
        $this->assertEqualsIgnoringCase(
            'cronjobs',
            $controller->getSettingsHeadline()
        );
    }

    public function testGetSettings() {
        BetterCron::$currentTime = ConvertToSeconds::convertToSeconds(4, TimeUnit::DECADES);
        BetterCron::seconds('phpunit/foo', 1, static function() {
        });
        BetterCron::seconds('phpunit/bar', 1, static function() {
        });
        $controller = new BetterCron();
        $settingsPage = $controller->settings();
        $this->assertStringContainsString('22.12.2009', $settingsPage);
        $this->assertStringContainsString('phpunit/foo', $settingsPage);
        $this->assertStringContainsString('phpunit/bar', $settingsPage);
    }

    public function testUninstall() {
        $this->assertTrue(Database::tableExists('cronjobs'));

        $controller = new BetterCron();
        $controller->uninstall();

        $this->assertFalse(Database::tableExists('cronjobs'));
    }

    protected function updateCurrentTime(int $time) {
        BetterCron::$currentTime = $time;
    }

    protected function addToCurrentTime(int $time) {
        BetterCron::$currentTime += $time;
    }

    protected function doTest($unit) {
        $testIdentifier = 'phpunit/' . uniqid();

        $this->updateCurrentTime(0);

        BetterCron::updateLastRun($testIdentifier);

        $this->addToCurrentTime(
            ConvertToSeconds::convertToSeconds(4, TimeUnit::DECADES)
        );

        ob_start();
        $this->callBetterCron($testIdentifier, $unit, static function() {
            echo 'foo1';
        });

        $this->assertEquals('foo1', ob_get_clean());

        $allJobs1 = BetterCron::getAllCronjobs();
        $this->assertEquals(BetterCron::$currentTime, $allJobs1[$testIdentifier]);

        $this->addToCurrentTime(
            ConvertToSeconds::convertToSeconds($unit == TimeUnit::SECONDS ? 2 : 1, $unit)
        );

        ob_start();
        $this->callBetterCron($testIdentifier, $unit, static function() {
            echo 'foo2';
        });

        $this->assertEmpty(ob_get_clean());

        $allJobs2 = BetterCron::getAllCronjobs();
        $this->assertEquals($allJobs2[$testIdentifier], $allJobs2[$testIdentifier]);

        $this->addToCurrentTime(
            ConvertToSeconds::convertToSeconds(
                $unit == TimeUnit::SECONDS ? 5 : 1,
                $unit
            )
        );

        ob_start();
        $this->callBetterCron($testIdentifier, $unit, static function() {
            echo 'foo3';
        });
        $this->assertEquals('foo3', ob_get_clean());

        $allJobs3 = BetterCron::getAllCronjobs();
        $this->assertGreaterThan(
            $allJobs2[$testIdentifier],
            $allJobs3[$testIdentifier]
        );
    }
}
