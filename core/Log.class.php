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

spl_autoload_register(function ($class) {
    require_once __DIR__ . '/../core/' . $class . '.class.php';
});

class Log
{
    /**
     * Write a log message to the database
     *
     * @param string $username the username for the user that is currently logged in
     * @param string $message the log message
     * @param string $type the type of message: SECURITY, ERROR, OR NOTICE.
     */
    public static function write($username, $message, $type = 'NOTICE')
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $time = time();
        $sql = "INSERT INTO log (time, username, message, type) VALUES ('$time', '$username', '$message', '$type')";
        $db->query($sql);
    }

    /**
     * Dump messages from the database
     *
     * @return array $messages dump of the log messages 
     */
    public static function dump()
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT * FROM log";
        $stmt = $db->query($sql);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $messages;
    }   
}

/* EOF */
