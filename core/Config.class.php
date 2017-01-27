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

class Config
{
        /* The filename of our config file */
    const CONFIG_FILE_NAME = 'CH.conf';
    
    /**
     * Get the value of a specific field
     *
     * @param mixed $field the value for which $field
     * is the key, which could be an array.
     */
    public static function getField($field)
    {
        $conf = parse_ini_file(__DIR__ . '/../' . self::CONFIG_FILE_NAME);
        return $conf[$field];
    }
}