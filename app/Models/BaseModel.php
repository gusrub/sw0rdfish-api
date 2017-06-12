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
    private $validationErrors = [];

    const SORT_KEYWORDS = ["ASC", "DESC"];

    public function __construct(Array $args = null)
    {
        if($args !== null) {
            $this->assignAttributes($args);
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

    protected static function allowedOrderField(Array $args = null)
    {
        if (isset($args) && array_key_exists("orderBy", $args)) {
            if (property_exists(static::class, $args["orderBy"])) {
                return $args["orderBy"];
            }
        }

        return sprintf("%s.id", static::TABLE_NAME);
    }

    protected static function allowedSortDirection(Array $args = null)
    {
        if (isset($args) && array_key_exists("sort", $args)) {
            if (in_array(strtolower($args["sort"]), self::SORT_KEYWORDS)) {
                return $args["sort"];
            }
        }

        return self::SORT_KEYWORDS[0];
    }

    protected static function generateLimitAndOffset(Array $args = null)
    {
        if (isset($args) && array_key_exists("page", $args)) {
            $page = $args["page"];
            $limit = getenv("MAX_RECORDS_PER_PAGE");
            $offset = ($limit * $page)-$limit;
            return sprintf("LIMIT %s OFFSET %s", $limit, $offset);
        }

        return null;
    }

    protected static function generateConditions(Array $args = null)
    {
        if (isset($args) && array_key_exists("where", $args)) {
            $fields = [];

            foreach ($args["where"] as $key => $value) {
                if (property_exists(static::class, $key)) {
                    $field = $key == "id" ? sprintf("%s.%s", static::TABLE_NAME, $key) : $key;
                    array_push($fields, sprintf("%s=:%s", $field, $key));
                } else {
                    throw new ModelException(sprintf("Invalid filter: '%s' is not a valid field", $key));
                }
            }

            return sprintf("WHERE (%s)", implode(", ", $fields));
        }

        return null;
    }

    protected static function getTableTargets(Array $args)
    {
        $targets = [];

        if (defined(sprintf("%s::BASE_TABLE_NAME", static::class))) {
            $targets[static::BASE_TABLE_NAME] = array_intersect_key($args, (array)get_class_vars(get_parent_class(new static)));
            $targets[static::TABLE_NAME] = array_diff_key(
                array_intersect_key(
                    $args,
                    (array)get_class_vars(static::class)
                ),
                $targets[static::BASE_TABLE_NAME]
            );
        } else {
            $targets[static::TABLE_NAME] = array_intersect_key($args, (array)get_class_vars(static::class));
        }
        return $targets;
    }

    protected static function getShortName()
    {
        return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', (new \ReflectionClass(new static))->getShortName()));
    }

    private function runValidations(Array $args)
    {
        $obj = new static($args);
        if ($obj->valid() == false) {
            $message = sprintf("%s object has some invalid data", self::getShortName());
            $errors = $obj->getValidationErrors();
            throw new ValidationException($message);
        }
    }

    private function assignAttributes(Array $args)
    {
        foreach ($args as $key => $value) {
            if (property_exists(static::class, $key)) {
                $this->{$key} = $value;
            } else {
                throw new ModelException(sprintf("Property '%s' does not exist in '%s'", $key, static::class));
            }
        }
    }

    public function valid()
    {
        if (defined(sprintf("%s::VALIDATIONS", static::class))) {
            foreach (static::VALIDATIONS as $field => $validations) {
                // TODO: Implement more customizable validations
                // foreach ($validations as $type => $options) {
                //     $validation = new Validation($field, $type, $options);

                //     if ($validation->run() == false) {
                //         $this->validationErrors[$field][$type] = $validation->errorMessage();
                //     }
                // }
                if (empty($this->{$field})) {
                    $this->validationErrors[$field] = "Cannot be empty";
                }
            }
        }
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    public static function all(Array $args = null)
    {
        try {
            // default options
            $orderBy = self::allowedOrderField($args);
            $sort = self::allowedSortDirection($args);
            $paginate = self::generateLimitAndOffset($args);
            $conditions = self::generateConditions($args);

            $db = self::getDbConnection();
            $query = null;
            if (defined(sprintf("%s::BASE_TABLE_NAME", static::class))) {
                $query = sprintf(
                    "SELECT * FROM %s INNER JOIN %s ON %s.id = %s.id %s ORDER BY %s %s %s;",
                    static::TABLE_NAME,
                    static::BASE_TABLE_NAME,
                    static::TABLE_NAME,
                    static::BASE_TABLE_NAME,
                    $conditions,
                    $orderBy,
                    $sort,
                    $paginate
                );
            } else {
                $query = sprintf(
                    "SELECT * FROM %s %s ORDER BY %s %s %s;",
                    static::TABLE_NAME,
                    $conditions,
                    $orderBy,
                    $sort,
                    $paginate
                );
            }
            $statement = $db->prepare($query);

            if (isset($conditions)) {
                foreach ($args["where"] as $field => $value) {
                    $statement->bindValue(sprintf(":$field"), $value);
                }
            }

            $statement->execute();
            $resultSet = $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
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

            $query = null;
            if (defined(sprintf("%s::BASE_TABLE_NAME", static::class))) {
                $query = sprintf(
                    "SELECT * FROM %s INNER JOIN %s ON %s.id=%s.id WHERE %s.id=:id",
                    static::TABLE_NAME,
                    static::BASE_TABLE_NAME,
                    static::TABLE_NAME,
                    static::BASE_TABLE_NAME,
                    static::BASE_TABLE_NAME
                );
            } else {
                $query = sprintf(
                    "SELECT * FROM %s WHERE id=:id",
                    static::TABLE_NAME
                );
            }

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

            // set creation date
            $args["createdDate"] = date("c");
            unset($args["updatedDate"]);

            // define actual target tables for multi-insert
            $targets = self::getTableTargets($args);

            // set a placeholder for the base insertion ID which is the base for the other insert(s)
            $baseId = null;

            foreach ($targets as $table => $tableArgs) {
                // if there was an insertion before, set the ID for the next one as the parent
                if(isset($baseId)) {
                    $tableArgs["id"] = $baseId;
                }

                // generate the field name list and the bound params array for PDO
                $fields = implode(", ", array_keys($tableArgs));
                $params = self::generateBoundParams($tableArgs);

                $query = sprintf("INSERT INTO %s (%s) VALUES (%s);", $table, $fields, implode(", ", array_keys($params)));
                $statement = $db->prepare($query);

                // bind each value safely
                foreach ($params as $param => $value) {
                    $statement->bindValue($param, $value);
                }

                // execute the query
                $statement->execute();

                // set the baseId only once since further inserts won't generate INSERT ID's
                if (is_null($baseId)) {
                    $baseId = $db->lastInsertId();
                }
                $args["id"] = $baseId;
            }

            return static::get($baseId);

        } catch (\PDOException $e) {
            $message = sprintf("Error while creating record in '%s'", static::TABLE_NAME);
            throw new ModelException($message, $e);
        }
    }

    public static function update($id, Array $args)
    {
        try {
            // run validations
            self::runValidations($args);

            $db = self::getDbConnection();

            // Set the update date and unser the ID if any
            $args["updatedDate"] = date("c");
            unset($args["id"]);
            unset($args["createdDate"]);

            // define actual target tables for multi-insert
            $targets = self::getTableTargets($args);

            foreach ($targets as $table => $tableArgs) {
                // generate the field name list and the bound params array for PDO
                $fields = array_keys($tableArgs);
                $params = self::generateBoundParams(array_merge($tableArgs, ["id"=>$id]));
                array_walk($fields, function(&$key){
                    $key = "$key=:$key";
                });
                $fields = implode(", ", $fields);
                $query = sprintf("UPDATE %s SET %s WHERE id=:id;", $table, $fields);
                $statement = $db->prepare($query);

                // bind each value safely
                foreach ($params as $param => $value) {
                    $statement->bindValue($param, $value);
                }

                // execute the query
                $statement->execute();

            }

            // set the ID of the object before instantiation
            $args["id"] = $id;

            return static::get($id);

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
        $this->assignAttributes((array)$savedObject);

        return true;
    }

    public function delete()
    {
        try {
            $db = self::getDbConnection();

            $query = null;
            if (defined(sprintf("%s::BASE_TABLE_NAME", static::class))) {
                $query = sprintf("DELETE FROM %s WHERE id=:id", static::BASE_TABLE_NAME);
            } else {
                $query = sprintf("DELETE FROM %s WHERE id=:id", static::TABLE_NAME);
            }

            $statement = $db->prepare($query);
            $statement->bindValue(":id", $this->id);

            return $statement->execute();
        } catch (\PDOException $e) {
            $message = sprintf("Error while deleting record from '%s' with ID '%s' ", static::TABLE_NAME, $this->id);
            throw new ModelException($message, $e);
        }
    }

}
