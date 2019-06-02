<?php

use UliCMS\Exceptions\AccessDeniedException;

abstract class Controller {

    protected $blacklist = array(
        "runCommand"
    );

    public function __construct() {
        $file = Path::resolve("ULICMS_ROOT/lib/hooks.txt");
        if (is_file($file)) {
            $lines = StringHelper::linesFromFile($file);
            $lines = array_unique($lines);
            foreach ($lines as $line) {
                $blacklist[] = ModuleHelper::underscoreToCamel($line);
            }
        }
    }

    public function runCommand() {
        $sClass = Request::getVar("sClass");
        if (isset($_REQUEST["sMethod"]) and StringHelper::isNotNullOrEmpty($_REQUEST["sMethod"]) and ! faster_in_array($_REQUEST["sMethod"], $this->blacklist)) {
            $sMethod = $_REQUEST["sMethod"];
            $sMethodWithRequestType = $sMethod . ucfirst(Request::getMethod());

            $reflection = null;
            $reflectionWithRequestType = null;

            if (method_exists($this, $sMethod)) {
                $reflection = new ReflectionMethod($this, $sMethod);
            }
            if (method_exists($this, $sMethodWithRequestType)) {
                $reflectionWithRequestType = new ReflectionMethod($this, $sMethodWithRequestType);
            }

            if (method_exists($this, $sMethodWithRequestType) and ! startsWith($sMethodWithRequestType, "_") and $reflectionWithRequestType and $reflectionWithRequestType->isPublic()) {
                if (ControllerRegistry::userCanCall($sClass, $sMethodWithRequestType)) {
                    $this->$sMethodWithRequestType();
                } else {
                    throw new AccessDeniedException(get_translation("forbidden"));
                }
            } else if (method_exists($this, $sMethod) and ! startsWith($sMethod, "_") and $reflection and $reflection->isPublic()) {
                if (ControllerRegistry::userCanCall($sClass, $sMethod)) {
                    $this->$sMethod();
                } else {
                    throw new AccessDeniedException(get_translation("forbidden"));
                }
            } else {
                throw new BadMethodCallException("method " . _esc($sMethod) . " is not callable");
            }
        }
    }

}
