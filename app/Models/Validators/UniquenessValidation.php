<?php

namespace Sw0rdfish\Models\Validators;

use \InvalidArgumentException as InvalidArgumentException;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\Validators\AbstractValidation as AbstractValidation;
use Sw0rdfish\Helpers\I18n as I18n;

/**
 * Represents a validation that can be run on any model that checks that a field
 * has a unique value on the database. This validation supports a set of options
 * to check against a certain table or a scoped value.
 */
class UniquenessValidation extends AbstractValidation
{

	/**
	 * Creates a new instance of a uniqueness validator with the given
	 * parameters.
	 *
	 * @param object $object The object instance where the validation will be
	 * run.
	 * @param string $field The property name that must have a unique value.
	 * @param array $options An array of options to configure the criteria of
	 * the uniqueness match. Supported options are `table` for the table that
	 * should be checked against, `field` to define which field stores the value
	 * if its not the same name as the property and finally `scope` to use an
	 * dditional field as criteria, that is, uniqueness is scoped also to that
	 * field or key combination.
	 */
    function __construct($object, $field, Array $options = null)
    {
        parent::__construct($object, $field, $options);
    }

	/**
	 * Executes the uniqueness validation.
	 *
	 * @return boolean Whether the validation succeeded or not.
	 */
    public function run()
    {
        return parent::runValidation(function(){
            $uniquenessOptions = [
                'table' => null,
                'field' => null,
                'scope' => null
            ];

            // set defaults if no options were given
            if (array_key_exists('table', $this->options) == false) {
                $this->options['table'] = $this->object::TABLE_NAME;
            }
            if (array_key_exists('field', $this->options) == false) {
                $this->options['field'] = $this->field;
            }
            if (array_key_exists('scope', $this->options) == false) {
                $this->options['scope'] = null;
            }

            // check that we got the right options
            $optionsDiff = array_diff_key($this->options, $uniquenessOptions);
            if (empty($optionsDiff) == false) {
                $error = I18n::translate(
                    "Invalid options given for '{className}' validation. Valid options are: '{validations}' ",
                    [
                        'className' => __CLASS__,
                        'validations' => implode(", ", ['table', 'field', 'scope'])
                    ]
                );
                throw new InvalidArgumentException($error, 1);
            }

            $table = $this->options['table'];
            $field = $this->options['field'];

            $db = DatabaseManager::getDbConnection();


            $idFilter = null;
            $id = $this->object->id;
            if ($id > 0) {
                $idFilter = "AND id != '$id'";
            }

            $scopeFilter = null;
            $scope = $this->options['scope'];
            if ($scope) {
                $scopeValue = $this->object->{$scope};

                if (empty($scopeValue)) {
                    $this->errors = [
                        I18n::translate("field '{field}' is scoped to '{scope}' but it is empty",
                            [
                                'field' => $this->field,
                                'scope' => $scope
                            ]
                        )
                    ];
                    return false;
                }
                $scopeFilter = "AND $scope = '$scopeValue'";
            }

            $query = null;
            $query = sprintf(
                "SELECT COUNT(%s) FROM %s WHERE LOWER(%s) = '%s' $idFilter $scopeFilter",
                $field,
                $table,
                $field,
                strtolower($this->object->{$this->field})
            );

            $statement = $db->prepare($query);
            $statement->execute();
            $result = $statement->fetchColumn();

            if ($result > 0) {
                $this->errors = [
                    I18n::translate("duplicate value found for '{field}'",
                        [
                            'field' => $this->field
                        ]
                    )
                ];
            }
        });
    }
}
