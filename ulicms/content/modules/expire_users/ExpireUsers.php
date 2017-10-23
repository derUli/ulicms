<?php
class ExpireUsers extends controller {
	public function cron() {
		BetterCron::hours ( "module/expire_users/lock_expired_users", 12, function () {
			// TODO: Iterate over all users and lock expired users.
		} );
	}
}