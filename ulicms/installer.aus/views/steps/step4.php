<form role="form" id="database-login" method="post"
	action="index.php?step=3">
	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_MYSQL_HOST;?>*</label> <input
			type="text" class="form-control" name="mysql_host" id="mysql_host"
			value="<?php echo htmlspecialchars($_SESSION["mysql_host"]);?>"
			required>
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_MYSQL_USER;?>*</label> <input
			type="text" class="form-control" name="mysql_user" id="mysql_user"
			value="<?php echo htmlspecialchars($_SESSION["mysql_user"]);?>"
			required>
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_MYSQL_PASSWORD;?></label> <input
			type="password" class="form-control" name="mysql_password"
			id="mysql_password"
			value="<?php echo htmlspecialchars($_SESSION["mysql_password"]);?>">
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_MYSQL_DATABASE;?>*</label> <input
			type="text" name="mysql_database" class="form-control"
			id="mysql_database"
			value="<?php echo htmlspecialchars($_SESSION["mysql_database"]);?>"
			required>
	</div>


	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_MYSQL_PREFIX;?></label> <input
			type="text" name="mysql_prefix" class="form-control"
			id="mysql_prefix"
			value="<?php echo htmlspecialchars($_SESSION["mysql_prefix"]);?>">
	</div>

	<input type="hidden" name="submit_form" value="TryConnect">
	<button type="submit" class="btn btn-primary"><?php echo TRANSLATION_CONNECT;?></button>
	<div id="loading">
		<img src="../admin/gfx/loading.gif" alt="Loading">
	</div>

	<div id="error-message"></div>
</form>