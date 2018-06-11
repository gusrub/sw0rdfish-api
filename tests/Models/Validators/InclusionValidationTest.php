<?php

namespace Test\Models\Validators;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\Validators\InclusionValidation as InclusionValidation;
use Tests\Models\BaseTestCase;

/**
* Contains tests for the Sw0rdfish\Models\Validators\InclusionValidation validator.
*/
class InclusionValidationTest extends BaseTestCase
{

    /**
     * Test that input is in list
     *
     * @return void
     * @test
     */
    function includedInList()
    {
        $obj = new \StdClass();
        $obj->gender = 'male';

        $validator = new InclusionValidation($obj, 'gender', ['male', 'female']);
        $this->assertTrue($validator->run());
    }

    /**
     * Test that input is not in list
     *
     * @return void
     * @test
     */
    function notIncludedInList()
    {
        $obj = new \StdClass();
        $obj->gender = 'invalid';

        $validator = new InclusionValidation($obj, 'gender', ['male', 'female']);
        $this->assertFalse($validator->run());
    }

    /**
     * Test that given options are invalid
     *
     * @return void
     * @test
     */
    function invalidOptions()
    {
        $this->expectException(\InvalidArgumentException::class);
        $obj = new \StdClass();
        $obj->gender = 'invalid';

        $validator = new InclusionValidation($obj, 'gender');
        $validator->run();

    }
}
