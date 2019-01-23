<?php

namespace Test\Models\Validators;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\Validators\EmailValidation as EmailValidation;
use Tests\Models\BaseModelTestCase;

/**
* Contains tests for the Sw0rdfish\Models\Validators\EmailValidation validator.
*/
class EmailValidationTest extends BaseModelTestCase
{

    /**
     * Test that input is a valid email
     *
     * @return void
     * @test
     */
    function validEmail()
    {
        $obj = new \StdClass();
        $obj->email = 'someone@example.com';

        $validator = new EmailValidation($obj, 'email');
        $this->assertTrue($validator->run());
    }

    /**
     * Test that input is an invalid email
     *
     * @return void
     * @test
     */
    function invalidEmail()
    {
        $obj = new \StdClass();
        $obj->email = 'invalid';

        $validator = new EmailValidation($obj, 'email');
        $this->assertFalse($validator->run());
    }
}
