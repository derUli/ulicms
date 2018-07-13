<?php $license = htmlspecialchars(file_get_contents("license.txt"));?>
<p>You have to read and accept the license conditions to use this software:</p>
<p>
	<textarea id="license" rows="10" cols="80" readonly><?php echo $license;?></textarea>
</p>

<p><a href="?step=3" class="btn btn-default"><?php echo TRANSLATION_ACCEPT_LICNSE;?></a></p>