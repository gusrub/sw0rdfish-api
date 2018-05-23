<?php

/**
 * @package Sw0rdfish\Models Contains classes that represent data models to
 *  interact with the database.
 */
namespace Sw0rdfish\Models;

use Sw0rdfish\Models\ModelException as ModelException;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\Validators\PresenceValidation as PresenceValidation;
use Sw0rdfish\Models\Validators\EmailValidation as EmailValidation;
use Sw0rdfish\Models\Validators\InclusionValidation as InclusionValidation;
use Sw0rdfish\Models\Validators\NumericValidation as NumericValidation;
use Sw0rdfish\Models\Validators\UniquenessValidation as UniquenessValidation;
use Sw0rdfish\Helpers\I18n as I18n;

/**
* Base class that all other models inherit from. This class contains generic
* functionality to execute CRUD (Create-Read-Update-Delete) operations as well
* as other basic functionality such as filtering, listing, etc.
*/
class BaseModel
{

    /**
     * @var string int The ID for this record.
     */
    public $id;

    /**
     * @var string Date of the creation of this record.
     */
    public $createdDate;

    /**
     * @var string Date of the creation of this record.
     */
    public $updatedDate;

    /**
     * @var Array A list of validation errors for this model.
     */
    private $validationErrors = [];

    /**
     * Defines the allowed keywords to do sorting.
     */
    const SORT_KEYWORDS = ["ASC", "DESC"];

    /**
     * Creates a new instance for the model with the given arguments. Please
     * note that not all properties are going to be assigned, only those which
     * are public, for security reasons.
     *
     * @param Array $args An array with key-value pairs for the properties to
     * initialize this object with.
     * @return BaseModel A new instance of this object.
     */
    public function __construct(Array $args = null)
    {
        if($args !== null) {
            $this->assignAttributes($args);
        }
    }

    /**
     * Generates a merged array of bound parameters formatted do be used by the
     * PDO class.
     *
     * @param Array $args An array containing key-value pairs of the parameters.
     * @return Array An array with formatted values to be used by PDO.
     */
    protected static function generateBoundParams(Array $args)
    {
        $paramKeys = array_keys($args);
        $paramValues = array_values($args);
        array_walk($paramKeys, function(&$key){
            $key = ":$key";
        });

        return array_combine($paramKeys, $paramValues);
    }

    /**
     * Generates a string with the order statement by using the indicated field
     * if its exists or a default field if it does not.
     *
     * @param Array $args An array with parameters to do the ordering.
     * @return string The order statement string.
     */
    protected static function allowedOrderField(Array $args = null)
    {
        $orderField = sprintf("%s.id", static::TABLE_NAME);

        if (isset($args) && array_key_exists("orderBy", $args)) {
            if (property_exists(static::class, $args["orderBy"])
                && strtolower($args['orderBy']) != 'id') {
                $orderField = $args["orderBy"];
            }
        }

        return $orderField;
    }

    /**
     * Generates a string with the sort direction statement by using the
     * indicated sort keyword if its allowed or a default field if it does not.
     *
     * @param Array $args An array with parameters to do the sorting.
     * @return string The sort statement string.
     */
    protected static function allowedSortDirection(Array $args = null)
    {
        if (isset($args) && array_key_exists("sort", $args)) {
            if (in_array(strtoupper($args["sort"]), self::SORT_KEYWORDS)) {
                return $args["sort"];
            }
        }

        return self::SORT_KEYWORDS[0];
    }

    /**
     * Generates a string with the limit and offset (paging) by using the
     * passed options. If none are given `null` will be returned meaning we are
     * doing no pagination.
     *
     * @param Array $args An array with parameters to do the paging.
     * @return string|null The paging statement string.
     */
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

    /**
     * Generates a string with the conditions that will be used for filtering
     * records. If no conditions are given `null` will be returned. Two types of
     * filters can be passed as arguments in the array of options: `where` which
     * will match exactly the value of `like` which will match parts of it.
     *
     * @param Array $args An array with parameters to do the filtering.
     * @return string|null The filtering criteria statement string.
     */
    protected static function generateConditions(Array $args = null)
    {
        if (isset($args)) {
            $fields = [];

            if(array_key_exists('where', $args)) {
                foreach ($args['where'] as $key => $value) {
                    if (property_exists(static::class, $key)) {
                        $field = $key == "id" ? sprintf("%s.%s", static::TABLE_NAME, $key) : $key;
                        array_push($fields, sprintf("%s=:%s", $field, $key));
                    } else {
                        throw new ModelException(
                            I18n::translate("Invalid filter: '{key}' is not a valid field",
                                [
                                    'key' => $key
                                ]
                            )
                        );
                    }
                }
            }
            if (array_key_exists('like', $args)) {
                foreach ($args['like'] as $key => $value) {
                    if (property_exists(static::class, $key)) {
                        $field = $key == "id" ? sprintf("%s.%s", static::TABLE_NAME, $key) : $key;
                        array_push($fields, sprintf("%s LIKE :%s", $field, $key));
                    } else {
                        throw new ModelException(
                            I18n::translate("Invalid filter: '{key}' is not a valid field",
                                [
                                    'key' => $key
                                ]
                            )
                        );
                    }
                }
            }

            if (count($fields) > 0) {
                return sprintf("WHERE (%s)", implode(", ", $fields));
            }
        }

        return null;
    }

    /**
     * Generates an array with the names of the columns that will be used on the
     * operation by matchin them with the property names passed in the
     * arguments. If the class has inheritance it will also generated the column
     * names for the parent class or model.
     *
     * @param Array $args An array with names of the properties.
     * @return Array An array containing all the targets for the operation.
     */
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

    /**
     * Gets a friendly name for the model.
     *
     * @return string A friendly name for the model.
     */
    protected static function getShortName()
    {
        return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', (new \ReflectionClass(new static))->getShortName()));
    }

    /**
     * Executes all validations defined in the model. Will throw a
     * \ValidationException whenever any of them fail.
     *
     * @param object $obj The object where the validations need to be run
     * against.
     * @return null
     */
    private static function runValidations($obj)
    {
        if ($obj->valid() == false) {
            $message = I18n::translate(
                "{shortName} object has some invalid data",
                [
                    'shortName' => self::getShortName()
                ]

            );
            $errors = $obj->getValidationErrors();
            throw new ValidationException($message, $errors);
        }
    }

    /**
     * Checks the given arguments for an operation and sanitizes it based on
     * whether the given properties are public or not. It returnes only those
     * which are considered safe.
     *
     * @param Array $args An array with the input for the operation.
     * @return Array A sanitized or cleaned-up array with key-value pairs of
     * properties which are allowed to be used.
     */
    private static function sanitizeInput(Array $args)
    {
        $sanitizedInput = [];

        foreach ($args as $key => $value) {
            if (property_exists(static::class, $key)) {
                $property = new \ReflectionProperty(static::class, $key);
                if ($property->isPublic()) {
                    $sanitizedInput["$key"] = $value;
                }
            } elseif (property_exists(self::class, $key)) {
                $property = new \ReflectionProperty(self::class, $key);
                if ($property->isPublic()) {
                    $sanitizedInput["$key"] = $value;
                }
            }
        }

        return $sanitizedInput;
    }

    /**
     * Assigns the received input from user arguments to the object properties.
     * Only those properties which are public can be set, anything else will
     * cause a \ModelException.
     *
     * @param Array $args An array with key-value pairs of the properties to be
     * set
     * @return null
     */
    private function assignAttributes(Array $args)
    {
        foreach ($args as $key => $value) {
            if (property_exists(static::class, $key)) {
                $property = new \ReflectionProperty($this, $key);
                if ($property->isPublic()) {
                    $this->{$key} = $value;
                }
            } elseif (property_exists(self::class, $key)) {
                $property = new \ReflectionProperty(self::class, $key);
                if ($property->isPublic()) {
                    $this->{$key} = $value;
                }
            }
            else {
                throw new ModelException(
                    I18n::translate(
                        "Property '{key}' does not exist in '{className}' ",
                        [
                            'key' => $key,
                            'className' => static::class
                        ]
                    )
                );
            }
        }
    }

    /**
     * Runs a single validation for a model.
     *
     * @param string $field The field or property of this model that will be
     * validated.
     * @param Array $validators An array containing the different validations
     * and their options to be run against the designated field.
     * @return null
     */
    private function runSingleValidation($field, $validators)
    {
        foreach ($validators as $key => $value ) {
            $type = null;
            $options = null;

            // check whether this validation has/supports options
            if (is_numeric($key)) {
                $type = $value;
            } else {
                $type = $key;
                $options = $value;
            }

            switch ($type) {
                case 'presence':
                    $validation = new PresenceValidation($this, $field, $options);
                    break;
                case 'email':
                    $validation = new EmailValidation($this, $field, $options);
                    break;
                case 'uniqueness':
                    $validation = new UniquenessValidation($this, $field, $options);
                    break;
                case 'inclusion':
                    $validation = new InclusionValidation($this, $field, $options);
                    break;
                case 'numeric':
                    $validation = new NumericValidation($this, $field, $options);
                    break;
                default:
                    throw new InvalidArgumentException(
                        I18n::translate(
                            "No '{type}' validation exists ",
                            [
                                'type' => $type
                            ]
                        ),
                        1
                    );
            }

            if ($validation->run() == false) {
                $this->validationErrors[$field][$type] = $validation->getErrors();
            }
        }
    }

    /**
     * Checks whether this model is valid.
     *
     * @return boolean Whether this model is valid or not.
     */
    public function valid()
    {
        // check both base class (if any) and child class validations
        $validations = [];
        $parentClass = get_parent_class(static::class);
        if (defined(sprintf("%s::VALIDATIONS", static::class))) {
            $validations = static::VALIDATIONS;
        }
        if ($parentClass) {
            if (defined(sprintf("%s::VALIDATIONS", $parentClass))) {
                $validations = array_merge($validations, $parentClass::VALIDATIONS);
            }
        }

        foreach ($validations as $field => $validators) {
            $this->runSingleValidation($field, $validators);
        }

        return empty($this->validationErrors);
    }

    /**
     * Gets the validation error messages for this model.
     *
     * @return Array The validation error messages.
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    /**
     * Returns the total pages that this model has based on the
     * `MAX_RECORDS_PER_PAGE` environment variable.
     *
     * @return int The number of pages that this model has
     */
    public static function pages()
    {
        try {
            $db = DatabaseManager::getDbConnection();

            $query = null;
            if (defined(sprintf("%s::BASE_TABLE_NAME", static::class))) {
                $query = sprintf(
                    "SELECT COUNT(*) FROM %s INNER JOIN %s ON %s.id=%s.id;",
                    static::TABLE_NAME,
                    static::BASE_TABLE_NAME,
                    static::TABLE_NAME,
                    static::BASE_TABLE_NAME
                );
            } else {
                $query = sprintf(
                    "SELECT COUNT(*) FROM %s;",
                    static::TABLE_NAME
                );
            }

            $statement = $db->prepare($query);
            $statement->execute();
            $recordCount = $statement->fetchColumn();
            $result = ceil($recordCount / getenv("MAX_RECORDS_PER_PAGE"));

            return $result;
        } catch (\PDOException $e) {
            $message = I18n::translate(
                "Error while counting records from '{tableName}' ",
                [
                    'tableName' => static::TABLE_NAME
                ]
            );
            throw new ModelException($message, $e);
        }
    }

    /**
     * Lists the records for this model. If no arguments are given it will
     * return all the records from the first page. An array with options can be
     * passed with the folllowing:
     *
     * `orderBy` - A string with a name of a property that will be used for sorting
     * `sort` - The direction of the ordering for the field.
     * `page` - The number of page of the pagination.
     * `conditions` - An array of where/like conditions to be used to filter.
     *
     * Any of the above options can be combined.
     *
     * @param Array $args The options to do the listing.
     * @return Array An array of objects of the model if any match the criteria.
     */
    public static function all(Array $args = null)
    {
        try {
            // default options
            $orderBy = self::allowedOrderField($args);
            $sort = self::allowedSortDirection($args);
            $paginate = self::generateLimitAndOffset($args);
            $conditions = self::generateConditions($args);

            $db = DatabaseManager::getDbConnection();
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

            if (isset($conditions) && array_key_exists('where', $args)) {
                foreach ($args['where'] as $field => $value) {
                    $statement->bindValue(sprintf(":$field"), "$value");
                }
            }
            if (isset($conditions) && array_key_exists('like', $args)) {
                foreach ($args['like'] as $field => $value) {
                    $statement->bindValue(sprintf(":$field"), "%$value%");
                }
            }

            $statement->execute();
            $resultSet = $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
            return $resultSet;
        } catch (\PDOException $e) {
            $message = I18n::translate(
                "Error while loading records from '{tableName}' ",
                [
                    'tableName' => static::TABLE_NAME
                ]
            );
            throw new ModelException($message, $e);
        }
    }

    /**
     * Gets a single record from the database for this model by the given ID.
     *
     * @param int $id The ID of the record to retrieve.
     * @return \BaseModel A record representing this model.
     */
    public static function get($id)
    {
        try {
            $db = DatabaseManager::getDbConnection();

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
            $message = I18n::translate(
                "Error while getting record from '{tableName}' with ID '{id}' ",
                [
                    'tableName' => static::TABLE_NAME,
                    'id' => $id
                ]
            );
            throw new ModelException($message, $e);
        }
    }

    /**
     * Creates a new record of this model type with the given arguments. All
     * model validations will be executed before creation, if the model is
     * invalid then creation will fail.
     *
     * @param Array $args An array of key-value pairs representing the
     * properties to set upon creation.
     * @return \BaseModel the newly created record.
     */
    public static function create(Array $args)
    {
        try {
            // sanitize the input so we don't get protected or private values
            // overwritten
            $args = static::sanitizeInput($args);

            // create instance so we check for valid properties first
            $obj = new static($args);

            // run validations
            self::runValidations($obj);

            $db = DatabaseManager::getDbConnection();

            // set creation date
            $args["createdDate"] = date('Y-m-d H:i:s');
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
            $message = I18n::translate(
                "Error while creating record in '{tableName}' ",
                [
                    'tableName' => static::TABLE_NAME
                ]
            );
            throw new ModelException($message, $e);
        }
    }

    /**
     * Updates an existing record of this model type with the given arguments.
     * All model validations will be executed before update, if the model is
     * invalid then update will fail.
     *
     * @param int $id The ID of the record to be updated.
     * @param Array $args An array of key-value pairs representing the
     * properties to set when updating.
     * @return \BaseModel the updated record.
     */
    public static function update($id, Array $args)
    {
        try {
            // set the ID that we got from the first argument
            $args['id'] = $id;

            // sanitize the input so we don't get protected or private values
            // overwritten
            $args = static::sanitizeInput($args);

            // create instance so we check for valid properties first
            $obj = static::get($id);
            foreach ($args as $key => $value) {
                $obj->{$key} = $value;
            }

            // run validations
            self::runValidations($obj);

            $db = DatabaseManager::getDbConnection();

            // Set the update date and unset the ID if any
            $args["updatedDate"] = date('Y-m-d H:i:s');
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
            $message = I18n::translate(
                "Error while updating record in '{tableName}' with ID '{id}' ",
                [
                    'tableName' => static::TABLE_NAME,
                    'id' => $id
                ]
            );
            throw new ModelException($message, $e);
        }
    }

    /**
     * Returns whether this model instance is persisted or not.
     *
     * @return boolean Whether this instance is persisted or not.
     */
    public function isNew()
    {
        return $this->id <= 0;
    }

    /**
     * Saves the current model instance to the database. If the record is new
     * then it will be created, otherwise updated. This method will run all the
     * model validations before save.
     *
     * @return boolean Whether model could be saved or not.
     */
    public function save()
    {
        $args = get_object_vars($this);
        $savedObject = $this->isNew() ? static::create($args) : static::update($this->id, $args);
        $this->assignAttributes(get_object_vars($savedObject));

        return true;
    }

    /**
     * Deletes this object from the database.
     */
    public function delete()
    {
        try {
            $db = DatabaseManager::getDbConnection();

            $query = null;
            if (defined(sprintf("%s::BASE_TABLE_NAME", static::class))) {
                $query = sprintf("DELETE FROM %s WHERE id=:id;", static::BASE_TABLE_NAME);
            } else {
                $query = sprintf("DELETE FROM %s WHERE id=:id;", static::TABLE_NAME);
            }

            $statement = $db->prepare($query);
            $statement->bindValue(":id", $this->id);

            return $statement->execute();
        } catch (\PDOException $e) {
            $message = I18n::translate(
                "Error while deleting record from '{tableName}' with ID '{id}' ",
                [
                'tableName' => static::TABLE_NAME,
                'id' => $this->id
                ]
            );
            throw new ModelException($message, $e);
        }
    }
}
