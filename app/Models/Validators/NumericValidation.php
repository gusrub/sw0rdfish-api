<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;
use Sw0rdfish\Helpers\I18n as I18n;

/**
 * Represents a validation that can be run on any model that checks that a field
 * value is a numeric value.
 */
class NumericValidation extends AbstractValidation
{
	/**
	 * Creates a new instance of a numeric validator with the given parameters.
	 *
	 * @param object $object The object instance where the validation will be
	 * run.
	 * @param string $field The property name where the value that needs be
	 * numeric is stored.
	 * @param array $options An array of options for the numericality which can
	 * be: any of `greaterThan`, `greaterThanOrEqual`, `lessThan` or
	 * `lessThanOrEqual`. Each of these options should have assigned the number
	 * to be used as comparison.
	 */
    function __construct($object, $field, Array $options = null)
    {
        parent::__construct($object, $field, $options);
    }

	/**
	 * Executes the numeric validation.
	 *
	 * @return boolean Whether the validation succeeded or not.
	 */
    public function run()
    {
        return parent::runValidation(function(){
            // we should only accept either greaterThan or greaterThanOrEqual and lessThan or lessThanOrEqual noth both:
            if (count(array_intersect_key($this->options, array_flip(["greaterThan", "greaterThanOrEqual"]))) > 1) {
                $error = I18n::translate(
                    "Invalid options given for '{className}' validation, you can choose either greaterThan or greaterThanOrEqual but not both",
                    [
                        'className' => __CLASS__
                    ]
                );
                throw new InvalidArgumentException($error, 1);
            }

            if (count(array_intersect_key($this->options, array_flip(["lessThan", "lessThanOrEqual"]))) > 1) {
                $error = I18n::translate(
                    "Invalid options given for '{className}' validation, you can choose either lessThan or lessThanOrEqual but not both",
                    [
                        'className' => __CLASS__
                    ]
                );
                throw new InvalidArgumentException($error, 1);
            }

            // check that the ranges makes sense
            $greaterThan = null;
            $greaterThanOrEqual = null;
            $lessThan = null;
            $lessThanOrEqual = null;
            if (array_key_exists('greaterThan', $this->options)) {
                $greaterThan = $this->options["greaterThan"];
            }
            if (array_key_exists('greaterThanOrEqual', $this->options)) {
                $greaterThanOrEqual = $this->options["greaterThanOrEqual"];
            }
            if (array_key_exists('lessThan', $this->options)) {
                $lessThan = $this->options["lessThan"];
            }
            if (array_key_exists('lessThanOrEqual', $this->options)) {
                $lessThanOrEqual = $this->options["lessThanOrEqual"];
            }

            if (isset($greaterThan) && isset($lessThan)) {
                if ($greaterThan >= $lessThan) {
                    $error = I18n::translate(
                        "Invalid options given for '{className}' validation: 'greaterThan' ({greaterThan}) cannot be greater or equal than 'lessThan' ({lessThan}) ",
                        [
                            'className' => __CLASS__,
                            'lessThan' => $lessThan,
                            'greaterThan' => $greaterThan
                        ]
                    );
                    throw new InvalidArgumentException($error, 1);
                }
            }

            if (isset($greaterThan) && isset($lessThanOrEqual)) {
                if ($greaterThan >= $lessThanOrEqual) {
                    $error = I18n::translate(
                        "Invalid options given for '{className}' validation: 'greaterThan' ({greaterThan}) cannot be greater than 'lessThanOrEqual' ({lessThanOrEqual}) ",
                        [
                            'className' => __CLASS__,
                            'lessThanOrEqual' => $lessThanOrEqual,
                            'greaterThan' => $greaterThan
                        ]
                    );
                    throw new InvalidArgumentException($error, 1);
                }
            }

            if (isset($greaterThanOrEqual) && isset($lessThan)) {
                if ($greaterThanOrEqual >= $lessThan) {
                    $error = I18n::translate(
                        "Invalid options given for '{className}' validation: 'greaterThanOrEqual' ({greaterThanOrEqual}) cannot be greater than 'lessThan' ({lessThan}) ",
                        [
                            'className' => __CLASS__,
                            'lessThan' => $lessThan,
                            'greaterThanOrEqual' => $greaterThanOrEqual
                        ]
                    );
                    throw new InvalidArgumentException($error, 1);
                }
            }

            if (isset($greaterThanOrEqual) && isset($lessThanOrEqual)) {
                if ($greaterThanOrEqual > $lessThanOrEqual) {
                    $error = I18n::translate(
                        "Invalid options given for '{className}' validation: 'greaterThanOrEqual' ({greaterThanOrEqual}) cannot be greater than 'lessThanOrEqual' ({lessThanOrEqual}) ",
                        [
                            'className' => __CLASS__,
                            'lessThanOrEqual' => $lessThanOrEqual,
                            'greaterThanOrEqual' => $greaterThanOrEqual
                        ]
                    );
                    throw new InvalidArgumentException($error, 1);
                }
            }

            // do the actual validation
            $value = $this->object->{$this->field};

            if(is_numeric($this->object->{$this->field}) == false) {
                $this->errors = [I18n::translate(
                    "value '{value}' is not a valid number",
                    [
                        'value' => $value
                    ]
                )];
                // we don't want to even compare values if what we received
                // is not a number, so lets go back
                return;
            }

            if (isset($greaterThan) && ($value <= $greaterThan)) {
                array_push(
                    $this->errors, I18n::translate(
                        "must be greater than '{greaterThan}' ",
                        [
                            'greaterThan' => $greaterThan
                        ]
                    )
                );
            }

            if (isset($greaterThanOrEqual) && ($value < $greaterThanOrEqual)) {
                array_push(
                    $this->errors, I18n::translate(
                        "must be greater than or equal to '{greaterThanOrEqual}' ",
                        [
                            'greaterThanOrEqual' => $greaterThanOrEqual
                        ]
                    )
                );
            }

            if (isset($lessThan) && ($value >= $lessThan)) {
                array_push(
                    $this->errors, I18n::translate(
                        "must be less than '{lessThan}' ",
                        [
                            'lessThan' => $lessThan
                        ]
                    )
                );
            }

            if (isset($lessThanOrEqual) && ($value > $lessThanOrEqual)) {
                array_push(
                    $this->errors, I18n::translate(
                        "must be less than or equal to '{lessThanOrEqual}' ",
                        [
                            'lessThanOrEqual' => $lessThanOrEqual
                        ]
                    )
                );
            }

        });
    }
}
