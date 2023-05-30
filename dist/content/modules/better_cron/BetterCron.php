<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Controllers\MainClass;
use App\Database\DBMigrator;
use App\Helpers\ModuleHelper;
use content\modules\convert_to_seconds\ConvertToSeconds;
use content\modules\convert_to_seconds\TimeUnit;

/**
 * Provides utils to execute methods in an interval
 */
class BetterCron extends MainClass {
    public static ?int $currentTime = null;

    /**
     * Register cronjobs after HTML output
     *
     * @return void
     */
    public function afterHtml(): void {
        do_event('register_cronjobs');
    }

    /**
     * Run a method any X seconds
     *
     * @param string $job
     * @param int $seconds
     * @param mixed $callback
     *
     * @return void
     */
    public static function seconds(string $job, int $seconds, mixed $callback): void {
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

    /**
     * Run a method any X minutes
     *
     * @param string $job
     * @param int $minutes
     * @param mixed $callback
     *
     * @return void
     */
    public static function minutes(string $job, int $minutes, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($minutes, TimeUnit::MINUTES),
            $callback
        );
    }

    /**
     * Run a method any X hours
     *
     * @param string $job
     * @param int $hours
     * @param mixed $callback
     *
     * @return void
     */
    public static function hours(string $job, int $hours, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($hours, TimeUnit::HOURS),
            $callback
        );
    }

    /**
     * Run a method any X days
     *
     * @param string $job
     * @param int $days
     * @param mixed $callback
     *
     * @return void
     */
    public static function days(string $job, int $days, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($days, TimeUnit::DAYS),
            $callback
        );
    }

    /**
     * Run a method any X weeks
     *
     * @param string $job
     * @param int $weeks
     * @param mixed $callback
     *
     * @return void
     */
    public static function weeks(string $job, int $weeks, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($weeks, TimeUnit::WEEKS),
            $callback
        );
    }

    /**
     * Run a method any X months
     *
     * @param string $job
     * @param int $months
     * @param mixed $callback
     *
     * @return void
     */
    public static function months(string $job, int $months, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($months, TimeUnit::MONTHS),
            $callback
        );
    }

    /**
     * Run a method any X years
     *
     * @param string $job
     * @param int $years
     * @param mixed $callback
     *
     * @return void
     */
    public static function years(string $job, int $years, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($years, TimeUnit::YEARS),
            $callback
        );
    }

    /**
     * Run a method any X decades
     *
     * @param string $job
     * @param int $decades
     * @param mixed $callback
     *
     * @return void
     */
    public static function decades(string $job, int $decades, $callback): void {
        self::seconds(
            $job,
            ConvertToSeconds::convertToSeconds($decades, TimeUnit::DECADES),
            $callback
        );
    }

    /**
     * update the last run date of a cronjob
     *
     * @param string $name
     *
     * @return void
     */
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

    /**
     * Get all cronjobs in database as array of name => timestamp
     *
     * @return array<string, int>
     */
    public static function getAllCronjobs(): array {
        $cronjobs = [];
        $query = Database::query(
            'select name, last_run from `{prefix}cronjobs` '
                        . 'order by name',
            true
        );
        while ($row = Database::fetchObject($query)) {
            $cronjobs[(string)$row->name] = (int)($row->last_run);
        }
        return $cronjobs;
    }

    /**
     * Render settings page html
     *
     * @return string
     */
    public function settings(): string {
        return Template::executeModuleTemplate('better_cron', 'list.php');
    }

    /**
     * Get headline for admin settings page
     *
     * @return string
     */
    public function getSettingsHeadline(): string {
        return get_translation('cronjobs');
    }

    /**
     * Before uninstall drop better_cron table
     *
     * @return void
     */
    public function uninstall(): void {
        $migrator = new DBMigrator(
            'package/better_cron',
            ModuleHelper::buildRessourcePath('better_cron', 'sql/down')
        );
        $migrator->rollback();
    }

    /**
     * Callback for unit tests
     *
     * @return void
     */
    public function testCallback(): void {
        if (is_cli()) {
            echo 'foo';
        }
    }

    /**
     * Register cronjobs
     * Only used for unit tests
     *
     * @return void
     */
    public function registerCronjobs(): void {
        if (is_cli()) {
            defined('CRONJOBS_REGISTERED') || define('CRONJOBS_REGISTERED', true);
        }
    }

    /**
     * Execute string callback
     *
     * @param string $callback
     * @param mixed $job
     *
     * @return void
     */
    protected static function executeStringCallback(string $callback, mixed $job): void {
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

    /**
     * Execute callback function
     *
     * @param string $callback
     * @param string $job
     *
     * @return  void
     */
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

    /**
     * parse a string in the format MyController::myMethod and call
     * a controller action (if it exists)
     *
     * @param string $callback
     * @param string $job
     *
     * @return void     *
     */
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

    /**
     * Get last run time of a cronjob
     *
     * @param string $name
     *
     * @return int
     */
    private static function getLastRun(string $name): int {
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
