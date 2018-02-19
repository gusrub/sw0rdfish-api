<?php

namespace Tests\Factories;

trait GenericFactoryTrait {

    public static function build(Array $args = null, $sequence = null)
    {
        $model = self::MODEL_NAME;
        $obj = new $model(self::generateArguments($args, $sequence));
        return $obj;
    }

    public static function create(Array $args = null, $sequence = null)
    {
        $model = self::MODEL_NAME;
        $obj = $model::create(self::generateArguments($args, $sequence));
        return $obj;
    }

    public static function buildList($amount, Array $args = null, $sequence = false)
    {
        $objects = [];

        for ($i=1; $i <= $amount; $i++) {
            array_push($objects, self::build($args, $sequence));
        }

        return $objects;
    }

    public static function createList($amount, Array $args = null, $sequence = false)
    {
        $objects = [];

        for ($i=1; $i <= $amount; $i++) {
            array_push($objects, self::create($args, $sequence));
        }

        return $objects;
    }

}
