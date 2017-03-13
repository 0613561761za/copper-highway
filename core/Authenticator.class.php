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

spl_autoload_register(function($class) {
    require_once __DIR__ . '/../core/' . $class . '.class.php';
});

class Authenticator
{
    /** 
     * @static Authenticator::checkCredentials checks a user's
     * credentials against the database.
     *
     * @param string $username the user's username
     * @param string $password the user's plaintext password
     * @return boolean $result TRUE if username/password match, FALSE otherwise
     */
    public static function checkCredentials($username, $password)
    {
        $db = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT password_hash, temporary_password, temporary_password_expiration FROM users WHERE username=:username LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetchObject();

        if ($result) {

            if (password_verify($password, $result->password_hash)) {

                if ( !empty($result->temporary_password) && (int) $result->temporary_password_expiration < time() ) {                

                    Log::write($username, "Attempted login with expired temporary password by $username", "SECURITY");
                    Session::set("FEEDBACK", "Your temporary password has expired, please reset it, login, and change it immediately.");
                    return FALSE;
                }
                    
                $time = time();
                $sql = "UPDATE users SET last_logon='$time' WHERE username='$username'";
                $db->query($sql);
                Log::write($username, 'Successful login.', 'SECURITY');
                return TRUE;

            } else {

                Log::write($username, 'Failed login: Incorrect Password.', 'SECURITY');
                return FALSE;
            }
            
        } else {

            Log::write($username, 'Failed login: Username does not exist.', 'SECURITY');
            return FALSE;

        }
    }

    /** 
     * @static Authenticator::registerNewUser register a new user.
     *
     * @param array $n user input (cleaned already!!!)
     * @return boolean $result TRUE if registration was successful
     */
    public static function registerNewUser(array $n)
    {
        $first_name = $n['first-name'];
        $last_name = $n['last-name'];
        $username = $n['username'];
        $email = $n['email'];
        $password = $n['password'];
        $password_repeat = $n['password-repeat'];
        $ref_code = array_key_exists('ref-code', $n) ? $n['ref-code'] : '';

        /* 
         * Validate the input.  This is done client side, but that's
         * vulnerable to tampering by cheeky fucks.
         */

        if ( empty($first_name) || empty($last_name) ) {
            Session::set('FEEDBACK', 'First name and last name required.');
            Log::write($username, '(CLIENT SIDE INPUT VALIDATION FAILURE) New user registration failed: first or last name empty.', 'SECURITY');
            return FALSE;
        }
        
        if ( empty($username) ) {
            Session::set('FEEDBACK', 'Username cannot be empty.');
            Log::write($username, '(CLIENT SIDE INPUT VALIDATION FAILURE) New user registration failed: username empty.', 'SECURITY');
            return FALSE;
        }

        if ( preg_match('/\s/', $username) ) {
            Session::set('FEEDBACK', 'Username cannot contain spaces.');
            Log::write($username, '(CLIENT SIDE INPUT VALIDATION FAILURE) New user registration failed: username cannot contain spaces.', 'SECURITY');
            return FALSE;
        }

        if ( empty($email) || !preg_match('/.+@[^\s]+\..+/', $email) ) {
            Session::set('FEEDBACK', 'Invalid e-mail address.');
            Log::write($username, '(CLIENT SIDE INPUT VALIDATION FAILURE) New user registration failed: invalid e-mail address.', 'SECURITY');
            return FALSE;
        }

        if ( self::checkUserExists($username, $email, FALSE) ) {
            Session::set('FEEDBACK', 'That username or email is unavailable.');
            Log::write($username, '(CLIENT SIDE INPUT VALIDATION FAILURE) New user registration failed: username or e-mail already taken.', 'SECURITY');
            return FALSE;
        }

        if ( empty($password) || empty($password_repeat) || ($password !== $password_repeat) ) {
            Session::set('FEEDBACK', 'Passwords do not match.');
            Log::write($username, '(CLIENT SIDE INPUT VALIDATION FAILURE) New user registration failed: passwords did not match or were empty.', 'SECURITY');
            return FALSE;
        }

        if ( preg_match('/\s/', $password) ) {
            Session::set('FEEDBACK', 'Password cannot contain spaces.');
            Log::write($username, '(CLIENT SIDE INPUT VALIDATION FAILURE) New user registration failed: password cannot contain spaces.', 'SECURITY');
            return FALSE;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $time = time();
        $clearance = 0;
        
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "INSERT INTO users (first_name, last_name, username, email, password_hash, last_logon, clearance, account_creation_date, ref_code) VALUES (:first_name, :last_name, :username, :email, :password_hash, :last_logon, :clearance, :account_creation_date, :ref_code)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':last_logon', $time);
        $stmt->bindParam(':clearance', $clearance);
        $stmt->bindParam(':account_creation_date', $time);
        $stmt->bindParam(':ref_code', $ref_code);
        if ($stmt->execute()) {
            Log::write($username, 'New user (' . $username . ') registered successfully.', 'SECURITY');
            return TRUE;
        }
        
        Log::write($username, 'New user registration failed becuase the data could not be committed to the database.', 'SECURITY');
        return FALSE;
    }

    /** 
     * @static Authenticator::login logs a user into the system
     */
    public static function login($username, $clearance = 0)
    {
        Session::set('USERNAME', $username);
        Session::set('CLEARANCE', $clearance);
    }

    /**
     * @static Authenticator::logout logs a user out
     */
    public static function logout()
    {
        Log::write(Session::get('USERNAME'), 'Successful logout.', 'SECURITY');
        Session::destroy();
        Session::init();
    }

    /**
     * @static Authenticator::checkStatus checks a user's status
     * @return string $string the status of the user
     */
    public static function checkStatus()
    {
        if (!Session::get('USERNAME')) {
            return 'NOT LOGGED IN';
        }

        switch (Session::get('CLEARANCE')) {
	    case 0:
            return 'USER';
            break;
	    case 1:
            return 'ADMINISTRATOR';
            break;
	    case 2:
            return 'ROOT';
            break;
	    default:
            return 'ERROR';
        }
    }

    /**
     * @static Authenticator::loggedIn returns TRUE if a user is logged in 
     */
    public static function loggedIn()
    {
        if (!Session::get('USERNAME')) {
            return FALSE;
        } else if (Session::get('USERNAME')) {
            return TRUE;
        }
    }
    
    /**
     * @static Authenticator::getClearance get clearance level of a user
     *
     * @param string $username the username for which to fetch the clearance
     * @return string $clearance the clearance level:
     *      0: USER
     *      1: ADMINISTRATOR
     *      2: ROOT 
     */
    public static function getClearance($username)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT clearance FROM users WHERE username=:username LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetchObject();
        return $result->clearance;
    }

    /**
     * @static Authenticator::checkUserExists check if a user exists or not
     *
     * @param string $username the username to check
     * @param string $email the email to check
     * @param bool $strict should we check username AND e-mail (TRUE) or just one (FALSE)?
     * @return bool TRUE if both username and email exists, FALSE otherwise
     */
    public static function checkUserExists($username, $email, $strict = FALSE)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        
        if ($strict) {
            $sql = "SELECT * FROM users WHERE username='$username' AND email='$email' LIMIT 1";
        } else if (!$strict) {
            $sql = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
        }
        
        $result = $db->query($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Authenticator::randomPassword()
     *
     * @param int $length the length of the password to return; defaults to 8
     * @return string $randomPassword a pseudo-random password
     */
    public static function randomPassword($length = 8)
    {
        $randomPassword = '';
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[mt_rand(0, strlen($characters)-1)];
        }
        return $randomPassword;
    }    

}

/* EOF */
