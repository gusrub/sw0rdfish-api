<?php

namespace Test\Models;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\GenericSecret as GenericSecret;
use Tests\Models\BaseTestCase;
use Tests\Factories\UserFactory as UserFactory;
use Tests\Factories\GenericSecretFactory as GenericSecretFactory;

/**
* Contains tests for the Sw0rdfish\Models\GenericSecret model.
*/
class GenericSecretTest extends BaseTestCase
{
    /**
     * Defines an array of tables that should be cleaned before each test
     */
    const CLEANUP_TABLES = ['users', 'secrets', 'generic_secrets'];

    /**
     * Test that the model defines a table constant
     *
     * @return void
     * @test
     */
    function definesTableConstant()
    {
        $this->assertTrue(
            defined('Sw0rdfish\Models\GenericSecret::TABLE_NAME'),
            'GenericSecret has a TABLE_NAME constant defined'
        );
    }

    /**
     * Test that userId is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfUserId()
    {
        $secret = GenericSecretFactory::build(['userId'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('userId', $errors);
        $this->assertArrayHasKey('presence', $errors['userId']);
    }

    /**
     * Test that userId is numeric
     *
     * @return void
     * @test
     */
    function validatesNumericalityOfUserId()
    {
        $secret = GenericSecretFactory::build(['userId'=>'invalid']);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('userId', $errors);
        $this->assertArrayHasKey('numeric', $errors['userId']);
    }

    /**
     * Test that userId is positive integer
     *
     * @return void
     * @test
     */
    function validatesPositivityOfUserId()
    {
        $secret = GenericSecretFactory::build(['userId'=>-15]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('userId', $errors);
        $this->assertArrayHasKey('numeric', $errors['userId']);
    }

    /**
     * Test that secret is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfName()
    {
        $secret = GenericSecretFactory::build(['name'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('presence', $errors['name']);
    }

    /**
     * Test that name is unique
     *
     * @return void
     * @test
     */
    function validatesUniquenessOfName()
    {
        $user = UserFactory::create();
        GenericSecretFactory::create([
            'name' => 'Test Generic Secret',
            'userId' => $user->id
        ]);
        $secret = GenericSecretFactory::build([
            'name' => 'Test Generic Secret'
        ]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('uniqueness', $errors['name']);
    }

    /**
     * Test that category is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfCategory()
    {
        $secret = GenericSecretFactory::build([
            'category' => null
        ]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('category', $errors);
        $this->assertArrayHasKey('presence', $errors['category']);
    }

    /**
     * Test that category is present
     *
     * @return void
     * @test
     */
    function validatesInclusionOfCategory()
    {
        $secret = GenericSecretFactory::build([
            'category' => 'invalid'
        ]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('category', $errors);
        $this->assertArrayHasKey('inclusion', $errors['category']);
    }

    /**
     * Test that secret text is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfSecret()
    {
        $secret = GenericSecretFactory::build(['secret'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('secret', $errors);
        $this->assertArrayHasKey('presence', $errors['secret']);
    }

    /**
     * Test that a new generic secret is created
     *
     * @return void
     * @test
     */
    function createNew()
    {
        $user = UserFactory::create();
        $secret = GenericSecretFactory::build(['userId'=>$user->id]);
        $this->assertTrue($secret->valid());
        $secret->save();
        $this->assertNotEmpty($secret->id);
    }

    /**
     * Test that an existing generic secret is retrieved
     *
     * @return void
     * @test
     */
    function get()
    {
        $user = UserFactory::create();
        $secret = GenericSecretFactory::create(['userId'=>$user->id]);
        $secret = GenericSecret::get($secret->id);
        $this->assertNotEmpty($secret);
    }

    /**
     * Test that a generic secret is successfully deleted
     *
     * @return void
     * @test
     */
    function deleteExisting()
    {
        $user = UserFactory::create();
        $secret = GenericSecretFactory::create(['userId'=>$user->id]);
        $this->assertTrue($secret->delete());
        $this->assertEmpty(GenericSecret::get($secret->id));
    }

    /**
     * Test that all generic secrets are returned when using no filters
     *
     * @return void
     * @test
     */
    function listWithoutFilters()
    {
        $user = UserFactory::create();
        $secrets = GenericSecretFactory::createList(
            4,
            ['userId'=>$user->id],
            true
        );
        $secrets = GenericSecret::all();
        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, count($secrets));
    }

    /**
     * Test that all generic secrets that match where criteria are returned
     *
     * @return void
     * @test
     */
    function listWithWhereFilter()
    {
        $userId = UserFactory::create()->id;
        $secrets = GenericSecretFactory::createList(
            4,
            ['userId'=>$userId],
            true
        );

        $secrets = GenericSecret::all([
            'where' => [
                'userId' => $userId
            ]
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, count($secrets));
    }

    /**
     * Test that all generic secrets that match like criteria are returned
     *
     * @return void
     * @test
     */
    function listWithLikeFilter()
    {
        $userId = UserFactory::create()->id;
        $secrets = GenericSecretFactory::createList(
            2,
            [
                'description' => 'desc1',
                'userId'=> $userId
            ],
            true
        );
        $secrets = GenericSecretFactory::createList(
            2,
            [
                'description' => 'desc2',
                'userId'=> $userId
            ],
            true
        );

        $secrets = GenericSecret::all([
            'like' => [
                'description' => 'desc1'
            ]
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(2, count($secrets));
    }

    /**
     * Test that only paginated records are returned
     *
     * @return void
     * @test
     */
    function listWithPagination()
    {
        putenv("MAX_RECORDS_PER_PAGE=5");
        $userId = UserFactory::create()->id;
        GenericSecretFactory::createList(7, ['userId'=>$userId], true);

        $secrets = GenericSecret::all([
            'page' => 1
        ]);
        $this->assertNotEmpty($secrets);
        $this->assertEquals(5, count($secrets));

        $secrets = GenericSecret::all([
            'page' => 2
        ]);
        $this->assertNotEmpty($secrets);
        $this->assertEquals(2, count($secrets));
    }

    /**
     * Test that records are properly sorted
     *
     * @return void
     * @test
     */
    function listWithSorting()
    {
        $userId = UserFactory::create()->id;
        $secrets = GenericSecretFactory::createList(4, ['userId'=>$userId], true);

        $secrets = GenericSecret::all([
            'orderBy' => 'id',
            'sort' => 'desc'
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, $secrets[0]->id);
    }
}
