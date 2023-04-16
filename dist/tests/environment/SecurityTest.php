<?php
use Nette\Utils\Finder;

class SecurityTest extends \PHPUnit\Framework\TestCase{
    public function testPhpFilesProtected(){
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

            $skip = false;

            foreach($skipDirs as $skipDir){
                if(str_contains($path, $skipDir)){
                    $skip = true;
                }
            }

            if($skip){
                continue;
            }

            if(str_starts_with($path, '.')){
                continue;
            }

            $output = trim((string)shell_exec("php -f \"{$path}\""));

            if($output !== 'No direct script access allowed'){
                $this->fail("$path is not protected");
            }
        }
        $duration = time() - $startTime;
        
        $this->assertEquals(0, $duration);
    }
}