<?php

class PhpInfo extends MainClass
{

    public function render()
    {
        ob_start();
        phpinfo();
        $htmlOutput = ob_get_clean();
        
        // Body-Content rausholen
        $htmlOutput = preg_replace('#^.*<body>(.*)</body>.*$#s', '$1', $htmlOutput);
        $htmlOutput = preg_replace('#>(on|enabled|active)#i', '><span style="color:#090">$1</span>', $htmlOutput);
        $htmlOutput = preg_replace('#>(off|disabled)#i', '><span style="color:#f00">$1</span>', $htmlOutput);
        $htmlOutput = str_replace('<font', '<span', $htmlOutput);
        $htmlOutput = str_replace('</font>', '</span>', $htmlOutput);
        return $htmlOutput;
    }

    public function settings()
    {
        return $this->render();
    }

    public function getSettingsLinkText()
    {
        get_translation("info");
    }

    public function getSettingsHeadline()
    {
        return '<i class="fa fa-info-circle" style="color:#777bb3;" aria-hidden="true"></i> phpinfo';
    }
}