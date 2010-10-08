<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Session library.
 *
 * $Id: Session.php 4679 2009-11-10 01:45:52Z isaiah $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Session_Core {

	// Session singleton
	protected static $instance;

	// Protected key names (cannot be set by the user)
	protected static $protect = array('session_id', 'user_agent', 'last_activity', 'ip_address', 'total_hits', '_kf_flash_');

	// Configuration and driver
	protected static $config;
	protected static $driver;

	// Flash variables
	protected static $flash;

	// Input library
	protected $input;

	// Automatically save the session by default
	public static $should_save = true;

	/**
	 * Singleton instance of Session.
	 *
	 * @param string Force a specific session_id
	 */
	public static function instance($session_id = NULL)
	{
		if (Session::$instance == NULL)
		{
			// Create a new instance
			new Session($session_id);
		}
		elseif( ! is_null($session_id) AND $session_id != session_id() )
		{
			throw new Kohana_Exception('A session (SID: :session:) is already open, cannot open the specified session (SID: :new_session:).', array(':session:' => session_id(), ':new_session:' => $session_id));
		}

		return Session::$instance;
	}

	/**
	 * Be sure to block the use of __clone.
	 */
	private function __clone(){}

	/**
	 * On first session instance creation, sets up the driver and creates session.
	 *
	 * @param string Force a specific session_id
	 */
	protected function __construct($session_id = NULL)
	{
		$this->input = Input::instance();

		// This part only needs to be run once
		if (Session::$instance === NULL)
		{
			// Load config
			Session::$config = Kohana::config('session');

			// Makes a mirrored array, eg: foo=foo
			Session::$protect = array_combine(Session::$protect, Session::$protect);

			// Configure garbage collection
			ini_set('session.gc_probability', (int) Session::$config['gc_probability']);
			ini_set('session.gc_divisor', 100);
			ini_set('session.gc_maxlifetime', (Session::$config['expiration'] == 0) ? 86400 : Session::$config['expiration']);

			// Create a new session
			$this->create(NULL, $session_id);

			if (Session::$config['regenerate'] > 0 AND ($_SESSION['total_hits'] % Session::$config['regenerate']) === 0)
			{
				// Regenerate session id and update session cookie
				$this->regenerate();
			}
			else
			{
				// Always update session cookie to keep the session alive
				cookie::set(Session::$config['name'], $_SESSION['session_id'], Session::$config['expiration']);
			}

			// Close the session on system shutdown (run before sending the headers), so that
			// the session cookie(s) can be written.
			Event::add('system.shutdown', array($this, 'write_close'));

			// Singleton instance
			Session::$instance = $this;
		}

		Kohana_Log::add('debug', 'Session Library initialized');
	}

	/**
	 * Get the session id.
	 *
	 * @return  string
	 */
	public function id()
	{
		return $_SESSION['session_id'];
	}

	/**
	 * Create a new session.
	 *
	 * @param   array  variables to set after creation
	 * @param   string Force a specific session_id
	 * @return  void
	 */
	public function create($vars = NULL, $session_id = NULL)
	{
		// Destroy any current sessions
		$this->destroy();

		if (Session::$config['driver'] !== 'native')
		{
			// Set driver name
			$driver = 'Session_'.ucfirst(Session::$config['driver']).'_Driver';

			// Load the driver
			if ( ! Kohana::auto_load($driver))
				throw new Kohana_Exception('The :driver: driver for the :library: library could not be found',
										   array(':driver:' => Session::$config['driver'], ':library:' => get_class($this)));

			// Initialize the driver
			Session::$driver = new $driver();

			// Validate the driver
			if ( ! (Session::$driver instanceof Session_Driver))
				throw new Kohana_Exception('The :driver: driver for the :library: library must implement the :interface: interface',
										   array(':driver:' => Session::$config['driver'], ':library:' => get_class($this), ':interface:' => 'Session_Driver'));

			// Register non-native driver as the session handler
			session_set_save_handler
			(
				array(Session::$driver, 'open'),
				array(Session::$driver, 'close'),
				array(Session::$driver, 'read'),
				array(Session::$driver, 'write'),
				array(Session::$driver, 'destroy'),
				array(Session::$driver, 'gc')
			);
		}

		// Validate the session name
		if ( ! preg_match('~^(?=.*[a-z])[a-z0-9_]++$~iD', Session::$config['name']))
			throw new Kohana_Exception('The session_name, :session:, is invalid. It must contain only alphanumeric characters and underscores. Also at least one letter must be present.', array(':session:' => Session::$config['name']));

		// Name the session, this will also be the name of the cookie
		session_name(Session::$config['name']);

		// Set the session cookie parameters
		session_set_cookie_params
		(
			Session::$config['expiration'],
			Kohana::config('cookie.path'),
			Kohana::config('cookie.domain'),
			Kohana::config('cookie.secure'),
			Kohana::config('cookie.httponly')
		);

		$cookie = cookie::get(Session::$config['name']);
		
		if ($session_id === NULL)
		{
			// Reopen session from signed cookie value.
			$session_id = $cookie;
		}

		// Reopen an existing session if supplied
		if ( ! is_null($session_id))
		{
			session_id($session_id);
		}

		// Start the session!
		session_start();

		// Put session_id in the session variable
		$_SESSION['session_id'] = session_id();

		// Set defaults
		if ( ! isset($_SESSION['_kf_flash_']))
		{
			$_SESSION['total_hits'] = 0;
			$_SESSION['_kf_flash_'] = array();

			$_SESSION['user_agent'] = request::user_agent();
			$_SESSION['ip_address'] = $this->input->ip_address();
		}

		// Set up flash variables
		Session::$flash =& $_SESSION['_kf_flash_'];

		// Increase total hits
		$_SESSION['total_hits'] += 1;

		// Validate data only on hits after one
		if ($_SESSION['total_hits'] > 1)
		{
			// Validate the session
			foreach (Session::$config['validate'] as $valid)
			{
				switch ($valid)
				{
					// Check user agent for consistency
					case 'user_agent':
						if ($_SESSION[$valid] !== request::user_agent())
							return $this->create();
					break;

					// Check ip address for consistency
					case 'ip_address':
						if ($_SESSION[$valid] !== $this->input->$valid())
							return $this->create();
					break;

					// Check expiration time to prevent users from manually modifying it
					case 'expiration':
						if (time() - $_SESSION['last_activity'] > ini_get('session.gc_maxlifetime'))
							return $this->create();
					break;
				}
			}
		}

		// Expire flash keys
		$this->expire_flash();

		// Update last activity
		$_SESSION['last_activity'] = time();

		// Set the new data
		Session::set($vars);
	}

	/**
	 * Regenerates the global session id.
	 *
	 * @return  void
	 */
	public function regenerate()
	{
		if (Session::$config['driver'] === 'native')
		{
			// Generate a new session id
			// Note: also sets a new session cookie with the updated id
			session_regenerate_id(TRUE);

			// Update session with new id
			$_SESSION['session_id'] = session_id();
		}
		else
		{
			// Pass the regenerating off to the driver in case it wants to do anything special
			$_SESSION['session_id'] = Session::$driver->regenerate();
		}

		// Get the session name
		$name = session_name();

		if (isset($_COOKIE[$name]))
		{
			// Change the cookie value to match the new session id to prevent "lag"
			cookie::set($name, $_SESSION['session_id']);
		}
	}

	/**
	 * Destroys the current session.
	 *
	 * @return  void
	 */
	public function destroy()
	{
		if (session_id() !== '')
		{
			// Get the session name
			$name = session_name();

			// Destroy the session
			session_destroy();

			// Re-initialize the array
			$_SESSION = array();

			// Delete the session cookie
			cookie::delete($name);
		}
	}

	/**
	 * Runs the system.session_write event, then calls session_write_close.
	 *
	 * @return  void
	 */
	public function write_close()
	{
		static $run;

		if ($run === NULL)
		{
			$run = TRUE;

			// Run the events that depend on the session being open
			Event::run('system.session_write');

			// Expire flash keys
			$this->expire_flash();

			// Close the session
			session_write_close();
		}
	}

	/**
	 * Set a session variable.
	 *
	 * @param   string|array  key, or array of values
	 * @param   mixed         value (if keys is not an array)
	 * @return  void
	 */
	public function set($keys, $val = FALSE)
	{
		if (empty($keys))
			return FALSE;

		if ( ! is_array($keys))
		{
			$keys = array($keys => $val);
		}

		foreach ($keys as $key => $val)
		{
			if (isset(Session::$protect[$key]))
				continue;

			// Set the key
			$_SESSION[$key] = $val;
		}
	}

	/**
	 * Set a flash variable.
	 *
	 * @param   string|array  key, or array of values
	 * @param   mixed         value (if keys is not an array)
	 * @return  void
	 */
	public function set_flash($keys, $val = FALSE)
	{
		if (empty($keys))
			return FALSE;

		if ( ! is_array($keys))
		{
			$keys = array($keys => $val);
		}

		foreach ($keys as $key => $val)
		{
			if ($key == FALSE)
				continue;

			Session::$flash[$key] = 'new';
			Session::set($key, $val);
		}
	}

	/**
	 * Freshen one, multiple or all flash variables.
	 *
	 * @param   string  variable key(s)
	 * @return  void
	 */
	public function keep_flash($keys = NULL)
	{
		$keys = ($keys === NULL) ? array_keys(Session::$flash) : func_get_args();

		foreach ($keys as $key)
		{
			if (isset(Session::$flash[$key]))
			{
				Session::$flash[$key] = 'new';
			}
		}
	}

	/**
	 * Expires old flash data and removes it from the session.
	 *
	 * @return  void
	 */
	public function expire_flash()
	{
		static $run;

		// Method can only be run once
		if ($run === TRUE)
			return;

		if ( ! empty(Session::$flash))
		{
			foreach (Session::$flash as $key => $state)
			{
				if ($state === 'old')
				{
					// Flash has expired
					unset(Session::$flash[$key], $_SESSION[$key]);
				}
				else
				{
					// Flash will expire
					Session::$flash[$key] = 'old';
				}
			}
		}

		// Method has been run
		$run = TRUE;
	}

	/**
	 * Get a variable. Access to sub-arrays is supported with key.subkey.
	 *
	 * @param   string  variable key
	 * @param   mixed   default value returned if variable does not exist
	 * @return  mixed   Variable data if key specified, otherwise array containing all session data.
	 */
	public function get($key = FALSE, $default = FALSE)
	{
		if (empty($key))
			return $_SESSION;

		$result = isset($_SESSION[$key]) ? $_SESSION[$key] : Kohana::key_string($_SESSION, $key);

		return ($result === NULL) ? $default : $result;
	}

	/**
	 * Get a variable, and delete it.
	 *
	 * @param   string  variable key
	 * @param   mixed   default value returned if variable does not exist
	 * @return  mixed
	 */
	public function get_once($key, $default = FALSE)
	{
		$return = Session::get($key, $default);
		Session::delete($key);

		return $return;
	}

	/**
	 * Delete one or more variables.
	 *
	 * @param   string  variable key(s)
	 * @return  void
	 */
	public function delete($keys)
	{
		$args = func_get_args();

		foreach ($args as $key)
		{
			if (isset(Session::$protect[$key]))
				continue;

			// Unset the key
			unset($_SESSION[$key]);
		}
	}

	/**
	 * Do not save this session.
	 * This is a performance feature only, if using the native
	 * session "driver" the save will NOT be aborted.
	 *
	 * @return  void
	 */
	public function abort_save()
	{
		Session::$should_save = FALSE;
	}

} // End Session Class
