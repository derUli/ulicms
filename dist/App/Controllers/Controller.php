<?php

declare(strict_types=1);

namespace App\Controllers;

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Exceptions\AccessDeniedException;
use App\Helpers\StringHelper;
use BadMethodCallException;
use ControllerRegistry;
use ModuleHelper;
use Path;
use ReflectionMethod;
use Request;

/**
 * All module controllers must inherited from this class
 */
abstract class Controller
{
    /**
     * List of not callable public methods
     * @var string[]
     */
    protected array $blacklist = [
        'runCommand'
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        // add all hooks to blacklist
        // blacklisted methods can not be remote called as action
        $file = Path::resolve('ULICMS_ROOT/lib/ressources/hooks.txt');
        if (is_file($file)) {
            $lines = StringHelper::linesFromFile($file);
            $lines = array_unique($lines ?? []);
            foreach ($lines as $line) {
                $this->blacklist[] = ModuleHelper::underscoreToCamel($line);
            }
        }
    }

    /**
     * This method executes controller methods
     * Controller name and method can be specified as sClass and sMethod
     * Arguments by GET or POST request
     * Example URL: index.php?sClass=MyController&sMethod=helloWorld
     * @throws AccessDeniedException
     * @throws BadMethodCallException
     * @return void
     */
    public function runCommand(): void
    {
        $sClass = $_REQUEST['sClass'];
        if (isset($_REQUEST['sMethod']) && ! empty($_REQUEST['sMethod']) && ! in_array(
            $_REQUEST['sMethod'],
            $this->blacklist
        )
        ) {
            $sMethod = $_REQUEST['sMethod'];
            $sMethodWithRequestType = $sMethod . ucfirst(Request::getMethod() ?? '');

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
            if (method_exists($this, $sMethodWithRequestType) && ! str_starts_with($sMethodWithRequestType, '_') && $reflectionWithRequestType && $reflectionWithRequestType->isPublic()) {
                if (ControllerRegistry::userCanCall(
                    $sClass,
                    $sMethodWithRequestType
                )) {
                    $this->{$sMethodWithRequestType}();
                } else {
                    throw new AccessDeniedException(
                        get_translation('forbidden')
                    );
                }
            } elseif (method_exists($this, $sMethod) && ! str_starts_with($sMethod, '_')
                    && $reflection && $reflection->isPublic()) {
                if (ControllerRegistry::userCanCall($sClass, $sMethod)) {
                    $this->{$sMethod}();
                } else {
                    throw new AccessDeniedException(
                        get_translation('forbidden')
                    );
                }
            } else {
                throw new BadMethodCallException(
                    'method ' . _esc($sMethod) .
                    ' is not callable'
                );
            }
        }
    }
}
