<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use \Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;

/**
*
*/
class InclusionValidation extends AbstractValidation
{
	function __construct($object, $field, Array $options = null)
	{
		parent::__construct($object, $field, $options);
	}

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
