<?php

namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\UserToken as UserToken;

class UserTokenFactory
{
    use GenericFactoryTrait;

    const MODEL_NAME = 'Sw0rdfish\Models\UserToken';

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
            'expiration' => array_key_exists('expiration', $args) ? $args['expiration'] : $faker->dateTime()->format('Y-m-d H:i:s')
        ];
    }
}
