<form method="post" action="<?php echo build_polls_admin_url();?>">
<?php csrf_token_html();?>
	<p>
		<strong><?php translate("question");?></strong> <br /> <input
			type="text" name="title" value="" required>
	</p>
	<p>
		<strong><?php translate("date_from");?></strong><br /> <input
			type="date" name="date_from">
	</p>

	<p>
		<strong><?php translate("date_to");?></strong><br /> <input
			type="date" name="date_to">
	</p>
	<p>
		<strong><?php translate("answers");?></strong>
	</p>
	<p>

  <?php
		
		for($i = 0; $i < Settings::get ( "poll_max_items" ); $i ++) {
			?>
	<?php translate("ANSWER_NUMBER_X", array("%x" => $i +1));?><br /> <input
			type="text" name="answers[]" value=""><br /> <br />
  	<?php
		}
		?>
  </p>
	<input type="submit" value="<?php translate("create");?>" name="create">
</form>