<?php

namespace Tests\Factories;

use Faker\Factory as Factory;
use Sw0rdfish\Models\User as User;

class UserFactory
{

    public static function build(Array $args = null)
    {
        $user = new User(self::generateArguments($args));
        return $user;
    }

    public static function create(Array $args = null)
    {
        $user = User::create(self::generateArguments($args));
        return $user;
    }

    public static function buildList($amount)
    {
        $users = [];

        while (count($users) < $amount) {
            array_push($users, self::create());
        }

        return $users;
    }

    public static function createList($amount)
    {
        $users = [];

        while (count($users) < $amount) {
            array_push($users, self::create());
        }

        return $users;
    }

    private static function generateArguments(Array $args = null)
    {
        $faker = Factory::create();

        return [
            'firstName' => $args['firstName'] ?? $faker->firstName,
            'lastName' => $args['lastName'] ?? $faker->lastName,
            'email' => $args['email'] ?? $faker->safeEmail,
            'password' => $args['password'] ?? $faker->password,
            'role' => $args['role'] ?? User::ROLES[array_rand(User::ROLES)]
        ];
    }
}