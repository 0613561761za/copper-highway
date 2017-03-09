<?php

/**
 * Copper Highway
 * 
 * A VPN service co-op
 * 
 * @author Austin <austin@copperhighway.org>
 * @version 1.0
 * @date 2017.01.21
 */

class Session
{
    /**
     * @static Session::init starts or resumes a session
     *
     * @param string $name the name of the session (e.g., PHPSESSID)
     * @param int $limit the lifetime of the session cookie in seconds (0 = until the browser is closed)
     * @param string $path the path on the domain where the session cookie will work ('/' = all paths)
     * @param string $domain the domain that the cookie is visible to (allow subdomains like '.php.net')
     * @param boolean $secure if TRUE cookie will only be sent over secure connections
     */ 
    public static function init($name = 'CH', $limit = 0, $path = '/', $domain = NULL, $secure = NULL)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {

            session_name($name . '_session');
            $domain = isset($domain) ? $domain : $_SERVER['SERVER_NAME'];
            $secure = isset($secure) ? $secure : isset($_SERVER['HTTPS']);
            session_set_cookie_params($limit, $path, $domain, $secure, TRUE);
            session_start();

        }	
    }

    /**
     * @static Session::destroy tear down the session
     */
    public static function destroy()
    {
	self::init();
	$_SESSION = array();
	$cookie_parameters = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $cookie_parameters['path'], $cookie_parameters['domain'], $cookie_parameters['secure'], $cookie_parameters['httponly']);
	session_destroy();
    }

    /** 
     * @static Session::set sets a key/value pair into the $_SESSION variable
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @static Session::get gets the value for $key in $_SESSION[]
     * @param mixed $key the array $key
     * @return mixed $value the array value for $key or FALSE if doesn't exist
     */
    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            return $value;
        } else {
            return FALSE;
        }
    }

    /**
     * @static Session::kill unsets a value from the $_SESSION array
     * @param mixed $key the key of the value to unset
     * @return TRUE if value was killed, FALSE if value didn't exist to begin with
     */
    public static function kill($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
/* EOF */

