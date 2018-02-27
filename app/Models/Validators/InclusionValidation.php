<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use \Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;

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
	 * @param object $object The object instance where the validation will be
	 * run.
	 * @param string $field The property name where the value that needs to
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
	            $error = sprintf(
	                "Invalid options given for '%s' validation, you must give an array with values to check against",
	                __CLASS__
	            );
	            throw new InvalidArgumentException($error, 1);
	        }

	        $value = $this->object->{$this->field};
	        if (in_array($value, $this->options) == false) {
	            $this->errors = [sprintf("value '%s' not included in [%s]",
	                $value,
	                implode(', ', $this->options)
	            )];
	        }
		});
	}
}
