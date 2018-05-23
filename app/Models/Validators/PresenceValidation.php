<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;
use Sw0rdfish\Helpers\I18n as I18n;

/**
 * Represents a validation that can be run on any model that checks that a field
 * has a value set.
 */
class PresenceValidation extends AbstractValidation
{

    /**
     * Creates a new instance of a presence validator with the given parameters.
     *
     * @param object $object The object instance where the validation will be
     * run.
     * @param string $field The property name that must have a value.
     * @param array $options Any additional options for this validator.
     */
    function __construct($object, $field, Array $options = null)
    {
        parent::__construct($object, $field, $options);
    }

    /**
     * Executes the presence validation.
     *
     * @return boolean Whether the validation succeeded or not.
     */
    public function run()
    {
        return parent::runValidation(function(){
            if (empty($this->object->{$this->field})) {
                $this->errors = [
                    I18n::translate('cannot be empty')
                ];
            }
        });
    }
}
