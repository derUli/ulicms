<?php
use Adldap\Adldap;
class LdapLogin extends Controller {
	public function getSettings() {
		$cfg = new config ();
		if (! isset ( $cfg->ldapConfig )) {
			return null;
		}
		return $cfg->ldapConfig;
	}
	public function sessionDataFilter($input) {
		if (! Request::isPost ()) {
			throw new BadMethodCallException ( "only post request are allowed" );
		}
		$memberName = Request::getVar ( "user" );
		$memberPassword = Request::getVar ( "password" );
		
		$settings = $this->getSettings ();
		if (! $settings) {
			die ( "please add \$ldapConfig array to cms-config.php." );
		}
		
		// LETS START THE LDAP CONECTION
		$provider = new \Adldap\Connections\Provider ( $settings );
		
		try {
			// CONNECTING TO THE SERVER AS ADMIN
			$provider->auth ()->bindAsAdministrator ();
			$search = $provider->search ();
			
			try {
				// AUTHENTICATING THE USER FROM LOGIN PAGE
				if ($provider->auth ()->attempt ( $memberName, $memberPassword )) {
					$rec = $search->users ()->whereEquals ( "cn", $memberName )->first ();
					
					var_dump ( $rec );
					// $guid = $this->_to_p_guid($rec['objectguid'][0]);
					$manager = new UserManager ();
					$users = $manager->getAllUsers ();
					$result = null;
					foreach ( $users as $user ) {
						if ($user->getUsername () == $memberName) {
							$result = $user;
							break;
						}
					}
					// DOES USER ALREADY EXIST ON LOCAL ULICMS USER TABLE
					if (! $result) { // CREATE USER IN TABLE
						
						$user = new User ();
						$user->setUsername ( $memberName );
						$user->setPassword ( $memberPassword );
						
						$email = $rec ['email'] [0];
						
						if (is_null ( $rec ['email'] [0] )) {
							$email = $rec ['userprincipalname'] [0];
						}
						
						$user->setEmail ( $email );
						
						if (is_null ( $rec ['givenName'] [0] )) {
							$fname = $rec ['cn'] [0];
						} else {
							$fname = $rec ['givenName'] [0];
						}
						
						$user->setFirstname ( $fname );
						$lname = $rec ['sn'] [0];
						if (is_null ( $lname )) {
							$lname = $rec ['cn'] [0];
						}
						$user->setLastname ( $lname );
						if (isset ( $settings ["group_id"] ) and is_numeric ( $settings ["group_id"] )) {
							$user->setGroupId ( $settings ["group_id"] );
						}
						$user->save ();
					} else { // USER ALREDY EXIST IN LOCAL DB
						$id = $result->getId ();
						$user = new User ( $id );
						$user->setPassword ( §memberPassword ); // get password from post and submit it to get hashed
						$user->save (); // save user
					}
					return $input; // LOGIN AS CRAFT USER CORRESPONDING TO username
				} else {
					// Credentials were incorrect.
					return null;
				}
			} catch ( \Adldap\Exceptions\Auth\UsernameRequiredException $e ) {
				// The user didn't supply a username.
				die ( "User did not provide a username" );
			} catch ( \Adldap\Exceptions\Auth\PasswordRequiredException $e ) {
				// The user didn't supply a password.
				die ( "User did not provide a password" );
			}
		} catch ( \Adldap\Exceptions\Auth\BindException $e ) {
			die ( "Can't bind to LDAP server!" );
		}
	}
}