<?php

namespace Tests\Factories;

trait GenericFactoryTrait {

    public static function build(Array $args = null)
    {
        $model = self::MODEL_NAME;
        $obj = new $model(self::generateArguments($args));
        return $obj;
    }

    public static function create(Array $args = null)
    {
        $model = self::MODEL_NAME;
        $obj = $model::create(self::generateArguments($args));
        return $obj;
    }

    public static function buildList($amount, Array $args = null)
    {
        $objects = [];

        while (count($objects) < $amount) {
            array_push($objects, self::create($args));
        }

        return $objects;
    }

    public static function createList($amount, Array $args = null)
    {
        $objects = [];

        while (count($objects) < $amount) {
            array_push($objects, self::create($args));
        }

        return $objects;
    }

}
