<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Controllers\MainClass;
use App\Database\DBMigrator;
use App\Helpers\ModuleHelper;
use content\modules\convert_to_seconds\ConvertToSeconds;
use content\modules\convert_to_seconds\TimeUnit;

// This module provides methods to run functions in a regular interval
class BetterCron extends MainClass {
    public static $currentTime = null;

    public function afterHtml(): void {
        do_event('register_cronjobs');
    }

    // Run a method every X seconds
    public static function seconds(string $job, int $seconds, $callback): void {
        if (! is_string($callback) && ! is_callable($callback)) {
            throw new BadMethodCallException(
                "Callback of job {$job} is not callable"
            );
        }

        // When was the last run of this job?
        $currentTime = self::$currentTime ?? time();
        $last_run = self::getLastRun($job);

        // Is the time range between the last run and now larger or equal to
        // $seconds?
        // If not then abort here.
        if ($currentTime - $last_run < $seconds) {
            return;
        }

        // Callback can be a controller method name as string
        // e.g. MyController::myMethod
        if (is_string($callback)) {
            // if $callback is a string without ::
            // then it is a normal (non controller) method name
            self::updateLastRun($job);
            self::executeStringCallback($callback, $job);
        } elseif (is_callable($callback)) {
            // if $callback is a callbable function then execute it
            self::updateLastRun($job);
            $callback();
        }
    }

    // Run a method every X minutes
    public static function minutes(string $job, int $minutes, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($minutes, TimeUnit::MINUTES),
            $callback
        );
    }

    // Run a method every X hours
    public static function hours(string $job, int $hours, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($hours, TimeUnit::HOURS),
            $callback
        );
    }

    // Run a method every X days
    public static function days(string $job, int $hours, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($hours, TimeUnit::DAYS),
            $callback
        );
    }

    // Run a method every X weeks
    public static function weeks(string $job, int $weeks, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($weeks, TimeUnit::WEEKS),
            $callback
        );
    }

    // Run a method every X months
    public static function months(string $job, int $months, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($months, TimeUnit::MONTHS),
            $callback
        );
    }

    // Run a method every X years
    public static function years(string $job, int $years, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($years, TimeUnit::YEARS),
            $callback
        );
    }

    public static function decades(string $job, int $decades, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($decades, TimeUnit::DECADES),
            $callback
        );
    }

    // update the last run date of a cronjob
    public static function updateLastRun(string $name): void {
        // if this job exists update in database do an sql update else
        // an sql insert
        $query = Database::selectAll('cronjobs', ['name'], 'name = ?', [$name]);

        $currentTime = is_numeric(self::$currentTime) ?
                self::$currentTime : time();

        $args = Database::any($query) ? [
            $currentTime,
            $name
        ] : [
            $name,
            $currentTime
        ];

        $sql = Database::any($query) ?
                'update `{prefix}cronjobs` set last_run = ? where name = ?' :
                'insert into `{prefix}cronjobs` (name, last_run) '
                . 'values(?, ?)';

        Database::pQuery(
            $sql,
            $args,
            true
        );
    }

    // get all cronjobs in database as array of
    // name => timestamp
    public static function getAllCronjobs(): array {
        $cronjobs = [];
        $query = Database::query(
            'select name, last_run from `{prefix}cronjobs` '
                        . 'order by name',
            true
        );
        while ($row = Database::fetchObject($query)) {
            $cronjobs[$row->name] = (int)($row->last_run);
        }
        return $cronjobs;
    }

    // Settins page
    public function settings(): string {
        return Template::executeModuleTemplate('better_cron', 'list.php');
    }

    // As the method name says translates the headline for the module's settings
    // page
    public function getSettingsHeadline(): string {
        return get_translation('cronjobs');
    }

    // before uninstall rollback migrations (Drop cronjobs Table)
    public function uninstall(): void {
        $migrator = new DBMigrator(
            'package/better_cron',
            ModuleHelper::buildRessourcePath('better_cron', 'sql/down')
        );
        $migrator->rollback();
    }

    public function testCallback() {
        if (is_cli()) {
            echo 'foo';
        }
    }

    public function registerCronjobs() {
        if (is_cli()) {
            defined('CRONJOBS_REGISTERED') || define('CRONJOBS_REGISTERED', true);
        }
    }

    protected static function executeStringCallback(string $callback, $job) {
        // Callback can be a controller method name as string
        // e.g. MyController::myMethod
        if (str_contains($callback, '::')) {
            self::executeControllerCallback($callback, $job);
        } else {
            // if $callback is a string without ::
            // then it is a normal (non controller) method name
            self::executeCallbackFunction($callback, $job);
        }
    }

    protected static function executeCallbackFunction(string $callback, string $job): void {
        if (function_exists($callback)) {
            // update last run for this job before running it
            // to prevent running the job multiple at the same time
            self::updateLastRun($job);
            call_user_func($callback);
        } else {
            throw new BadMethodCallException(
                "Callback method {$callback} for the job {$job} doesn't exist"
            );
        }
    }

    // parse a string in the format MyController::myMethod and call
    // a controller action (if it exists)
    protected static function executeControllerCallback(string $callback, string $job): void {
        $args = explode('::', $callback);
        $sClass = $args[0];
        $sMethod = $args[1];
        // If this method exists, execute it
        // FIXME: if the job doesn't exists log an error
        if (ControllerRegistry::get($sClass) &&
                method_exists(ControllerRegistry::get($sClass), $sMethod)) {
            ControllerRegistry::get($sClass)->{$sMethod}();
        } else {
            throw new BadMethodCallException(
                "Callback method {$callback} for the job {$job} doesn't exist"
            );
        }
    }

    // returns the timestamp when did a job run the last time
    // if not run yet return 0 (year 1970)
    private static function getLastRun($name): int {
        $last_run = 0;

        $query = Database::pQuery(
            'select last_run from `{prefix}cronjobs` where name = ?',
            [
                $name
            ],
            true
        );
        if (Database::any($query)) {
            $result = Database::fetchObject($query);
            $last_run = (int)($result->last_run);
        }
        return $last_run;
    }
}
