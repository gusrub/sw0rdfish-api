<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;
use Sw0rdfish\Helpers\I18n as I18n;

/**
 * Represents a validation that can be run on any model that checks that a field
 * contains a value that is included in a given list of values.
 */
class InclusionValidation extends AbstractValidation
{
    /**
     * Creates a new instance of an inclusion validator with the given
     * parameters.
     *
     * @param \Sw0rdfish\Models\BaseModel $object The object instance where the
     * validation will be run.
     * @param String $field The property name where the value that needs to
     * match the list is stored.
     * @param array $options The list of values to check against inclusion.
     */
    function __construct($object, $field, Array $options = null)
    {
        parent::__construct($object, $field, $options);
    }

    /**
     * Executes the inclusion validation.
     *
     * @return boolean Whether the validation succeeded or not.
     */
    public function run()
    {
        return parent::runValidation(function(){
            if (empty($this->options)) {
                $error = I18n::translate(
                    "Invalid options given for '{className}' validation, you must give an array with values to check against",
                    [
                        'className' => __CLASS__
                    ]
                );
                throw new InvalidArgumentException($error, 1);
            }

            $value = $this->object->{$this->field};
            if (in_array($value, $this->options) == false) {
                $this->errors = [I18n::translate("value '{value}' not included in [{options}]",
                    [
                        'value' => $value,
                        'options' => implode(', ', $this->options)
                    ]
                )];
            }
        });
    }
}
