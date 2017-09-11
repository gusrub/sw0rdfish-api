<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use \Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;

/**
*
*/
class PresenceValidation extends AbstractValidation
{
	function __construct($object, $field, Array $options = null)
	{
		parent::__construct($object, $field, $options);
	}

	public function run()
	{
		return parent::runValidation(function(){
	        if (empty($this->object->{$this->field})) {
	            $this->errors = ["cannot be empty"];
	        }
		});
	}
}
