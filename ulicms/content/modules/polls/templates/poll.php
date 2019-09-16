<?php
$data = get_custom_data ();

if (isset ( $data ["poll_id"] ) and is_numeric ( $data ["poll_id"] )) {
	$id = intval ( $data ["poll_id"] );
	$question = new Question ( $id );
	if ($question->getID () === null) {
		translate ( "poll_not_found" );
	} else {
		?>
<p>
	<strong><?php Template::escape($question->title);?></strong>
</p>
<form action="<?php echo buildSEOURL();?>" method="post">
   <?php csrf_token_html();?>
	<input type="hidden" name="poll-id"
		value="<?php echo $question->getID();?>">
		<?php
		$first = true;
		foreach ( $question->getAnswers () as $answer ) {
			?>
		<p>
		<input type="radio" id="answer_<?php echo $answer->getID();?>"
			name="answer" value="<?php echo $answer->getID();?>"
			<?php if($first) echo "checked";?>> <label
			for="answer_<?php echo $answer->getID();?>"><?php Template::escape($answer->title)?></label>
	</p>
		<?php
			
			$first = false;
		}
		?>
		<input type="submit" name="submit-poll"
		value="<?php translate("do_vote");?>">
</form>


<?php
	}
} else {
	translate ( "poll_id_not_set" );
}