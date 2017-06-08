<?php

namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;

/**
*
*/
class BaseModel
{

    public $id;
    public $createdDate;
    public $updatedDate;

    const SORT_KEYWORDS = ["ASC", "DESC"];

    public function __construct(Array $args = null)
    {
        if($args !== null) {
            foreach ($args as $key => $value) {
                if (property_exists(static::class, $key)) {
                    $this->{$key} = $value;
                } else {
                    throw new ModelException(sprintf("Property '%s' does not exist in '%s'", $key, static::class));

                }
            }
        }
    }

    protected static function getDbConnection()
    {
        $dbHost = getenv("DB_HOST");
        $dbName = getenv("DB_NAME");
        $dbUser = getenv("DB_USER");
        $dbPassword = getenv("DB_PASSWORD");

        $db = new \PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $db;
    }

    protected static function generateBoundParams(Array $args)
    {
        $paramKeys = array_keys($args);
        $paramValues = array_values($args);
        array_walk($paramKeys, function(&$key){
            $key = ":$key";
        });

        return array_combine($paramKeys, $paramValues);
    }

    protected static function allowedOrderField(Array $args)
    {
        if (isset($args) && array_key_exists("orderBy", $args)) {
            if (property_exists(static::class, $args["orderBy"])) {
                return $args["orderBy"];
            }
        }

        return "id";
    }

    protected static function allowedSortDirection(Array $args)
    {
        if (isset($args) && array_key_exists("sort", $args)) {
            if (in_array(strtolower($args["sort"]), self::SORT_KEYWORDS)) {
                return $args["sort"];
            }
        }

        return self::SORT_KEYWORDS[0];
    }

    public static function generateLimitAndOffset(Array $args)
    {
        if (isset($args) && array_key_exists("page", $args)) {
            $page = $args["page"];
            $limit = getenv("MAX_RECORDS_PER_PAGE");
            $offset = ($limit * $page)-$limit;
            return sprintf("LIMIT %s OFFSET %s", $limit, $offset);
        }

        return null;
    }

    public static function all(Array $args = null)
    {
        try {
            // default options
            $orderBy = self::allowedOrderField($args);
            $sort = self::allowedSortDirection($args);
            $paginate = self::generateLimitAndOffset($args);

            $db = self::getDbConnection();
            $query = sprintf("SELECT * FROM %s ORDER BY %s %s %s;", static::TABLE_NAME, $orderBy, $sort, $paginate);
            $statement = $db->prepare($query);
            $statement->execute();
            $resultSet = $statement->fetchAll(\PDO::FETCH_CLASS, self::class);
            return $resultSet;
        } catch (\PDOException $e) {
            $message = sprintf("Error while loading records from '%s'", static::TABLE_NAME);
            throw new ModelException($message, $e);
        }
    }

    public static function get($id)
    {
        try {
            $db = self::getDbConnection();

            $query = sprintf("SELECT * FROM %s WHERE ID=:id", static::TABLE_NAME);
            $statement = $db->prepare($query);
            $statement->bindValue(":id", $id);
            $statement->execute();

            $result = $statement->fetchObject(static::class);

            return $result;
        } catch (\PDOException $e) {
            $message = sprintf("Error while getting record from '%s' with ID '%s' ", static::TABLE_NAME, $id);
            throw new ModelException($message, $e);
        }
    }

    public static function create(Array $args)
    {
        try {
            $db = self::getDbConnection();

            // Set the creation date and remove ID if given
            unset($args["id"]);
            $args["createdDate"] = date("c");

            // generate the field name list and the bound params array for PDO
            $fields = implode(", ", array_keys($args));
            $params = self::generateBoundParams($args);

            $sql = sprintf("INSERT INTO %s (%s) VALUES (%s);", static::TABLE_NAME, $fields, implode(", ", array_keys($params)));
            $query = $db->prepare($sql);

            // bind each value safely
            foreach ($params as $param => $value) {
                $query->bindValue($param, $value);
            }

            // execute the query
            $query->execute();
            $args["id"] = $db->lastInsertId();

            return new User($args);

        } catch (\PDOException $e) {
            $message = sprintf("Error while creating record in '%s'", static::TABLE_NAME);
            throw new ModelException($message, $e);
        }
    }

    public static function update($id, Array $args)
    {
        try {
            $db = self::getDbConnection();

            // Set the update date and ID since this is an update
            $args["updatedDate"] = date("c");
            $args["id"] = $id;

            // generate the field name list and the bound params array for PDO
            $fields = array_keys($args);
            $params = self::generateBoundParams($args);
            array_walk($fields, function(&$key){
                $key = "$key=:$key";
            });
            $fields = implode(", ", $fields);

            $sql = sprintf("UPDATE %s SET %s WHERE id=:id;", static::TABLE_NAME, $fields, $id);
            $query = $db->prepare($sql);

            // bind each value safely
            foreach ($params as $param => $value) {
                $query->bindValue($param, $value);
            }

            // execute the query
            $query->execute();

            return new User($args);

        } catch (\PDOException $e) {
            $message = sprintf("Error while updating record in '%s' with ID '%s'", static::TABLE_NAME, $id);
            throw new ModelException($message, $e);
        }
    }

    public function isNew()
    {
        return $this->id <= 0;
    }

    public function save()
    {
        $args = get_object_vars($this);
        $savedObject = $this->isNew() ? static::create($args) : static::update($this->id, $args);

        $this->id = $savedObject->id;
        $this->createdDate = $savedObject->createdDate;
        $this->updatedDate = $savedObject->updatedDate;

        return true;
    }

    public function delete()
    {
        try {
            $db = self::getDbConnection();

            $sql = "DELETE FROM " .static::TABLE_NAME. " WHERE id=:id";
            $query = $db->prepare($sql);
            $query->bindValue(":id", $this->id);

            return $query->execute();
        } catch (\PDOException $e) {
            $message = sprintf("Error while deleting record from '%s' with ID '%s' ", static::TABLE_NAME, $this->id);
            throw new ModelException($message, $e);
        }
    }

}

class ModelException extends \Exception
{
    public function __construct($message, $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}