<?php

/** 
 * Class CSRF
 *
 * Provides Cross-Site Request Forgery (CSRF) protection
 * through the generation and verification of random tokens
 *
 * @author SFC Austin Davis <michael.austin.davis@soc.mil>
 * @license ~/LICENSE.md
 */

spl_autoload_register(function($class) {
    require_once __DIR__ . '/../core/' . $class . '.class.php';
});

class CSRF
{
    /**
     * @static CSRF::makeToken make a CSRF token
     * 
     * @return string $csrf_token the CSRF token 
     */
    public static function makeToken()
    {
        $csrf_token_lifetime = 60 * 60; // 60 MINUTES
        $csrf_token_birth = Session::get('CSRF_TOKEN_BIRTH');
        $csrf_token = Session::get('CSRF_TOKEN');

        if (($csrf_token_birth + $csrf_token_lifetime <= time()) || empty($csrf_token)) {
            $csrf_token = base64_encode(openssl_random_pseudo_bytes(32));
            Session::set('CSRF_TOKEN', $csrf_token);
            Session::set('CSRF_TOKEN_BIRTH', time());
	}
        return $csrf_token;
    }

    /** 
     * @static CSRF::verifyToken verify a CSRF token
     *
     * @param string $token the CSRF token to verify
     * @return boolean TRUE if token matches, FALSE otherwise
     */
    public static function verifyToken($token)
    {
        if ($token === Session::get('CSRF_TOKEN')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

/* EOF */
