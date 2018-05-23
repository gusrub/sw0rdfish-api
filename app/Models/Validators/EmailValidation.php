<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;
use Sw0rdfish\Helpers\I18n as I18n;

/**
 * Represents a validation that can be run on any model that checks that a field
 * is a valid email address.
 */
class EmailValidation extends AbstractValidation
{
	/**
	 * Creates a new instance of an email validator with the given parameters.
	 *
	 * @param object $object The object instance where the validation will be
	 * run.
	 * @param string $field The property name where the email address is stored.
	 * @param array $options Any additional options for this validator.
	 */
	function __construct($object, $field, Array $options = null)
	{
		parent::__construct($object, $field, $options);
	}

	/**
	 * Executes the email validation.
	 *
	 * @return boolean Whether the validation succeeded or not.
	 */
	public function run()
	{
		return parent::runValidation(function(){
	        $email = filter_var($this->object->{$this->field}, FILTER_VALIDATE_EMAIL);

	        if (empty($email)) {
	            $this->errors = [I18n::translate('invalid email')];
	        }
		});
	}
}
