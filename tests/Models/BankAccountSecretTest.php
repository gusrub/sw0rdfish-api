<?php

namespace Test\Models;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Sw0rdfish\Models\BankAccountSecret as BankAccountSecret;
use Tests\Models\BaseTestCase;
use Tests\Factories\UserFactory as UserFactory;
use Tests\Factories\BankAccountSecretFactory as BankAccountSecretFactory;

/**
* Contains tests for the Sw0rdfish\Models\BankAccountSecret model.
*/
class BankAccountSecretTest extends BaseTestCase
{
    /**
     * Defines an array of tables that should be cleaned before each test
     */
    const CLEANUP_TABLES = ['users', 'secrets', 'bank_account_secrets'];

    /**
     * Test that the model defines a table constant
     *
     * @return void
     * @test
     */
    function definesTableConstant()
    {
        $this->assertTrue(
            defined('Sw0rdfish\Models\BankAccountSecret::TABLE_NAME'),
            'BankAccountSecret has a TABLE_NAME constant defined'
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
        $secret = BankAccountSecretFactory::build(['userId'=>null]);
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
        $secret = BankAccountSecretFactory::build(['userId'=>'invalid']);
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
        $secret = BankAccountSecretFactory::build(['userId'=>-15]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('userId', $errors);
        $this->assertArrayHasKey('numeric', $errors['userId']);
    }

    /**
     * Test that name is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfName()
    {
        $secret = BankAccountSecretFactory::build(['name'=>null]);
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
        BankAccountSecretFactory::create([
            'name' => 'Test Bank Account Secret',
            'userId' => $user->id
        ]);
        $secret = BankAccountSecretFactory::build([
            'name' => 'Test Bank Account Secret'
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
        $secret = BankAccountSecretFactory::build([
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
        $secret = BankAccountSecretFactory::build([
            'category' => 'invalid'
        ]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('category', $errors);
        $this->assertArrayHasKey('inclusion', $errors['category']);
    }

    /**
     * Test that accountNumber is present
     *
     * @return void
     * @test
     */
    function validatesPresenceOfAccountNumber()
    {
        $secret = BankAccountSecretFactory::build(['accountNumber'=>null]);
        $this->assertFalse($secret->valid());
        $errors = $secret->getValidationErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('accountNumber', $errors);
        $this->assertArrayHasKey('presence', $errors['accountNumber']);
    }

    /**
     * Test that a new bank account secret is created
     *
     * @return void
     * @test
     */
    function createNew()
    {
        $user = UserFactory::create();
        $secret = BankAccountSecretFactory::build(['userId'=>$user->id]);
        $this->assertTrue($secret->valid());
        $secret->save();
        $this->assertNotEmpty($secret->id);
    }

    /**
     * Test that an existing bank account secret is retrieved
     *
     * @return void
     * @test
     */
    function get()
    {
        $user = UserFactory::create();
        $secret = BankAccountSecretFactory::create(['userId'=>$user->id]);
        $secret = BankAccountSecret::get($secret->id);
        $this->assertNotEmpty($secret);
    }

    /**
     * Test that a bank account secret is successfully deleted
     *
     * @return void
     * @test
     */
    function deleteExisting()
    {
        $user = UserFactory::create();
        $secret = BankAccountSecretFactory::create(['userId'=>$user->id]);
        $this->assertTrue($secret->delete());
        $this->assertEmpty(BankAccountSecret::get($secret->id));
    }

    /**
     * Test that all bank account secrets are returned when using no filters
     *
     * @return void
     * @test
     */
    function listWithoutFilters()
    {
        $user = UserFactory::create();
        $secrets = BankAccountSecretFactory::createList(
            4,
            ['userId'=>$user->id],
            true
        );
        $secrets = BankAccountSecret::all();
        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, count($secrets));
    }

    /**
     * Test that all bank account secrets that match where criteria are returned
     *
     * @return void
     * @test
     */
    function listWithWhereFilter()
    {
        $userId = UserFactory::create()->id;
        $secrets = BankAccountSecretFactory::createList(
            4,
            ['userId'=>$userId],
            true
        );

        $secrets = BankAccountSecret::all([
            'where' => [
                'userId' => $userId
            ]
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, count($secrets));
    }

    /**
     * Test that all bank account secrets that match like criteria are returned
     *
     * @return void
     * @test
     */
    function listWithLikeFilter()
    {
        $userId = UserFactory::create()->id;
        $secrets = BankAccountSecretFactory::createList(
            2,
            [
                'description' => 'desc1',
                'userId'=> $userId
            ],
            true
        );
        $secrets = BankAccountSecretFactory::createList(
            2,
            [
                'description' => 'desc2',
                'userId'=> $userId
            ],
            true
        );

        $secrets = BankAccountSecret::all([
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
        BankAccountSecretFactory::createList(7, ['userId'=>$userId], true);

        $secrets = BankAccountSecret::all([
            'page' => 1
        ]);
        $this->assertNotEmpty($secrets);
        $this->assertEquals(5, count($secrets));

        $secrets = BankAccountSecret::all([
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
        $secrets = BankAccountSecretFactory::createList(4, ['userId'=>$userId], true);

        $secrets = BankAccountSecret::all([
            'orderBy' => 'id',
            'sort' => 'desc'
        ]);

        $this->assertNotEmpty($secrets);
        $this->assertEquals(4, $secrets[0]->id);
    }
}
