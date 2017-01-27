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

            case "contribute":
                if ( Authenticator::loggedIn() ) {
                    $this->view->render("contribute");
                } else {
                    $this->view->render("home");
                }
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

            if ( !empty($p["password"]) && !empty($p["password-repeat"]) && $p["password"] == $p["password-repeat"] ) {

                $username = Session::get("USERNAME");

                if ( EasyRSA::certWizard($username, $p["password"]) ) {

                    $path = rtrim(Config::getField("CH_ROOT"), "/");
                    shell_exec("cd $path/ovpn/ && $path/ovpn/make_unified.sh " . $username . " 2>&1");
                    $conf_path = "$path/ovpn/" . $username . ".ovpn";
                    DatabaseFactory::quickQuery("UPDATE users SET conf_path='$conf_path' WHERE username='$username'");
                    Session::set("FEEDBACK", "Certificate and configuration file generated!");
                    Log::write($username, "certWizard: certificate and conf file created for $username", "NOTICE");
                    $this->view->render("userhome");

                } else {
                    Session::set("FEEDBACK", "Couldn't generate your certificate or configuration file.  Try again later.");
                    $this->view->render("userhome");
                }
                
            } else {
                Log::write(Session::get("USERNAME"), "Create certificate failed: passwords were empty or did not match", "NOTICE");
                Session::set("FEEDBACK", "Your passwords did not match.");
                $this->view->render("userhome");
            }
            
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

            $username = $p["username"];
            $email = $p["email"];
            $temporary_password = Authenticator::randomPassword(12);
            $password_hash = password_hash($temporary_password, PASSWORD_DEFAULT);
            $temporary_password_expiration = (int) time() + 3600; /* 1 hour */
            $sql = "UPDATE users SET password_hash='$password_hash', temporary_password_expiration='$temporary_password_expiration', temporary_password='$temporary_password' WHERE username='$username' AND email='$email'";
            
            if ( Authenticator::checkUserExists($username, $email) ) {

                if ( DatabaseFactory::quickQuery($sql) ) {

                    Log::write(Session::get("USERNAME"), "Password reset successfully for $username ($email)", "NOTICE");
                    Session::set("FEEDBACK", "Password reset&mdash;check your e-mail.");
                    $this->view->render("forgot-password");

                } else {

                    Log::write(Session::get("USERNAME"), "Password could not be reset for $username ($email): database could not be written to.", "ERROR");
                    Session::set("FEEDBACK", "Your password could not be reset because of a system error.  Try again later.");
                    $this->view->render("forgot-password");
                }
                
            } else {
                
                Log::write(Session::get("USERNAME"), "Password could not be reset for $username ($email):  does not exist.", "ERROR");
                Session::set("FEEDBACK", "That username and e-mail combination doesn't exists.");
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
                $sql = "UPDATE users SET password_hash='$password_hash' WHERE username='$username'";

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
        
        default:
            $this->view->showError("400"); /* malformed request */
        }
    }
}


/* EOF */