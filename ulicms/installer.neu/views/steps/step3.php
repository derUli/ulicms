<form role="form" id="database-login" method="post"
	action="index.php?step=3">
	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_MYSQL_HOST;?></label> <input
			type="text" class="form-control" name="mysql_host" id="mysql_host"
			value="<?php echo htmlspecialchars($_SESSION["mysql_host"]);?>">
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_MYSQL_USER;?></label> <input
			type="text" class="form-control" name="mysql_user" id="mysql_user"
			value="<?php echo htmlspecialchars($_SESSION["mysql_user"]);?>">
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_MYSQL_PASSWORD;?></label> <input
			type="password" class="form-control" name="mysql_password"
			id="mysql_password"
			value="<?php echo htmlspecialchars($_SESSION["mysql_password"]);?>">
	</div>

	<div class="form-group">
		<label for="text"><?php echo TRANSLATION_MYSQL_DATABASE;?></label> <input
			type="text" name="mysql_database" class="form-control"
			id="mysql_database"
			value="<?php echo htmlspecialchars($_SESSION["mysql_database"]);?>">
	</div>

	<button type="submit" class="btn btn-default"><?php echo TRANSLATION_CONNECT;?></button>
	<div id="loading">
		<img src="../admin/gfx/loading.gif" alt="Loading">
	</div>

	<div id="error-message"></div>
</form>