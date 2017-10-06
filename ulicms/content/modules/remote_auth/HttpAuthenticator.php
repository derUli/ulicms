<?php
class HttpAuthenticator extends Controller {
	public function getConfig() {
		$cfg = new config ();
		if (isset ( $cfg->remote_auth_config ) and is_array ( $cfg->remote_auth_config )) {
			return $cfg->remote_auth_config;
		}
		return null;
	}
	public function getEnvVars() {
		$env_vars = array (
				"REMOTE_USER",
				"REDIRECT_REMOTE_USER" 
		);
		$cfg = $this->getConfig ();
		if (isset ( $cfg ["env_vars"] )) {
			if (is_array ( $cfg ["env_vars"] )) {
				$env_vars = $cfg ["env_vars"];
			} else if (is_string ( $cfg ["env_vars"] )) {
				$env_vars = array (
						$cfg ["env_vars"] 
				);
			}
		}
		return $env_vars;
	}
	public function getRemoteUser() {
		$vars = $this->getEnvVars ();
		foreach ( $vars as $var ) {
			if (isset ( $_SERVER [$var] ) and StringHelper::isNotNullOrWhitespace ( $_SERVER [$var] )) {
				return $_SERVER ["var"];
				// return new Authorization ( $_SERVER [$var] );
			}
		}
		return null;
	}
	public function auth() {
		$user = $this->getRemoteUser ();
		$user = getUserByName ( $user );
		$cfg = $this->getConfig ();
		if (! $user and isset ( $cfg ["create_user"] ) and $cfg ["create_user"]) {
			// TODO: User anlegen
		}
		return $user;
	}
}