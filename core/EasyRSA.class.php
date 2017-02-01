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

class easyRSA
{   
    public static function certWizard($username, $password)
    {
        $path = rtrim(Config::getField('EASY_RSA_PATH'), '/');
        $username_c = escapeshellarg($username);
        
        $cmd = 'RANDFILE=' . $path . '/pki/.rnd ' . $path . '/easyrsa build-client-full ' . $username_c;

        /* set up our pipes */
        $descriptorspec = array(
            0 => array('pipe', 'r'), /* STDIN */
            1 => array('pipe', 'w'), /* STDOUT */
            2 => array('file', '/tmp/copperhighway-easy-rsa-errors', 'a') /* STDERR */
        );

        /*
         * openssl is kindof a cunt when it comes to accepting
         * input for the private key password... we can use
         * the 'script' program to trick openssl into believing
         * were at an interactive TTY and not interprocess 
         * communication, which is what we're actually doing.
         */
        $proc = proc_open(
            'script -qfc "' . $cmd . ' >/tmp/trash.out" /dev/null 2>&1',
            $descriptorspec,
            $pipes,
            $path
        );

        if (is_resource($proc)) {

            $buffer = "";

            /* read from STDOUT until the program is done */
            while (!feof($pipes[1])) {
        
                $input = fread($pipes[1], 8192);
                $buffer .= $input;

                /* debugging */
                //echo $input;

                if (preg_match('/\nEnter PEM pass phrase:$/i', $buffer)) {
                    fwrite($pipes[0], $password."\n");
                } elseif (preg_match('/\nVerifying - Enter PEM pass phrase:$/i', $buffer)) {
                    fwrite($pipes[0], $password."\n");
                } elseif (preg_match('/(error|denied)/i', $buffer)) {
                    return FALSE;
                }
            }

            fclose($pipes[0]);
            fclose($pipes[1]);
            proc_close($proc);

            return TRUE;
            
        } else {
            return FALSE;
        }
    }

    public static function revoke($username)
    {
        $path = rtrim(Config::getField('EASY_RSA_PATH'), '/');
        $stdout = array();
        $ret = null;
        exec("cd $path; ./easyrsa revoke " . $username . " 2>&1", $stdout, $ret);

        if ( $ret == 0 ) {
            exec("cd $path; ./easyrsa gen-crl");
            return TRUE;
        } else if ( $ret == 1 ) {
            return FALSE;
        }
    }
}


/* EOF */