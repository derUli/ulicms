<?php

class JSTranslation
{

    private $keys = array();

    private $varName = "Translation";

    public function __construct($keys = array(), $varName = "Translation")
    {
        $this->addKeys($keys);
        $this->setVarName($varName);
    }

    public function addKey($name)
    {
        if (! faster_in_array($name, $this->keys)) {
            $this->keys[] = $name;
        }
    }

    public function addKeys($names)
    {
        foreach ($names as $name) {
            $this->addKey($name);
        }
    }

    public function removeKey($del_var)
    {
        if (($key = array_search($del_val, $this->keys)) !== false) {
            unset($this->keys[$key]);
        }
    }

    public function removeKeys($del_vars)
    {
        foreach ($del_vars as $del_var) {
            $this->removeKey($del_var);
        }
    }

    public function getKeys()
    {
        return $this->keys;
    }

    public function setVarName($val)
    {
        $this->varName = $val;
    }

    public function getVarName()
    {
        return $this->varName;
    }

    public function getJS($wrap = "<script type=\"text/javascript\">{code}</script>")
    {
        $js = array(
            "{$this->varName} = {};"
        );
        foreach ($this->keys as $key) {
            if (startsWith($key, "TRANSLATION_")) {
                $key = substr($key, 12);
            }
            $jsName = ucfirst(ModuleHelper::underscoreToCamel(strtolower($key)));
            $key = strtoupper($key);
            $value = get_translation($key);
            $value = str_replace("\"", "\\\"", $value);
            $line = "{$this->varName}." . $jsName . " = \"" . $value . "\";";
            $js[] = $line;
        }
        $jsString = implode("", $js);
        $output = str_replace("{code}", $jsString, $wrap);
        return $output;
    }

    public function renderJS($wrap = "<script type=\"text/javascript\">{code}</script>")
    {
        echo $this->getJS($wrap);
    }

    public function render($wrap = "<script type=\"text/javascript\">{code}</script>")
    {
        $this->renderJS($wrap);
    }
}
