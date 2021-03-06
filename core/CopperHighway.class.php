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
    require __DIR__ . "/../core/" . $class . ".class.php";
});

class CopperHighway
{
    public $controller;
    public $view;
    public $db;
    
    public function __construct()
    {
        $this->main();
    }

    public function __destruct()
    {
        Session::kill('FEEDBACK');
    }

    private function main()
    {
        /* 
         * IMPORTANT:  This function is the doorman for user input,
         * that is, ALL user input should be cleaned by the Filter
         * class here before being passed to any other functions.
         */
        Session::init();

        $this->view = new View();

        /* handle a request for / */
        if ( empty($_POST) && empty($_SERVER["QUERY_STRING"]) ) {
            $this->view->render("home");
        }
        
        /* handle POST data */
        if ( !empty($_POST) ) {
            Filter::XSSArray($_POST);
            $this->handlePost($_POST);
        }

        /* handle links (sent as a GET request) */
        if ( empty($_POST) && !empty($_SERVER["QUERY_STRING"]) ) {

            Filter::XSS($_SERVER["QUERY_STRING"]);

            switch ( $_SERVER["QUERY_STRING"] ) {

            case "about":
                $this->view->render("about");
                break;
                
            case "getting-started":
                if ( Authenticator::loggedIn() ) {
                    $this->view->render("getting-started");
                } else {
                    $this->view->render("home");
                }
                break;

            case "admin-console":
                if ( Authenticator::loggedIn() && Session::get("CLEARANCE") == 2 ) {
                    $this->view->render("admin-console");
                } else {
                    $this->view->render("home");
                }
                break;

            case "goaccess":
                if ( Authenticator::loggedIn() && Session::get("CLEARANCE") == 2 ) {
                    $output = array();
                    $ret = NULL;
                    $ch_root = rtrim(Config::getField("CH_ROOT"), "/");
                    $nginx_log_dir = rtrim(Config::getField("NGINX_LOG_DIR"), "/");
                    $geo_ip_path = rtrim(Config::getField("GEO_IP_PATH"), "/");
                    exec('zcat -f ' . $nginx_log_dir . '/access* | goaccess -o ' . $ch_root . '/view/goaccess.html --geoip-database ' . $geo_ip_path);
                    $this->view->goAccess();
                } else {
                    $this->view->render("home");
                }
                break;

            case "log":
                if ( Authenticator::loggedIn() && Session::get("CLEARANCE") == 2 ) {
                    $this->view->render("log");
                } else {
                    $this->view->render("home");
                }
                break;
                
            case "account":
                if ( Authenticator::loggedIn() ) {
                    $this->view->render("userhome");
                } else {
                    $this->view->render("account");
                }
                break;

            case "create-account":
                if ( Authenticator::loggedIn() ) {
                    $this->view->render("userhome");
                } else {
                    $this->view->render("create-account");
                }
                break;

            case "forgot-password":
                if ( Authenticator::loggedIn() ) {
                    $this->view->render("userhome");
                } else {
                    $this->view->render("forgot-password");
                }
                break;

            case "change-password":
                if ( Authenticator::loggedIn() ) {
                    $this->view->render("change-password");
                } else {
                    $this->view->render("home");
                }
                break;

            case "delete-account":
                if ( Authenticator::loggedIn() ) {
                    $this->view->render("delete-account");
                } else {
                    $this->view->render("home");
                }
                break;                
                
            case "download-configuration":
                if ( Authenticator::loggedIn() ) {
                    include __DIR__ . "/download-configuration.php";
                } else {
                    $this->view->render("home");
                }
                break;                

            case "logout":
                if ( Authenticator::loggedIn() ) {
                    Authenticator::logout();
                    Session::set("FEEDBACK", "You have been logged out.");
                }
                $this->view->render("home");
                break;
                
            default:
                $this->view->render("home");
            }
        }
    }

    /** 
     * Handle POST data 
     *
     * @param $p an array of POST data... usually just from $_POST
     */
    private function handlePost(array $p)
    {

        /* every POST request must have a valid CSRF token */
        if ( empty($p["csrf"]) ) {
            Log::write(Session::get("USERNAME"), 'Failed to process POST request: CSRF token was empty!', 'CSRF_ERROR');
            die($this->view->showError('csrf'));
        }
        
        if ( !CSRF::verifyToken($p["csrf"]) ) {
            Log::write(Session::get("USERNAME"), 'Failed to process POST request: CSRF token verification failed!', 'CSRF_ERROR');
            die($this->view->showError('csrf'));
        }
        

        switch ( $p['referrer'] ) {

        case "create-cert":

            if ( !Authenticator::loggedIn() ) {
                $this->view->showError("403");
                break;
            }

            if ( empty($p["password"]) || empty($p["password-repeat"]) || $p["password"] != $p["password-repeat"] ) {
                Session::set("FEEDBACK", "Your passwords did not match.");
                $this->view->render("userhome");
                break;
            }

            if ( preg_match('/\s/', $p["password"]) ) {
                Session::set("FEEDBACK", "Passwords cannot contain spaces.");
                $this->view->render("userhome");
                break;
            }

            $username = Session::get("USERNAME");

            if ( EasyRSA::certWizard($username, $p["password"]) ) {

                $path = rtrim(Config::getField("CH_ROOT"), "/");
                shell_exec("cd $path/ovpn/ && $path/ovpn/make_unified.sh " . $username);
                $conf_path = "$path/ovpn/" . $username . ".ovpn";
                DatabaseFactory::quickQuery("UPDATE users SET conf_path='$conf_path' WHERE username='$username'");
                Session::set("FEEDBACK", "Certificate and configuration file generated!");
                Log::write($username, "certWizard: certificate and conf file created for $username", "NOTICE");
                $this->view->render("userhome");

            } else {
                Session::set("FEEDBACK", "Couldn't generate your certificate or configuration file.  Try again later.");
                $this->view->render("userhome");
            }
            
            break;

        case "approve-user":

            if ( !Authenticator::loggedIn() || Session::get("CLEARANCE") != 2) {
                $this->view->showError("403");
                break;
            }

            $uid = $p["uid"];
            $db = DatabaseFactory::quickQuery("SELECT email, username FROM users WHERE uid='$uid' LIMIT 1");
            $row = $db->fetch(PDO::FETCH_ASSOC);
            $email = $row['email'];
            $username = $row['username'];            
            $sql = "UPDATE users SET approved=1 WHERE uid='$uid'";

            if ( !DatabaseFactory::quickQuery($sql) ) {
                Log::write(Session::get("USERNAME"), "Attempt to approve UID $uid failed, could not commit to DB.", "ERROR");
                Session::set("FEEDBACK", "Error: couldn't commit changes to the database!");
            } else {
                Mail::approved($email, $username);
                Log::write(Session::get("USERNAME"), "User ID $uid approved, email sent", "NOTICE");
                Session::set("FEEDBACK", "User (UID: $uid) approved, e-mail sent");
            }

            $this->view->render("admin-console");
            
            break;

        case "revoke-user":

            if ( !Authenticator::loggedIn() || Session::get("CLEARANCE") != 2) {
                $this->view->showError("403");
                break;
            }

            $uid = $p["uid"];
            $db = DatabaseFactory::quickQuery("SELECT email, username FROM users WHERE uid='$uid' LIMIT 1");
            $row = $db->fetch(PDO::FETCH_ASSOC);
            $email = $row['email'];
            $username = $row['username'];            
            $sql = "UPDATE users SET cert_revoked=1 WHERE uid='$uid'";

            if ( !DatabaseFactory::quickQuery($sql) || !EasyRSA::revoke($username) ) {
                Log::write(Session::get("USERNAME"), "Attempt to revoke UID $uid certificate failed, could not commit to DB.", "ERROR");
                Session::set("FEEDBACK", "Error: couldn't commit changes to the database!");
            } else {
                Mail::revoked($email, $username);
                Log::write(Session::get("USERNAME"), "User ID $uid certificate revoked.", "SECURITY");
                Session::set("FEEDBACK", "User (UID: $uid) revoked, e-mail sent");
            }

            $this->view->render("admin-console");
            
            break;

        case "update-record":

            if ( !Authenticator::loggedIn() || Session::get("CLEARANCE") != 2) {
                $this->view->showError("403");
                break;
            }

            $uid = $p["uid"];
            $field = $p["field"];
            $value = $p["value"] ? $p["value"] : 0;

            $sql = "UPDATE users SET $field='$value' WHERE uid='$uid'";

            if ( !DatabaseFactory::quickQuery($sql) ) {
                Session::set("FEEDBACK", "Error: couldn't commit changes to the database!");
            }

            $this->view->render("admin-console");
            
            break;
            
        case "create-account":
            
            $new_user_data = array_intersect_key($p, array(
                "first-name" => "",
                "last-name" => "",
                "username" => "",
                "email" => "",
                "password" => "",
                "password-repeat" => "",
                "ref-code" => "")
            );

            if ( Authenticator::registerNewUser($new_user_data) ) {
                Mail::newRegistration($new_user_data['email'], $new_user_data['username']);
                Mail::notifyAdmin("New user registered!", FALSE);
                Authenticator::login($new_user_data['username']);
                Session::set('FEEDBACK', 'Account created successfully.  You are now logged in.');
                $this->view->render("userhome");
            } else {
                $this->view->render("create-account");
            }

            break;

        case "account":
         
            if ( Authenticator::checkCredentials($p["username"], $p["password"]) ) {
                $clearance = Authenticator::getClearance($p["username"]);
                Authenticator::login($p["username"], $clearance);
                $this->view->render("userhome");
            } else {
                if ( !Session::get("FEEDBACK") ) {
                    Session::set("FEEDBACK", "Invalid credentials!");
                }
                $this->view->render("account");
            }
            break;

        case "forgot-password":

            if ( empty($p["email"]) ) {
                Session::set('FEEDBACK', "You must enter an e-mail address.");
                $this->view->render("forgot-password");
                break;
            }
            
            $email = $p["email"];
            $temporary_password = Authenticator::randomPassword(12);
            $password_hash = password_hash($temporary_password, PASSWORD_DEFAULT);
            $temporary_password_expiration = (int) time() + 3600; /* 1 hour */
            $sql = "UPDATE users SET password_hash='$password_hash', temporary_password_expiration='$temporary_password_expiration', temporary_password='$temporary_password' WHERE email='$email'";
            
            if ( Authenticator::checkUserExists("null", $email) ) {

                if ( DatabaseFactory::quickQuery($sql) ) {

                    $username = Authenticator::getUsernameFromEmail($email);
                    if ( $username == FALSE ) $username = ""; 
                    Mail::temporaryPassword($email, $username, $temporary_password);
                    Log::write(Session::get("USERNAME"), "Password reset successfully for $email", "NOTICE");
                    Session::set("FEEDBACK", "Password reset for $email if it exists&mdash;check your e-mail.");
                    $this->view->render("forgot-password");

                } else {

                    Log::write(Session::get("USERNAME"), "Password could not be reset for $email: database could not be written to.", "ERROR");
                    Session::set("FEEDBACK", "Your password could not be reset because of a system error.  Try again later.");
                    $this->view->render("forgot-password");
                }
                
            } else {
                
                Log::write(Session::get("USERNAME"), "Password could not be reset for $email:  does not exist.", "SECURITY");
                Session::set("FEEDBACK", "Password reset for $email if it exists&mdash;check your e-mail.");
                $this->view->render("forgot-password");
            }
            
            break;

        case "change-password":

            if ( !Authenticator::loggedIn() ) {
                $this->view->showError("403");
                break;
            }
            
            if ( $p["password"] == $p["password-repeat"] ) {

                $username = Session::get("USERNAME");
                $password_hash = password_hash($p["password"], PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password_hash='$password_hash', temporary_password='', temporary_password_expiration='' WHERE username='$username'";

                if ( !DatabaseFactory::quickQuery($sql) ) {
                    Log::write($username, "Couldn't change password: database error.", "ERROR");
                    Session::set("FEEDBACK", "Couldn't change password because of a weird system error");
                    $this->view->render("change-password");
                } else {
                    Log::write($username, "Password changed for $username.", "SECURITY");
                    Session::set("FEEDBACK", "Your password has been changed.");
                    $this->view->render("userhome");             
                }

            } else {

                Session::set("FEEDBACK", "Passwords don't match.");
                $this->view->render("change-password");
            }

            break;

        case "delete-account":

            if ( !Authenticator::loggedIn() ) {
                $this->view->showError("403");
                break;
            }

            if ( Session::get("USERNAME") != $p["username"] ) {
                Session::set("FEEDBACK", "You entered a username other than your own.");
                $this->view->render("delete-account");
                break;
            }
            
            if ( Authenticator::checkCredentials($p["username"], $p["password"]) ) {
                             
                $username = Session::get("USERNAME");
                $stmt = DatabaseFactory::quickQuery("SELECT conf_path, email FROM users WHERE username='$username' LIMIT 1");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $email = $row["email"];
                $conf_path = $row["conf_path"];

                Mail::deletedAccount($email, $username);

                exec("rm -f $conf_path");
                
                if ( !EasyRSA::revoke($username) ) {
                    Mail::notifyAdmin("CopperHighway Bot:  Could not revoke cert for $username.  Please revoke manually and update the CRL.  Have a good one.");
                }

                if ( !DatabaseFactory::quickQuery("DELETE FROM users WHERE username='$username'") ) {
                    Mail::notifyAdmin("CoppeHighway Bot:  Could not delete $username from the database. Please investigate. Have a good one.");
                }

                if ( Authenticator::loggedIn() ) {
                    Authenticator::logout();
                    Session::set("FEEDBACK", "Have a good one.");
                }
                $this->view->render("home");

            } else {

                Session::set("FEEDBACK", "Invalid credentials!");
                Log::write(Session::get("USERNAME"), "Error while trying to delete user account: invalid credentials", "NOTICE");
                $this->view->render("delete-account");
            }

            break;
        
        default:
            $this->view->showError("400"); /* malformed request */
        }
    }
}


/* EOF */