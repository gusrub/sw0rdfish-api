<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use Sw0rdfish\Helpers\I18n as I18n;

/**
 * Represents a base validation that consists of necessary behavior for other
 * classes wanting to implement specific validations.
 *
 * Each of these validations will be run against a property of the object that
 * implements them.
 */
abstract class AbstractValidation
{

    /**
     * @var object The object where the validation will be run.
     */
    public $object;

    /**
     * @var string The property name that needs to be validated.
     */
    public $field;

    /**
     * @var Array An array containing all the validations and their options for
     * the field. Note that a single field may contain many validations. Check
     * the constructor for each specific implementation details.
     */
    public $options = [];

    /**
     * @var Array Contains an array of strings that holds any validation error
     * messages.
     */
    protected $errors = [];

    /**
     * Creates a new instance of a validator.
     *
     * @param object $object The object where the validation will be run.
     * @param string $field The property name that needs to be validated.
     * @param Array $options An array containing all the validations and their
     * options for the field. Note that a single field may contain many
     * validations.
     * @return AbstractValidation The validation object.
     */
    function __construct($object, $field, Array $options = null)
    {
        $this->object = $object;
        $this->field = $field;

        if (isset($options)) {
            $this->options = $options;
        }
    }

    /**
     * Returns an array of error messages if the validation failed.
     *
     * @return Array A list of error messages.
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Validates that the property defined in $field actually exists in the
     * object. If the property does not exist will throw an
     * \InvalidArgumentException.
     *
     * @return null
     */
    private function validateField()
    {
        $objectClass = get_class($this->object);
        $objectVars = get_class_vars($objectClass);

        if (array_key_exists($this->field, $objectVars) == false) {
            $error = I18n::translate(
                "Object of type '{objectClass}' has no '{field}' property ",
                [
                    'objectClass' => $objectClass,
                    'field' => $this->field
                ]
            );
            throw new InvalidArgumentException($error, 1);
        }
    }

    /**
     * Validates that the instance in $object is not null. Will throw an
     * \InvalidArgumentException if it does not.
     *
     * @return null
     */
    private function validateObject()
    {
        if (is_null($this->object)) {
            $error = I18n::translate(
                'Expecting an instance of an object to validate against but none was given'
            );
            throw new InvalidArgumentException($error, 1);
        }
    }

    /**
     * Executes the actual validation on the object for the property defined.
     * Note that this is just a caller method that needs to have the actual
     * validation code injected and within that code the $errors variable
     * should be filled in if there were any errors.
     *
     * @param callable $validationCode A callable function or object that runs
     * the specific validation code.
     * @return boolean Whether the validation failed or succeeded.
     */
    protected function runValidation(callable $validationCode)
    {
        // check that the object has something
        $this->validateObject();

        // check that the instance has that field available
        $this->validateField();

        call_user_func($validationCode);

        return empty($this->errors);
    }
}