<?php

use Nette\Utils\Finder;
use GisoStallenberg\FilePermissionCalculator\FilePermissionCalculator;

class SecurityTest extends \PHPUnit\Framework\TestCase
{
    public function testPhpFilesProtected() {
        $startTime = time();

        $skipDirs = [
            'vendor',
            'tests',
            'fm',
            'index.php',
            'CMSConfigSample.php',
            'CMSConfig.php',
            'phpunit_init.php',
            'init.php',
            'update.php'
        ];

        $protectedFiles = 0;
        $unprotectedFiles = 0;
        foreach (Finder::findFiles(['*.php'])->from('.') as $name => $file) {
            $path = $file->getRealPath();
            $filename = basename($path);

            $containsMessage = false;

            $skip = false;

            foreach($skipDirs as $skipDir){
                if(str_contains($path, $skipDir)){
                    $skip = true;
                }
            }

            if($skip){
                continue;
            }

            $handle = fopen($path, 'r');

            $expected = 'No direct script access allowed';

            while (($actual = fgets($handle)) !== false) {

                if(str_contains($actual, 'No direct script access allowed')){
                    $containsMessage = true;
                    break;
                }
            }

            fclose($handle);

            $this->assertTrue($containsMessage, "{$path} is not protected");
        }
    }

    public function testNewFilePermissions(){
        $testFile = Path::resolve("ULICMS_TMP", uniqid() . ".tmp");

        file_put_contents($testFile, 'Hello World');

        $this->assertTrue(is_readable($testFile));
        $this->assertTrue(is_writable($testFile));

    }

    public function testNewDirectoryPermissions(){
        $testDir = Path::resolve("ULICMS_TMP", uniqid() . ".tmp");
        mkdir($testDir);

        $testFile = $testDir . '/' . uniqid() . ".tmp";
        file_put_contents($testFile, 'Hello World');

        $this->assertTrue(is_readable($testDir));
        $this->assertTrue(is_readable($testFile));
    }
}
