<?php

declare(strict_types=1);

function get_translation(string $name, array $placeholders = []): string {
    $iname = strtoupper($name);
    foreach (get_defined_constants() as $key => $value) {
        if (startsWith($key, "TRANSLATION_")
                and $key == "TRANSLATION_" . $iname) {
            $custom_translation = Translation::get($key);
            if ($custom_translation !== null) {
                $value = $custom_translation;
            }
            // Platzhalter ersetzen, diese können
            // als assoziatives Array als zweiter Parameter
            // dem Funktionsaufruf mitgegeben werden
            foreach ($placeholders as $placeholder => $replacement) {
                $value = str_ireplace($placeholder, $replacement, $value);
            }

            return $value;
        }
    }
    return $name;
}

function _t(string $name, array $placeholders = []): string {
    return get_translation($name, $placeholders);
}

function t(string $name, array $placeholders = []): void {
    echo _t($name, $placeholders);
}

function singularOrPlural(int $count, string $singular, string $plural) {
    return $count === 1 ?
            str_ireplace("%number%", $count, $singular) :
            str_ireplace("%number%", $count, $plural);
}

function translation(string $name, array $placeholders = []): void {
    echo get_translation($name, $placeholders);
}

function translate(string $name, array $placeholders = []) {
    translation($name, $placeholders);
}

function get_secure_translation(
        string $name,
        array $placeholders = []
): string {
    return Template::getEscape(get_translation($name, $placeholders));
}

function secure_translation(string $name, array $placeholders = []): void {
    echo get_secure_translation($name, $placeholders);
}

function secure_translate(string $name, array $placeholders = []): void {
    secure_translation($name, $placeholders);
}

function add_translation(string $key, string $value): void {
    register_translation($key, $value);
}

function register_translation(string $key, string $value): void {
    $key = strtoupper($key);
    if (!startsWith($key, "TRANSLATION_")) {
        $key = "TRANSLATION_" . $key;
    }
    idefine($key, $value);
}

function getFrontendLanguage(){
    $domainLanguage = get_domain() ? 
            getDomainByLanguage(get_domain()) : null;
    $fallbackLanguage = $domainLanguage ?
            $domainLanguage : Settings::get("language");
    
   return isset($_SESSION["language"]) ? $_SESSION["language"] : $fallbackLanguage;
}