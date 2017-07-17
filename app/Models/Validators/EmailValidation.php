<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use \Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;

/**
*
*/
class EmailValidation extends AbstractValidation
{
	function __construct($object, $field, Array $options = null)
	{
		parent::__construct($object, $field, $options);
	}

	public function run()
	{
		return parent::run(function(){
	        $email = filter_var($this->object->{$this->field}, FILTER_VALIDATE_EMAIL);

	        if (empty($email)) {
	            $this->errors = ["invalid email"];
	        }
		});
	}
}
