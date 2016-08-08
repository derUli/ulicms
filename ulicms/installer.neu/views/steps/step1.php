<p><?php
echo TRANSLATION_SELECT_LANGUAGE;
$language = InstallerController::getLanguage ();
?></p>
<select name="language" id="language">
	<option value="en" <?php if($language == "en") echo "selected";?>>English</option>
</select>