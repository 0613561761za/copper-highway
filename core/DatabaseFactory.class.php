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

class DatabaseFactory
{
    private static $factory;
    private $database;
    
    public static function getFactory()
    {
        if (!self::$factory) {
            self::$factory = new DatabaseFactory();
        }
        return self::$factory;
    }
    
    public function getConnection()
    {
        if (!$this->database) {
            $this->database = new PDO('sqlite:' . __DIR__ . '/../model/data');
        }
        return $this->database;
    }

    /**
     * DatabaseFactory::quickQuery()
     *
     * @param string $sql the SQL command to be queried
     * @param boolean $debugging print errorInfo() or not
     * @return mixed PDOStatment object, or FALSE on failure
     */
    public static function quickQuery($sql, $debugging = FALSE)
    {
        $db = self::getFactory()->getConnection();
        if ( $debugging === TRUE ) {
            $stmt = $db->query($sql);
            var_dump($db->errorInfo());
            return $stmt;
        } else {
            return $db->query($sql);
        }
    }
}

/* EOF */
