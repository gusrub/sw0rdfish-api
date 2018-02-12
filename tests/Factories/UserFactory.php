<?php

namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\User as User;

class UserFactory
{
    use GenericFactoryTrait;

    const MODEL_NAME = 'Sw0rdfish\Models\User';

    private static function generateArguments(Array $args = null)
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