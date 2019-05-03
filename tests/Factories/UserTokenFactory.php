<?php

/**
 * @package Tests\Factories Contains classes that represent factories of data
 * models.
 */
namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\UserToken as UserToken;

/**
 * A factory that creates a default user token
 */
class UserTokenFactory
{
    use GenericFactoryTrait;

    /**
     * Defines the model name that this factory creates
     */
    const MODEL_NAME = 'Sw0rdfish\Models\UserToken';

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
            'userId' => array_key_exists('userId', $args) ? $args['userId'] : $faker->randomDigitNotNull(),
            'type' => array_key_exists('type', $args) ? $args['type'] : UserToken::TYPES[array_rand(UserToken::TYPES)],
            'token' => array_key_exists('token', $args) ? $args['token'] : $faker->sha256(),
            'expiration' => array_key_exists('expiration', $args) ? $args['expiration'] : $faker->dateTime()->format('Y-m-d H:i:s'),
            'securityCode' => array_key_exists('securityCode', $args) ? $args['securityCode'] : base64_encode(random_bytes(UserToken::SECURITY_CODE_SIZE))
        ];
    }
}
