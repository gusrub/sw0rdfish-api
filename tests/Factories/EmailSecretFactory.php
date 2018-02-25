<?php

namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\EmailSecret as EmailSecret;

class EmailSecretFactory
{
    use GenericFactoryTrait;

    const MODEL_NAME = 'Sw0rdfish\Models\EmailSecret';

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
            'email' => array_key_exists('email', $args) ? $args['email'] : $faker->safeEmail(),
            'password' => array_key_exists('password', $args) ? $args['password'] : $faker->password(),
            'url' => array_key_exists('url', $args) ? $args['url'] : $faker->url()
        ];
    }
}
