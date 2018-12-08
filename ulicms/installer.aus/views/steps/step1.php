<p><?php
echo TRANSLATION_SELECT_LANGUAGE;
$language = InstallerController::getLanguage();
?></p>
<p>
	<select name="language" id="language" class="form-control">
		<option value="de" <?php if($language == "de") echo "selected";?>>Deutsch</option>

		<option value="en" <?php if($language == "en") echo "selected";?>>English</option>
	</select>
</p>
<p>
	<a href="index.php?step=2" class="btn btn-primary"><?php echo TRANSLATION_NEXT;?></a>
</p>