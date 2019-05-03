<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

/**
 * This class contains the core functionality to connect to the database and
 * execute PDO statements.
 */
class DatabaseManager
{

    /**
     * Defines any SQLite pragmas to be set before each connection.
     */
    const SQLITE_PRAGMAS = [
        'foreign_keys' => 'ON'
    ];

    /**
     * Gets a PDO connection object based on the db driver configuration set
     * on the application.
     */
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
                throw new \Exception(
                    I18n::translate(
                        "Unknown driver '{dbDriver}', valid values are 'sqlite' and 'mysql'",
                        [
                            'dbDriver' => $dbDriver
                        ]
                    )
                );
        }

        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }

    /**
     * Generates a truncation SQL statement to remove all records from a table
     * depending based on the db driver configuration set on the application.
     *
     * @param String $table The table name that will be truncated.
     * @return String The truncation query.
     */
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
                throw new \Exception(
                    I18n::translate(
                        "Unknown driver '{dbDriver}', valid values are 'sqlite' and 'mysql'",
                        [
                            'dbDriver' => $dbDriver
                        ]
                    )
                );
        }

        return $query;
    }

    /**
     * Returns a PDO connection object for MySql ready to be used.
     *
     * @return \PDO A PDO connection object for MySql
     */
    private static function getMySqlConnection()
    {
        $dbHost = getenv("DB_HOST");
        $dbName = getenv("DB_NAME");
        $dbUser = getenv("DB_USER");
        $dbPassword = getenv("DB_PASSWORD");

        return new \PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    }

    /**
     * Returns a PDO connection object for SQLite ready to be used.
     *
     * @return \PDO A PDO connection object for SQLIte
     */
    private static function getSQLiteConnection()
    {
        $dbName = getenv("DB_NAME");
        $dbPath = getenv("DB_PATH");
        $connection = new \PDO("sqlite:"."$dbPath/$dbName.db3");

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
