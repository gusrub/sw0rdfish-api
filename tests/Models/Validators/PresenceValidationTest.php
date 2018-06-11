<?php

namespace Test\Models\Validators;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\Validators\PresenceValidation as PresenceValidation;
use Tests\Models\BaseTestCase;

/**
* Contains tests for the Sw0rdfish\Models\Validators\PresenceValidation validator.
*/
class PresenceValidationTest extends BaseTestCase
{

    /**
     * Test that value is present
     *
     * @return void
     * @test
     */
    function valueIsPresent()
    {
        $obj = new \StdClass();
        $obj->firstName = null;

        $validator = new PresenceValidation($obj, 'firstName');
        $this->assertFalse($validator->run());

        $obj = new \StdClass();
        $obj->firstName = 'John';

        $validator = new PresenceValidation($obj, 'firstName');
        $this->assertTrue($validator->run());
    }
}
