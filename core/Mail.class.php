<?php

/** 
 * Class Mail
 *
 * Provides static functions used to authenticate users
 *
 * @author SFC Austin Davis <michael.austin.davis@soc.mil>
 * @license ~/LICENSE.md
 */

spl_autoload_register(function($class) {
    require_once __DIR__ . '/../core/' . $class . '.class.php';
});

class Mail
{

    const HEADERS = 'From: Copper Highway <noreply@copperhighway.org>';
    const PARAMS = '-fnoreply@copperhighway.org';

    public static function approved($email, $username)
    {
        $message = "$username,\r\n\r\nYour account on copperhighway.org has been approved! You may now login, create your certificate, download your configuration file and start surfing securely!";
        mail($email, "Vetting complete!", $message, self::HEADERS, self::PARAMS);
    }

    public static function revoked($email, $username)
    {
        $message = "$username,\r\n\r\nYour certificate for use with the CopperHighway.org VPN service has been revoked.  You will no longer be able to connect to the VPN service using your current certificate or configuration file.  If you did not request this action or for additional information, please contact revocations@copperhighway.org.";
        mail($email, "Certificate Revocation", $message, self::HEADERS, self::PARAMS);
    }

    public static function newRegistration($email, $username)
    {
        $message = "$username,\r\n\r\nYou have successfully registered for an account on CopperHighway.org.  Your account is in the process of being vetted.  Once complete, you'll receive another notification e-mail.  If you did not authorize this action, please contact vetting@copperhighway.org.";
        mail($email, "Registration Successful", $message, self::HEADERS, self::PARAMS);
    }
    
    public static function temporaryPassword($email, $username, $temporary_password)
    {
        $message = "$username,\r\n\r\nYour CopperHighway.org web account password has been reset.  You have ONE (1) HOUR to login to your account AND change your password before the temporary password expires.  If you fail to do so within one hour, you'll need to request another password reset.\r\n\r\nYour temporary password is: $temporary_password";
        mail($email, "Your Temporary Password", $message, self::HEADERS, self::PARAMS);
    }

    public static function deletedAccount($email, $username)
    {
        $message = "$username,\r\n\r\nAs per your request, your CopperHighway.org user account has been deleted.  Your VPN certificate has been revoked and you will no longer be able to logon to the website nor will you be able to connect to the VPN service.\r\n\r\nHave a good one.";
        mail($email, "Goodbye.", $message, self::HEADERS, self::PARAMS);
    }

    public static function notifyAdmin($message, $badnews = TRUE)
    {
        if ( $badnews == TRUE ) {
            mail("insdavm@gmail.com", "CopperHighway Bot:  Help!", $message, self::HEADERS, self::PARAMS);
        } else if ( $badnews == FALSE ) {
            mail("insdavm@gmail.com", "CopperHighway Bot:  Heads up", $message, self::HEADERS, self::PARAMS);
        }
    }
}
