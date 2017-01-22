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
        Session::init();
        
        $this->view = new View();

        /* 
         * IMPORTANT:  This function is the doorman for user input,
         * that is, ALL user input should be cleaned by the Filter
         * class here before being passed to any other functions.
         */
        
        if ( empty($_POST) && !empty($_SERVER["QUERY_STRING"]) ) {
            Filter::XSS($_SERVER["QUERY_STRING"]);
            $this->view->render($_SERVER["QUERY_STRING"]);
        } else {
            $this->view->render("home");
        }

        if ( !empty($_POST) ) {
            Filter::XSSArray($_POST);
            $this->handlePost($_POST);
        }
    }

    private function handlePost(array $p)
    {
        switch ( $p['referrer'] ) {

        case "create-account":
            
            $new_user_data = array_intersect_key($_POST, array(
                "first-name" => "",
                "last-name" => "",
                "username" => "",
                "email" => "",
                "password" => "",
                "password-repeat" => "",
                "ref-code" => "")
            );
            
            if ( Authenticator::registerNewUser($new_user_data) ) {
                $this->view->render('account');
            } else {
                $this->view->render('create-account');
            }
            
            break;
        
        default:
            $this->view->showError("400"); /* malformed request */
        }
    }
}


/* EOF */