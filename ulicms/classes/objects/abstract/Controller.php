<?php

declare(strict_types=1);

use UliCMS\Exceptions\AccessDeniedException;

// All module controllers must inherit from this class
abstract class Controller
{
    protected $blacklist = array(
        "runCommand"
    );

    public function __construct()
    {
        // add all hooks to blacklist
        // blacklisted methods can not be remote called as action
        $file = Path::resolve("ULICMS_ROOT/lib/ressources/hooks.txt");
        if (file_exists($file)) {
            $lines = StringHelper::linesFromFile($file);
            $lines = array_unique($lines);
            foreach ($lines as $line) {
                $this->blacklist[] = ModuleHelper::underscoreToCamel($line);
            }
        }
    }

    // this method executes controller methods
    // controller name and method can be specified as sClass and sMethod
    // arguments by GET or POST request
    // Example URL: index.php?sClass=MyController&sMethod=helloWorld
    public function runCommand(): void
    {
        $sClass = $_REQUEST["sClass"];
        if (isset($_REQUEST["sMethod"])
                and StringHelper::isNotNullOrEmpty($_REQUEST["sMethod"]) && !faster_in_array(
                    $_REQUEST["sMethod"],
                    $this->blacklist
                )
        ) {
            $sMethod = $_REQUEST["sMethod"];
            $sMethodWithRequestType = $sMethod . ucfirst(Request::getMethod());

            $reflection = null;
            $reflectionWithRequestType = null;

            // get reflection for the method to call
            if (method_exists($this, $sMethod)) {
                $reflection = new ReflectionMethod($this, $sMethod);
            }
            // there can be methods for specific request methods
            // e.g. helloWorldPost(), helloWorldGet()
            // if there is no request method specific controller action
            // helloWorld() is called
            if (method_exists($this, $sMethodWithRequestType)) {
                $reflectionWithRequestType = new ReflectionMethod(
                    $this,
                    $sMethodWithRequestType
                );
            }

            // if there is a method, it is public and the user has the required
            // permissions, call it
            if (method_exists($this, $sMethodWithRequestType) && !startsWith($sMethodWithRequestType, "_")
                    and $reflectionWithRequestType
                    and $reflectionWithRequestType->isPublic()) {
                if (ControllerRegistry::userCanCall(
                    $sClass,
                    $sMethodWithRequestType
                )) {
                    $this->$sMethodWithRequestType();
                } else {
                    throw new AccessDeniedException(
                        get_translation("forbidden")
                    );
                }
            } elseif (method_exists($this, $sMethod) && !startsWith($sMethod, "_")
                    and $reflection and $reflection->isPublic()) {
                if (ControllerRegistry::userCanCall($sClass, $sMethod)) {
                    $this->$sMethod();
                } else {
                    throw new AccessDeniedException(
                        get_translation("forbidden")
                    );
                }
            } else {
                throw new BadMethodCallException(
                    "method " . _esc($sMethod) .
                        " is not callable"
                );
            }
        }
    }
}
