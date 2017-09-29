<?php
class PositionHelper extends Helper {
	public static function getNextFreePosition($user = null) {
		$position = 1;
		if (! $user) {
			$user = get_user_id ();
		}
		$items = TodoListItem::getAllbyUser ( $user );
		if (count ( $items ) > 0) {
			$last = $items [count ( $items ) - 1];
			$position = $last->getPosition () + 1;
		}
		return $position;
	}
}