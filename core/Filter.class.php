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

class Filter
{
    /**
     * @static Filter::XSS filters code out of a string
     *
     * @param string $string value to be sanitized
     * 
     * Input to this function is passed by reference! Use it
     * like this: Filter::XSS($user_input)
     */
    public static function XSS(&$string)
    {
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @static Filter::XSSArray filters code out of an array
     *
     * @param array $array array of values to be sanitized
     */
    public static function XSSArray(array &$array)
    {
        foreach ($array as $key=>$value) {
            self::XSS($value);
            $array[$key] = $value;
        }
    }
}

/* EOF */
