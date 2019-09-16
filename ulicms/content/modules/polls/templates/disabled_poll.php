<?php
$data = get_custom_data ();

if (isset ( $data ["poll_id"] ) and is_numeric ( $data ["poll_id"] )) {
	$id = intval ( $data ["poll_id"] );
	$question = new Question ( $id );
	if ($question->getID () === null) {
		translate ( "poll_not_found" );
	} else {
		$votes_total = PollFactory::getVotesSum ( $id );
		?>
<p>
	<strong><?php Template::escape($question->title);?></strong>
</p>
<p><?php translate("THIS_POLL_IS_DISABLED");?></p>
<div class="poll-bar-graph">
<?php
		
		foreach ( $question->getAnswers () as $answer ) {
			$votes = 0.00;
			if ($votes_total > 0 and $answer->getVotes () > 0) {
				$votes = $answer->getVotes () * 100 / $votes_total * 3;
			}
			$votes = str_replace ( ",", ".", $votes );
			$color = RandomColor::get ();
			?>
			<strong>
			<?php Template::escape($answer->title);?>
			</strong> <br />
			
<?php if($votes > 0){?>
<div style="width: <?php echo intval($votes);?>px; background-color:<?php echo $color;?>; float:left;margin-right:5px;">&nbsp;</div>
<?php
			}
			?><div style="">
<?php echo $answer->getVotes();?></div>


			<?php
		}
		?>
		</div>
<?php
	}
} else {
	translate ( "poll_id_not_set" );
}