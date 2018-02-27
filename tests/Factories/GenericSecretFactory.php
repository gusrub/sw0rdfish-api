<?php

/**
 * @package Tests\Factories Contains classes that represent factories of data
 * models.
 */
namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\GenericSecret as GenericSecret;

/**
 * A factory that creates a default generic secret
 */
class GenericSecretFactory
{
    use GenericFactoryTrait;

    /**
     * Defines the model name that this factory creates
     */
    const MODEL_NAME = 'Sw0rdfish\Models\GenericSecret';

    /**
     * Generates the necessary arguments to fill in this factory. The model
     * properties may be explicitly set or otherwise they will be automatically
     * generated with random values. If a field needs uniqueness the `sequence`
     * parameter can be set to `true` to generated unique values for thar field.
     *
     * @param Array $args An array of key-value pairs to override the default
     * values of the generator.
     * @param boolean $sequence If set to true, fields that need unique values
     * will have a unique sequence appended so they are unique. Defaults to
     * `false`.
     */
    private static function generateArguments(Array $args = null, $sequence = null)
    {
        if (is_null($args)) {
            $args = [];
        }
        if ($sequence === true) {
            $sequence = uniqid();
        }
        $faker = Factory::create();

        return [
            'name' => array_key_exists('name', $args) ? $args['name'] : "Test Secret $sequence",
            'description' => array_key_exists('description', $args) ? $args['description'] : 'Test Secret Description',
            'notes' => array_key_exists('notes', $args) ? $args['notes'] : $faker->text($maxNbChars = 200),
            'category' => array_key_exists('category', $args) ? $args['category'] : 'generic_secret',
            'userId' => array_key_exists('userId', $args) ? $args['userId'] : $faker->randomDigitNotNull(),
            'secret' => array_key_exists('secret', $args) ? $args['secret'] : $faker->text($maxNbChars = 200)
        ];
    }
}
