<?php

namespace App\Core;

use Exception;

class Session {

	protected $name;
	protected $limit = 0;
	protected $path = '/';
	protected $domain = null; 
	protected $secure = null;

	public function __construct($name, $limit = 0, $path = '/', $domain = null, $secure = null) {
		$this->name = $name;
		$this->limit = $limit;
		$this->path = $path;
		$this->domain = $domain;
		$this->secure = $secure;

		return $this;
	}

	protected function validate() {
		if( isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES']) )
			return false;

		if(isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time())
			return false;

		return true;
	}

	public function regenerate() {
		// If this session is obsolete it means there already is a new id
		if(isset($_SESSION['OBSOLETE']) && $_SESSION['OBSOLETE'] == true)
			return;

		// Set current session to expire in 10 seconds
		$_SESSION['OBSOLETE'] = true;
		$_SESSION['EXPIRES'] = time() + 10;

		// Create new session without destroying the old one
		session_regenerate_id(false);

		// Grab current session ID and close both sessions to allow other scripts to use them
		$newSession = session_id();
		session_write_close();

		// Set session ID to the new one, and start it back up again
		session_id($newSession);
		session_start();

		// Now we unset the obsolete and expiration values for the session we want to keep
		unset($_SESSION['OBSOLETE']);
		unset($_SESSION['EXPIRES']);
	}

	protected function preventHijacking() {
		if(!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent']))
			return false;

		if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR'])
			return false;

		if( $_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT'])
			return false;

		return true;
	}

	public function start() {

		$name = $this->name;
		$limit = $this->limit;
		$path = $this->path;
		$domain = $this->domain;
		$secure = $this->secure;

		// Set the cookie name
		// session_name($name . '_Session');

		// Set SSL level
		$https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);

		// Set session cookie options
		session_set_cookie_params($limit, $path, $domain, $https, true);
		session_start();

		// Make sure the session hasn't expired, and destroy it if it has
		if($this->validate()) {
			// Check to see if the session is new or a hijacking attempt
			if(!$this->preventHijacking()) {
				// Reset session data and regenerate id
				$_SESSION = array();
				$_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
				$this->regenerate();

			// Give a 5% chance of the session id changing on any request
			} elseif(rand(1, 100) <= 5) {
				$this->regenerate();
			}
		} else {
			$_SESSION = array();
			session_destroy();
			session_start();
		}

		return $this;
	}

	public function add($name, $value) {
		if (!isset($_SESSION[$name]))
			$_SESSION[$name] = $value;
		else throw new Exception(sprintf("The Session %s already exists", ucfirst($name)));

		return $this;
	}

	public function remove($name) {
		if (isset($_SESSION[$name]))
			unset($_SESSION[$name]);
		return $this;
	}

	public function overwrite($name, $value) {
		$_SESSION[$name] = $value;
		return $this;
	}

	public function has($name) {
		return isset($_SESSION[$name]);
	}

	public function get($name) {
		if (isset($_SESSION[$name]))
			return $_SESSION[$name];
		else return null;
	}

	public function getOnce($name) {
		if (isset($_SESSION[$name])) {
			$n = $_SESSION[$name];
			unset($_SESSION[$name]);
			return $n;
		}
		else return null;
	}

	public function getAll() {
		return $_SESSION;
	}

	public function destroy() {
		$_SESSION = array();
		session_destroy();
		unset($this);
	}
}
