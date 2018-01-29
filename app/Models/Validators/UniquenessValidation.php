<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use \Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use \Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;

/**
*
*/
class UniquenessValidation extends AbstractValidation
{
	function __construct($object, $field, Array $options = null)
	{
		parent::__construct($object, $field, $options);
	}

	public function run()
	{
		return parent::runValidation(function(){
	        $uniquenessOptions = [
	            'table' => null,
	            'field' => null,
	            'caseSensitive' => null
	        ];

	        // set defaults if no options were given
	        if (array_key_exists('table', $this->options) == false) {
				$this->options['table'] = $this->object::TABLE_NAME;
	        }
	        if (array_key_exists('field', $this->options) == false) {
				$this->options['field'] = $this->field;
	        }
			if (array_key_exists('caseSensitive', $this->options) == false) {
				$this->options['caseSensitive'] = false;
	        }

	        // check that we got the right options
	        $optionsDiff = array_diff_key($uniquenessOptions, $this->options);

	        if (empty($optionsDiff) == false) {
	            $error = sprintf(
	                "Invalid options given for '%s' validation. Valid options are: '%s'",
	                $this->type,
	                implode(", ", ['table', 'field', 'caseSensitive'])
	            );
	            throw new InvalidArgumentException($error, 1);
	        }

	        $table = $this->options['table'];
	        $field = $this->options['field'];

	        $db = DatabaseManager::getDbConnection();


	        $idFilter = null;
	        $id = $this->object->id;
	        if ($id > 0) {
				$idFilter = "AND id != $id";
	        }

			$query = null;
            if ($this->options['caseSensitive'] == true) {
	            $query = sprintf(
	                "SELECT COUNT(%s) FROM %s WHERE %s = '%s' $idFilter",
	                $field,
	                $table,
	                $field,
	                $this->object->{$this->field}
	            );
            }
            else {
                $query = sprintf(
                    "SELECT COUNT(%s) FROM %s WHERE LOWER(%s) = '%s' $idFilter",
                    $field,
                    $table,
                    $field,
                    strtolower($this->object->{$this->field})
                );
	        }

	        $statement = $db->prepare($query);
	        $statement->execute();
	        $result = $statement->fetchColumn();

	        if ($result > 0) {
	            $this->errors = [sprintf("duplicate value found for '%s'", $this->field)];
	        }
		});
	}
}
