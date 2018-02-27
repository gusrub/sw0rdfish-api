<?php

/**
 * @package Tests\Factories Contains classes that represent factories of data
 * models.
 */
namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\User as User;

/**
 * A factory that creates a default user
 */
class UserFactory
{
    use GenericFactoryTrait;

    /**
     * Defines the model name that this factory creates
     */
    const MODEL_NAME = 'Sw0rdfish\Models\User';

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
        $faker = Factory::create();

        return [
            'firstName' =>  array_key_exists('firstName', $args) ? $args['firstName'] : $faker->firstName,
            'lastName' => array_key_exists('lastName', $args) ? $args['lastName'] : $faker->lastName,
            'email' => array_key_exists('email', $args) ? $args['email'] : $faker->safeEmail,
            'password' => array_key_exists('password', $args) ? $args['password'] : $faker->password,
            'role' => array_key_exists('role', $args) ? $args['role'] : User::ROLES[array_rand(User::ROLES)]
        ];
    }
}