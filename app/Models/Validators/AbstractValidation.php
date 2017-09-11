<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;

/**
 *
 */
abstract class AbstractValidation
{

    public $object;
    public $field;
    public $options = [];

    protected $errors = [];

    function __construct($object, $field, Array $options = null)
    {
        $this->object = $object;
        $this->field = $field;

        if (isset($options)) {
            $this->options = $options;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function validateField()
    {
        $objectClass = get_class($this->object);
        $objectVars = get_class_vars($objectClass);

        if (array_key_exists($this->field, $objectVars) == false) {
            $error = sprintf(
                "Object of type '%s' has no '%s' property",
                $objectClass,
                $this->field
            );
            throw new InvalidArgumentException($error, 1);
        }
    }

    private function validateObject()
    {
        if (is_null($this->object)) {
            throw new InvalidArgumentException("Expecting an instance of an object to validate against but none was given", 1);
        }
    }

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