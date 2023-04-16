<?php

use Nette\Utils\Finder;

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
}
