<?php
class PhpMerger
{
    private static $required = [];
    private static function add_to_queue($path)
    {
        self::$required[] = $path;
    }
    private static function removeFromQueue($path)
    {
        $pathsToRemove = [
            $path
        ];
        self::$required = array_diff(self::$required, $pathsToRemove);
    }
    public static function generate($conf)
    {
        self::$required = [];
        foreach ($conf['jobs'] as $def) {
            // untrail slash
            $def['root'] = preg_replace('/\/$/', '', $def['root']);

            // load base classes, which are required for other classes
            foreach ($def['files'] as $file) {
                self::add_to_queue($def['root'] . '/' . $file);
            }

            // load dirs in defined order. No recursion.
            foreach ($def['dirs'] as $dir) {
                $dirAbs = __DIR__  . '/' . $def['root'] . '/' . $dir;
                $files = glob($dirAbs . '/*.php');
                foreach ($files as $file) {
                    // only require files that begins with uppercase (A-Z)
                    if (preg_match('/\/[A-Z][a-zA-Z]*\.php/', $file)) {
                        $file = str_replace(__DIR__ . '/' . $def['root'], '', $file);
                        $file = str_replace('./', '', $file);

                        //echo $file . "\n";
                        self::add_to_queue($def['root'] . $file);
                    }
                }
            }

            // remove exclude files
            if (isset($def['exclude'])) {
                foreach ($def['exclude'] as $excludeFile) {
                    self::removeFromQueue($def['root'] . $excludeFile);
                }
            }
        }

        // remove duplicates
        self::$required = array_unique(self::$required);


        echo "included: \n" . implode("\n", self::$required) . "\n";
//exit;
        // generate file content
        $data = '';
        $data .= "<?php \n";
        foreach (self::$required as $path) {
            $file = file_get_contents(__DIR__ . '/' . $path);
            //$file = str_replace('<' . '?php', '', $file);
            //$file = str_replace('<' . '?php', '?' . '><?' . 'php', $file);
            // prepend closing php tag before php tag (only if php tag is in beginning of file)
            $file = preg_replace('/^\<\?php/', '?><?' . 'php', $file);
            $data .= $file . "\n";

        }

        // generate file
        //$my_file = '../generated.inc';
        $handle = fopen(__DIR__ . '/' . $conf['destination'], 'w') or die('Cannot open file:  ' . $conf['destination']);
        fwrite($handle, $data);
        echo "saved to '" . $conf['destination'] . "'\n";
    }
}
