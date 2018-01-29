<?php

namespace Sw0rdfish\Models;

/**
*
*/
class DatabaseManager
{

    const SQLITE_PRAGMAS = [
        'foreign_keys' => 'ON'
    ];

    public static function getDbConnection()
    {
        $dbDriver = getenv("DB_DRIVER");
        $db = null;

        switch ($dbDriver) {
            case 'sqlite':
                $db = self::getSQLiteConnection();
                break;
            case 'mysql':
                $db = self::getMySqlConnection();
                break;
            default:
                throw new \Exception("Uknown driver '$dbDriver', valid values are 'sqlite' and 'mysql'");
        }

        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }

    public static function getTruncateQuery($table)
    {
        $dbDriver = getenv("DB_DRIVER");
        $query = null;

        switch ($dbDriver) {
            case 'sqlite':
                $query = [
                    "PRAGMA foreign_keys = OFF;",
                    "DELETE FROM $table;",
                    "DELETE FROM sqlite_sequence WHERE name='$table';",
                    "PRAGMA foreign_keys = ON;"
                ];
                break;
            case 'mysql':
                $query = "SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE $table; SET FOREIGN_KEY_CHECKS = 1;";
                break;
            default:
                throw new \Exception("Uknown driver '$dbDriver', valid values are 'sqlite' and 'mysql'");
        }

        return $query;
    }

    private static function getMySqlConnection()
    {
        $dbHost = getenv("DB_HOST");
        $dbName = getenv("DB_NAME");
        $dbUser = getenv("DB_USER");
        $dbPassword = getenv("DB_PASSWORD");

        return new \PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    }

    private static function getSQLiteConnection()
    {
        $dbName = getenv("DB_NAME");
        $connection = new \PDO("sqlite:".__DIR__."../../../$dbName.db3");

        // unfortunately we need to enable pragma for foreign keys on each connection :(
        if ($connection) {
            foreach (self::SQLITE_PRAGMAS as $pragma => $value) {
                $statement = $connection->prepare("PRAGMA $pragma = $value;");
                $statement->execute();
            }
        }

        return $connection;
    }
}
