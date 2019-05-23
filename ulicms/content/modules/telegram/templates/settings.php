<?php

echo UliCMS\HTML\Alert::warning("Coming soon!");

if (Request::getVar("save")) {
    echo UliCMS\HTML\Alert::success(get_translation("changes_was_saved"));
}

if (Request::getVar("error")) {
    echo UliCMS\HTML\Alert::danger(get_translation(
                    Request::getVar("error")));
}
?>

