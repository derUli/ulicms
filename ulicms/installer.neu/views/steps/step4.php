<form role="form" id="admin-login" method="post"
	action="index.php?step=5">
	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_ADMIN_USER;?></label> <input
			type="text" class="form-control" name="admin_user" id="admin_user"
			value="<?php echo htmlspecialchars($_SESSION["admin_user"]);?>"
			required>
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_ADMIN_PASSWORD;?></label> <input
			type="password" class="form-control" name="admin_password"
			id="admin_password"
			value="<?php echo htmlspecialchars($_SESSION["admin_password"]);?>"
			required>
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_ADMIN_PASSWORD_REPEAT;?></label>
		<input type="password" class="form-control"
			name="admin_password_repeat" id="admin_password_repeat" value=""
			required>
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_ADMIN_EMAIL;?></label> <input
			type="email" class="form-control" name="admin_email" id="admin_email"
			value="<?php echo htmlspecialchars($_SESSION["admin_email"]);?>"
			required>
	</div>


	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_ADMIN_LASTNAME;?></label> <input
			type="text" class="form-control" name="admin_lastname"
			id="admin_lastname"
			value="<?php echo htmlspecialchars($_SESSION["admin_lastname"]);?>"
			required>
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_ADMIN_FIRSTNAME;?></label> <input
			type="text" class="form-control" name="admin_firstname"
			id="admin_firstname"
			value="<?php echo htmlspecialchars($_SESSION["admin_firstname"]);?>"
			required>
	</div>

	<button type="submit" class="btn btn-default"><?php echo TRANSLATION_APPLY;?></button>
	<div id="loading">
		<img src="../admin/gfx/loading.gif" alt="Loading">
	</div>

</form>