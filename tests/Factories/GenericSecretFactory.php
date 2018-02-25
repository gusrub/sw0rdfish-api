<?php

namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\GenericSecret as GenericSecret;

class GenericSecretFactory
{
    use GenericFactoryTrait;

    const MODEL_NAME = 'Sw0rdfish\Models\GenericSecret';

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
