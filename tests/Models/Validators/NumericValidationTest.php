<?php

namespace Test\Models\Validators;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\Validators\NumericValidation as NumericValidation;
use Tests\Models\BaseTestCase;

/**
* Contains tests for the Sw0rdfish\Models\Validators\NumericValidation validator.
*/
class NumericValidationTest extends BaseTestCase
{

    /**
     * Test that given value is not numeric
     *
     * @return void
     * @test
     */
    function invalidNumber()
    {
        $obj = new \StdClass();
        $obj->age = "invalid";
        $validator = new NumericValidation($obj, 'age');
        $this->assertFalse($validator->run());
    }

    /**
     * Test that given greater than options are valid
     *
     * @return void
     * @test
     */
    function invalidOptionsGreaterThan()
    {
        $this->expectException(\InvalidArgumentException::class);
        $obj = new \StdClass();
        $obj->age = "invalid";
        $options = [
            'greaterThan' => 18,
            'greaterThanOrEqual' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $validator->run();
    }

    /**
     * Test that given less than options are valid
     *
     * @return void
     * @test
     */
    function invalidOptionsLessThan()
    {
        $this->expectException(\InvalidArgumentException::class);
        $obj = new \StdClass();
        $obj->age = "invalid";
        $options = [
            'lessThan' => 18,
            'lessThanOrEqual' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $validator->run();
    }

    /**
     * Test that greaterThan does not surpass lessThan
     *
     * @return void
     * @test
     */
    function greaterThanSurpasesLessThan()
    {
        $this->expectException(\InvalidArgumentException::class);
        $obj = new \StdClass();
        $obj->age = 18;
        $options = [
            'greaterThan' => 18,
            'lessThan' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $validator->run();
    }

    /**
     * Test that greaterThan does not surpass lessThanOrEqual
     *
     * @return void
     * @test
     */
    function greaterThanSurpasesLessThanOrEqual()
    {
        $this->expectException(\InvalidArgumentException::class);
        $obj = new \StdClass();
        $obj->age = 18;
        $options = [
            'greaterThan' => 18,
            'lessThanOrEqual' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $validator->run();
    }

    /**
     * Test that greaterThanOrEqual does not surpass lessThan
     *
     * @return void
     * @test
     */
    function greaterThanOrEqualSurpasesLessThan()
    {
        $this->expectException(\InvalidArgumentException::class);
        $obj = new \StdClass();
        $obj->age = 18;
        $options = [
            'greaterThanOrEqual' => 18,
            'lessThan' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $validator->run();
    }

    /**
     * Test that greaterThanOrEqual does not surpass lessThanOrEqual
     *
     * @return void
     * @test
     */
    function greaterThanOrEqualSurpasesLessThanOrEqual()
    {
        $this->expectException(\InvalidArgumentException::class);
        $obj = new \StdClass();
        $obj->age = 18;
        $options = [
            'greaterThanOrEqual' => 19,
            'lessThanOrEqual' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $validator->run();
    }

    /**
     * Test greaterThan option
     *
     * @return void
     * @test
     */
    function validateGreaterThan()
    {
        $obj = new \StdClass();
        $obj->age = 16;
        $options = [
            'greaterThan' => 17
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $this->assertFalse($validator->run());

        $obj = new \StdClass();
        $obj->age = 18;
        $options = [
            'greaterThan' => 17
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $this->assertTrue($validator->run());
    }

    /**
     * Test greaterThanOrEqual option
     *
     * @return void
     * @test
     */
    function validateGreaterThanOrEqual()
    {
        $obj = new \StdClass();
        $obj->age = 17;
        $options = [
            'greaterThanOrEqual' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $this->assertFalse($validator->run());

        $obj = new \StdClass();
        $obj->age = 18;
        $options = [
            'greaterThanOrEqual' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $this->assertTrue($validator->run());
    }

    /**
     * Test lessThan option
     *
     * @return void
     * @test
     */
    function validateLessThan()
    {
        $obj = new \StdClass();
        $obj->age = 18;
        $options = [
            'lessThan' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $this->assertFalse($validator->run());

        $obj = new \StdClass();
        $obj->age = 17;
        $options = [
            'lessThan' => 18
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $this->assertTrue($validator->run());
    }

    /**
     * Test lessThanOrEqual option
     *
     * @return void
     * @test
     */
    function validateLessThanOrEqual()
    {
        $obj = new \StdClass();
        $obj->age = 18;
        $options = [
            'lessThanOrEqual' => 17
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $this->assertFalse($validator->run());

        $obj = new \StdClass();
        $obj->age = 17;
        $options = [
            'lessThanOrEqual' => 17
        ];
        $validator = new NumericValidation($obj, 'age', $options);
        $this->assertTrue($validator->run());
    }
}
