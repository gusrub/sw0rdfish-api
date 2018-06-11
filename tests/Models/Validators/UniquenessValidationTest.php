<?php

namespace Test\Models\Validators;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\Validators\UniquenessValidation as UniquenessValidation;
use Sw0rdfish\Models\User as User;
use Tests\Factories\UserFactory as UserFactory;
use Tests\Models\BaseTestCase;

/**
* Contains tests for the Sw0rdfish\Models\Validators\UniquenessValidation validator.
*/
class UniquenessValidationTest extends BaseTestCase
{

    /** Defines an array of tables that should be cleaned before each test */
    const CLEANUP_TABLES = ['users', 'user_tokens', 'secrets'];

    /**
     * Test that given options are invalid
     *
     * @return void
     * @test
     */
    function invalidOptions()
    {
        $this->expectException(\InvalidArgumentException::class);
        $obj = UserFactory::build();
        $obj->email = 'someone@example.com';
        $options = ['invalidOption' => 'invalid'];
        $validator = new UniquenessValidation($obj, 'email', $options);
        $validator->run();
    }

    /**
     * Test uniqueness with default options
     *
     * @return void
     * @test
     */
    function defaultOptions()
    {
        UserFactory::create(['email'=>'someone@example.com']);
        $obj = UserFactory::build(['email'=>'someone@example.com']);
        $validator = new UniquenessValidation($obj, 'email');
        $this->assertFalse($validator->run());

        $obj = UserFactory::build(['email'=>'someone2@example.com']);
        $validator = new UniquenessValidation($obj, 'email');
        $this->assertTrue($validator->run());
    }

    /**
     * Test uniqueness with caseSensitive option set to true
     *
     * @return void
     * @test
     */
    function caseSensitiveOption()
    {
        UserFactory::create(['email'=>'SOMEONE@EXAMPLE.COM']);
        $obj = UserFactory::build(['email'=>'someone@example.com']);
        $options = ['caseSensitive'=>true];
        $validator = new UniquenessValidation($obj, 'email', $options);
        $this->assertTrue($validator->run());
    }

    /**
     * Test uniqueness with scope option set
     *
     * @return void
     * @test
     */
    function scopeOption()
    {
        UserFactory::create(['email'=>'john.wayne@example.com', 'firstName'=>'John', 'lastName'=>'Wayne']);
        $obj = UserFactory::build(['email'=>'john.doe@example.com', 'firstName'=>'John', 'lastName'=>'Doe']);
        $options = ['scope'=>'lastName'];
        $validator = new UniquenessValidation($obj, 'firstName', $options);
        $this->assertTrue($validator->run());
    }
}
