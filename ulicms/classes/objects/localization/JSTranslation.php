<?php

declare(strict_types=1);

// Makes translation strings avaiable for Javascript
class JSTranslation {

    private $keys = [];
    private $varName = "Translation";

    public function __construct(array $keys = [], string $varName = "Translation") {
        $this->addKeys($keys);
        $this->setVarName($varName);
    }

    public function addKey(string $name): void {
        if (!faster_in_array($name, $this->keys)) {
            $this->keys[] = $name;
        }
    }

    public function addKeys(array $names): void {
        foreach ($names as $name) {
            $this->addKey($name);
        }
    }

    public function removeKey(string $del_var): void {
        if (($key = array_search($del_var, $this->keys)) !== false) {
            unset($this->keys[$key]);
        }
    }

    public function removeKeys(array $del_vars): void {
        foreach ($del_vars as $del_var) {
            $this->removeKey($del_var);
        }
    }

    public function getKeys(): array {
        return $this->keys;
    }

    public function setVarName(string $val): void {
        $this->varName = $val;
    }

    public function getVarName(): string {
        return $this->varName;
    }

    public function getJS(string $wrap = "<script type=\"text/javascript\">{code}</script>"): string {
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
            $value = str_replace("\n", '\n', $value);
            $line = "{$this->varName}." . $jsName . " = \"" . $value . "\";";
            $js[] = $line;
        }
        $jsString = implode("", $js);
        $output = str_replace("{code}", $jsString, $wrap);
        return $output;
    }

    public function renderJS(string $wrap = "<script type=\"text/javascript\">{code}</script>"): void {
        echo $this->getJS($wrap);
    }

    public function render(string $wrap = "<script type=\"text/javascript\">{code}</script>"): void {
        $this->renderJS($wrap);
    }

}
