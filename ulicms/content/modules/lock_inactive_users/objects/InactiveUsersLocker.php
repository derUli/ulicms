<?php

class InactiveUsersLocker
{

    private $days = 30;

    public function __construct($days)
    {
        $this->days = intval($days);
    }

    // lock all users which have logged in since $days days.
    // returns the count of affected users;
    public function lockInactiveUsers()
    {
        $daysInSeconds = $this->days * 60 * 60 * 24;
        
        $userManager = new UserManager();
        
        $locked = 0;
        
        // get all not locked users
        $users = $userManager->getLockedUsers(false);
        foreach ($users as $user) {
            // lock all users where the time difference between now and the last login
            // is greater than X days
            if ($user->getLastLogin() and time() - $user->getLastLogin() >= $daysInSeconds) {
                $user->setLocked(true);
                $user->save();
                $locked ++;
            }
        }
        return $locked;
    }

    public function getDays()
    {
        return $this->days;
    }
}