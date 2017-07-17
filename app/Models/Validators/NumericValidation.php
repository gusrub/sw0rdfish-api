<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use \Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;

/**
*
*/
class NumericValidation extends AbstractValidation
{
	function __construct($object, $field, Array $options = null)
	{
		parent::__construct($object, $field, $options);
	}

	public function run()
	{
		return parent::run(function(){
	        // we should only accept either greaterThan or greaterThanOrEqual and lessThan or lessThanOrEqual noth both:
	        if (count(array_intersect_key($this->options, array_flip(["greaterThan", "greaterThanOrEqual"]))) > 1) {
	            $error = sprintf(
	                "Invalid options given for '%s' validation, you can choose either greaterThan or greaterThanOrEqual but not both",
	                __CLASS__
	            );
	            throw new InvalidArgumentException($error, 1);
	        }

	        if (count(array_intersect_key($this->options, array_flip(["lessThan", "lessThanOrEqual"]))) > 1) {
	            $error = sprintf(
	                "Invalid options given for '%s' validation, you can choose either lessThan or lessThanOrEqual but not both",
	                __CLASS__
	            );
	            throw new InvalidArgumentException($error, 1);
	        }

	        // check that the ranges makes sense
	        $greaterThan = $this->options["greaterThan"];
	        $greaterThanOrEqual = $this->options["greaterThanOrEqual"];
	        $lessThan = $this->options["lessThan"];
	        $lessThanOrEqual = $this->options["lessThanOrEqual"];

	        if ($greaterThan && $lessThan) {
	        	if ($greaterThan >= $lessThan) {
		            $error = sprintf(
		                "Invalid options given for '%s' validation: '$greaterThan' cannot be greater or equal than '$lessThan' ",
		                __CLASS__
		            );
		            throw new InvalidArgumentException($error, 1);
	        	}
	        }

	        if ($greaterThan && $lessThanOrEqual) {
	        	if ($greaterThan >= $lessThanOrEqual) {
		            $error = sprintf(
		                "Invalid options given for '%s' validation: '$greaterThan' cannot be greater than '$lessThanOrEqual' ",
		                __CLASS__
		            );
		            throw new InvalidArgumentException($error, 1);
	        	}
	        }

	        if ($greaterThanOrEqual && $lessThan) {
	        	if ($greaterThanOrEqual >= $lessThan) {
		            $error = sprintf(
		                "Invalid options given for '%s' validation: '$greaterThanOrEqual' cannot be greater than '$lessThan' ",
		                __CLASS__
		            );
		            throw new InvalidArgumentException($error, 1);
	        	}
	        }

	        if ($greaterThanOrEqual && $lessThanOrEqual) {
	        	if ($greaterThanOrEqual > $lessThanOrEqual) {
		            $error = sprintf(
		                "Invalid options given for '%s' validation: '$greaterThanOrEqual' cannot be greater or equal than '$lessThanOrEqual' ",
		                __CLASS__
		            );
		            throw new InvalidArgumentException($error, 1);
	        	}
	        }

	        // do the actual validation
	        $value = $this->object->{$this->field};

	        if(is_numeric($this->object->{$this->field}) == false) {
	            $this->errors = [sprintf("value '%s' is not a valid number", $value)];
	            // we don't want to even compare values if what we received
	            // is not a number, so lets go back
	            return;
	        }

	        if ($greaterThan && ($value <= $greaterThan)) {
	        	array_push($this->errors, "must be greater than '$greaterThan' ");
	        }

	        if ($greaterThanOrEqual && ($value < $greaterThanOrEqual)) {
	        	array_push($this->errors, "must be greater than or equal to '$greaterThanOrEqual' ");
	        }

	        if ($lessThan && ($value >= $lessThan)) {
	        	array_push($this->errors, "must be less than '$lessThan' ");
	        }

	        if ($lessThanOrEqual && ($value > $lessThanOrEqual)) {
	        	array_push($this->errors, "must be less than or equal to '$lessThanOrEqual' ");
	        }

		});
	}
}
