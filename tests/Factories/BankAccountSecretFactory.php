<?php

namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\BankAccountSecret as BankAccountSecret;

class BankAccountSecretFactory
{
    use GenericFactoryTrait;

    const MODEL_NAME = 'Sw0rdfish\Models\BankAccountSecret';

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
            'category' => array_key_exists('category', $args) ? $args['category'] : 'bank_account_secret',
            'userId' => array_key_exists('userId', $args) ? $args['userId'] : $faker->randomDigitNotNull(),
            'accountNumber' => array_key_exists('accountNumber', $args) ? $args['accountNumber'] : $faker->iban('US'),
            'routingNumber' => array_key_exists('routingNumber', $args) ? $args['routingNumber'] : $faker->swiftBicNumber()
        ];
    }
}
