<?php
$id = intval ( $_GET ["edit"] );
$question = new Question ( $id );
if ($question->getID () !== null) {
	$answers = $question->getAnswers ();
	$date_from = "";
	$date_to = "";
	if ($question->date_from !== null) {
		$date_from = $question->date_from;
	}
	
	$date_to = "";
	if ($question->date_to !== null) {
		$date_to = $question->date_to;
	}
	?>
<form method="post" action="<?php echo build_polls_admin_url();?>">
	<input type="hidden" name="id" value="<?php echo $question->getID();?>">
<?php csrf_token_html();?>
<p>
		<strong><?php translate("question");?></strong> <br /> <input
			type="text" name="title"
			value="<?php Template::escape($question->title);?>" required>
	</p>
	<p>
		<strong><?php translate("date_from");?></strong><br /> <input
			type="date" name="date_from"
			value="<?php Template::escape($date_from)?>">
	</p>

	<p>
		<strong><?php translate("date_to");?></strong><br /> <input
			type="date" name="date_to" value="<?php Template::escape($date_to)?>">
	</p>
	<p>
		<strong><?php translate("answers");?></strong>
	</p>
	<p>

  <?php
	$i = 0;
	foreach ( $answers as $answer ) {
		
		?>
	<?php translate("ANSWER_NUMBER_X", array("%x" => $i +1));?><br /> <input
			type="text" name="answer_<?php echo $answer->getID();?>"
			value="<?php Template::escape($answer->title);?>"><br /> <br />
  	<?php
		$i ++;
	}
	
	for($i = $i; $i < Settings::get ( "poll_max_items" ); $i ++) {
		?>
	<?php translate("ANSWER_NUMBER_X", array("%x" => $i +1));?><br /> <input
			type="text" name="new_answers[]" value=""><br /> <br />
  	<?php
	}
	?>
  </p>
	<input type="submit" value="<?php translate("save_changes");?>"
		name="update">
</form>

<?php
}
?>