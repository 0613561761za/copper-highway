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

class View
{
    public function render($page)
    {
        require_once __DIR__ . "/../view/header.php";

        if ( is_array($page) ) {
            foreach ( $page as $key=>$basename ) {
                if ( file_exists(__DIR__ . "/../view/" . $basename . ".php") ) {
                    require_once __DIR__ . "/../view/" . $basename . ".php";
                } else {
                    header("HTTP/1.1 404 Not Found", TRUE, 404);
                    $this->showError("404");
                }
            }
        } else {
            if ( file_exists(__DIR__ . "/../view/" . $page . ".php") ) {
                require_once __DIR__ . "/../view/" . $page . ".php";
            } else {
                header("HTTP/1.1 404 Not Found", TRUE, 404);
                $this->showError("404");
            }
        }
                 
        require_once __DIR__ . "/../view/footer.php";
    }

    public function showError($error)
    {
        if (file_exists(__DIR__ . '/../view/error_pages/' . $error . '.php')) {
            require_once __DIR__ . '/../view/header.php';
            require_once __DIR__ . '/../view/error_pages/' . $error . '.php';
            require_once __DIR__ . '/../view/footer.php';
        } else {
            require_once __DIR__ . '/../view/header.php';
            require_once __DIR__ . '/../view/error_pages/general_error.php';
            require_once __DIR__ . '/../view/footer.php';
        }
    }
}

/* EOF */