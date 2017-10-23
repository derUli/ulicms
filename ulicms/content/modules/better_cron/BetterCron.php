<?php

class BetterCron extends Controller
{

    public static function seconds($job, $seconds, $callback)
    {
        $currentTime = time();
        $last_run = self::getLastRun($job);
        if ($currentTime - $last_run >= $seconds) {
            if (is_string($callback)) {
                if (str_contains("::", $callback)) {
                    $callback = explode("::", $callback);
                    $sClass = $callback[0];
                    $sMethod = $callback[1];
                    if (ControllerRegistry::get($sClass) && method_exists(ControllerRegistry::get($sClass), $sMethod)) {
                        ControllerRegistry::get($sClass)->$sMethod();
                    }
                } else {
                    call_user_func($callback);
                }
            } else if (is_callable($callback)) {
                $callback();
            }
            self::updateLastRun($job);
        }
    }

    public static function minutes($job, $minutes, $callback)
    {
        self::seconds($job, $minutes * 60, $callback);
    }

    public static function hours($job, $hours, $callback)
    {
        self::seconds($job, $hours * 60 * 60, $callback);
    }

    public static function days($job, $days, $callback)
    {
        self::seconds($job, $days * 60 * 60 * 24, $callback);
    }

    public static function weeks($job, $weeks, $callback)
    {
        self::seconds($job, $weeks * 60 * 60 * 24 * 7, $callback);
    }

    public static function months($job, $months, $callback)
    {
        self::seconds($job, $months * 60 * 60 * 24 * 7 * 30, $callback);
    }

    public static function years($job, $years, $callback)
    {
        self::seconds($job, $years * 60 * 60 * 24 * 7 * 30 * 365, $callback);
    }

    private static function getLastRun($name)
    {
        $last_run = 0;
        
        $query = Database::pQuery("select last_run from `{prefix}cronjobs` where name = ?", array(
            $name
        ), true);
        if (Database::any($query)) {
            $result = Database::fetchObject($query);
            $last_run = $result->last_run;
        }
        return $last_run;
    }

    private static function updateLastRun($name)
    {
        $query = Database::pQuery("select name from `{prefix}cronjobs` where name = ?", array(
            $name
        ), true);
        if (Database::any($query)) {
            Database::pQuery("update `{prefix}cronjobs` set last_run = ? where name = ?", array(
                time(),
                $name
            ), true);
        } else {
            Database::pQuery("insert into `{prefix}cronjobs` (name, last_run)
values(?, ?)", array(
                $name,
                time()
            ), true);
        }
    }

    public function uninstall()
    {
        $migrator = new DBMigrator("package/better_cron", ModuleHelper::buildRessourcePath("better_cron", "sql/down"));
        $migrator->rollback();
    }
}
