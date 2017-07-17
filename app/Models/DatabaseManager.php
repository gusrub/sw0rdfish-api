<?php

namespace Sw0rdfish\Models;

/**
*
*/
class DatabaseManager
{

    public static function getDbConnection()
    {
        $dbHost = getenv("DB_HOST");
        $dbName = getenv("DB_NAME");
        $dbUser = getenv("DB_USER");
        $dbPassword = getenv("DB_PASSWORD");

        $db = new \PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}
