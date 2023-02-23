<?php

const CORE_COMPONENT = 'foo';
require 'init.php';


$controller = new DesignSettingsController();
$families = $controller->getFontFamilys();

echo "<table border=1><thead><tr><th>Auwahl</th><th>Font Stack</th><tr></thead>";
echo "<tbody>";

foreach ($families as $name => $family) {
    echo "<tr>";
    echo "<td>" ._esc($name) . "</td>";
    echo "<td>" ._esc($family) . "</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
