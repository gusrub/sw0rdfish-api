<?php

namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\WebsiteCredentialSecret as WebsiteCredentialSecret;

class WebsiteCredentialSecretFactory
{
    use GenericFactoryTrait;

    const MODEL_NAME = 'Sw0rdfish\Models\WebsiteCredentialSecret';

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
            'username' => array_key_exists('username', $args) ? $args['username'] : $faker->userName(),
            'password' => array_key_exists('password', $args) ? $args['password'] : $faker->password(),
            'url' => array_key_exists('url', $args) ? $args['url'] : $faker->url()
        ];
    }
}
