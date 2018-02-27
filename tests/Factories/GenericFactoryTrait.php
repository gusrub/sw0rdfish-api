<?php

/**
 * @package Tests\Factories Contains classes that represent factories of data
 * models.
 */
namespace Tests\Factories;

/**
 * Contains generic methods to be used by all model factories
 */
trait GenericFactoryTrait {

    /**
     * Builds a new factory for the model with the given arguments. If no
     * arguments are given it will generate random data. The instance returned
     * is not persisted but just initialized.
     *
     * @param Array $args An array with key-value pairs of the properties and
     * values to initialize this factory. Defaults to `null`.
     * @param boolean $sequence Whether to generated unique values for fields
     * which must have uniqueness. Defaults to false.
     * @return object a New factory of the model type.
     */
    public static function build(Array $args = null, $sequence = null)
    {
        $model = self::MODEL_NAME;
        $obj = new $model(self::generateArguments($args, $sequence));
        return $obj;
    }

    /**
     * Creates a new factory for the model with the given arguments. If no
     * arguments are given it will generate random data. The instance created is
     * actually persisted in the database.
     *
     * @param Array $args An array with key-value pairs of the properties and
     * values to create this factory. Defaults to `null`.
     * @param boolean $sequence Whether to generated unique values for fields
     * which must have uniqueness. Defaults to false.
     * @return object a New factory of the model type.
     */
    public static function create(Array $args = null, $sequence = null)
    {
        $model = self::MODEL_NAME;
        $obj = $model::create(self::generateArguments($args, $sequence));
        return $obj;
    }

    /**
     * Similar to the build function but initializes a list of objects.
     *
     * @param int $amount The amount of factories to initialize.
     * @param Array $args The arguments to create the factories.
     * @param boolean $sequence Whether to set unique values or not for fields
     * that require uniqueness.
     * @return Array An array of factories.
     */
    public static function buildList($amount, Array $args = null, $sequence = false)
    {
        $objects = [];

        for ($i=1; $i <= $amount; $i++) {
            array_push($objects, self::build($args, $sequence));
        }

        return $objects;
    }

    /**
     * Similar to the create function but creates a list of objects.
     *
     * @param int $amount The amount of factories to initialize.
     * @param Array $args The arguments to create the factories.
     * @param boolean $sequence Whether to set unique values or not for fields
     * that require uniqueness.
     * @return Array An array of factories.
     */
    public static function createList($amount, Array $args = null, $sequence = false)
    {
        $objects = [];

        for ($i=1; $i <= $amount; $i++) {
            array_push($objects, self::create($args, $sequence));
        }

        return $objects;
    }

}
