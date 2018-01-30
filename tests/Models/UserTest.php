<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Tests\Models\BaseTestCase;
use Sw0rdfish\Models\User as User;
use Tests\Factories\UserFactory as UserFactory;

/**
*
*/
class UserTest extends BaseTestCase
{

    const CLEANUP_TABLES = ['users', 'user_tokens', 'secrets'];

	/**
	 * Test that the model defines a table constant
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function definesTableConstant()
	{
		$this->assertTrue(
			defined('Sw0rdfish\Models\User::TABLE_NAME'),
			'User has a TABLE_NAME constant defined'
		);
	}

	/**
	 * Test that firstName is present
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function validatesPresenceOfFirstName()
	{
		$user = new User(['firstName'=>null]);
		$this->assertFalse($user->valid());
		$errors = $user->getValidationErrors();
		$this->assertNotEmpty($errors);
		$this->assertArrayHasKey('firstName', $errors);
		$this->assertArrayHasKey('presence', $errors['firstName']);
	}

	/**
	 * Test that lastName is present
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function validatesPresenceOfLastName()
	{
		$user = new User(['lastName'=>null]);
		$this->assertFalse($user->valid());
		$errors = $user->getValidationErrors();
		$this->assertNotEmpty($errors);
		$this->assertArrayHasKey('lastName', $errors);
		$this->assertArrayHasKey('presence', $errors['lastName']);
	}

	/**
	 * Test that email is present
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function validatesPresenceOfEmail()
	{
		$user = new User(['email'=>null]);
		$this->assertFalse($user->valid());
		$errors = $user->getValidationErrors();
		$this->assertNotEmpty($errors);
		$this->assertArrayHasKey('email', $errors);
		$this->assertArrayHasKey('presence', $errors['email']);
	}

	/**
	 * Test that email is a valid address
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function validatesEmailFormat()
	{
		$user = new User(['email'=>'someone AT example.com']);
		$this->assertFalse($user->valid());
		$errors = $user->getValidationErrors();
		$this->assertNotEmpty($errors);
		$this->assertArrayHasKey('email', $errors);
		$this->assertArrayHasKey('email', $errors['email']);
	}

	/**
	 * Test that email is a valid address
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function validatesEmailUniqueness()
	{
		$existingUser = User::create([
			'firstName' => 'John',
			'lastName' => 'Wayne',
			'email' => 'john@example.com',
			'password' => 'password',
			'role' => 'user'
		]);
		$user = new User(['email'=>'john@example.com']);
		$this->assertFalse($user->valid());
		$errors = $user->getValidationErrors();
		$this->assertNotEmpty($errors);
		$this->assertArrayHasKey('email', $errors);
		$this->assertArrayHasKey('uniqueness', $errors['email']);
	}

	/**
	 * Test that role is present
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function validatesPresenceOfRole()
	{
		$user = new User(['role'=>null]);
		$this->assertFalse($user->valid());
		$errors = $user->getValidationErrors();
		$this->assertNotEmpty($errors);
		$this->assertArrayHasKey('role', $errors);
		$this->assertArrayHasKey('presence', $errors['role']);
	}

	/**
	 * Test that role is a valid value
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function validatesInclusionOfRole()
	{
		$user = new User(['role'=>'invalid']);
		$this->assertFalse($user->valid());
		$errors = $user->getValidationErrors();
		$this->assertNotEmpty($errors);
		$this->assertArrayHasKey('role', $errors);
		$this->assertArrayHasKey('inclusion', $errors['role']);
	}

	/**
	 * Test that a new user is created
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function createNew()
	{
		$user = UserFactory::build();
		$this->assertTrue($user->valid());
		$user->save();
		$this->assertNotEmpty($user->id);
	}

	/**
	 * Test that an existing user is updated
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function updateExisting()
	{
		$newParams = [
			'firstName' => 'John',
			'lastName' => 'Wayne',
			'email' => 'john@example.com',
			'role' => 'admin',
			'password' => 'password'
		];

		$user = UserFactory::create([
			'role' => 'super'
		]);
		$user = User::update($user->id, $newParams);

		$this->assertEquals($newParams['firstName'], $user->firstName);
		$this->assertEquals($newParams['lastName'], $user->lastName);
		$this->assertEquals($newParams['email'], $user->email);
		$this->assertEquals($newParams['role'], $user->role);
		$this->assertEquals($newParams['password'], $user->password);
	}

	/**
	 * Test that all users are returned when using no filters
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function listWithoutFilters()
	{
		UserFactory::createList(4);
		$users = User::all();
		$this->assertNotEmpty($users);
		$this->assertEquals(4, count($users));
	}

	/**
	 * Test that all users that match where criteria are returned
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function listWithWhereFilter()
	{
		$lastNames = ['Gallagher', 'Wayne', 'Gallagher'];

		foreach ($lastNames as $lastName) {
			UserFactory::create([
				'lastName' => $lastName
			]);
		}

		$users = User::all([
			'where' => [
				'lastName' => 'Gallagher'
			]
		]);

		$this->assertNotEmpty($users);
		$this->assertEquals(2, count($users));
	}

	/**
	 * Test that all users that match like criteria are returned
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function listWithLikeFilter()
	{
		$lastNames = ['Gallagher', 'Wayne', 'Gallagher'];

		foreach ($lastNames as $lastName) {
			UserFactory::create([
				'lastName' => $lastName
			]);
		}

		$users = User::all([
			'like' => [
				'lastName' => 'agher'
			]
		]);

		$this->assertNotEmpty($users);
		$this->assertEquals(2, count($users));
	}


	/**
	 * Test that only paginated records are returned
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function listWithPagination()
	{
		putenv("MAX_RECORDS_PER_PAGE=5");
		UserFactory::createList(7);

		$users = User::all([
			'page' => 1
		]);
		$this->assertNotEmpty($users);
		$this->assertEquals(5, count($users));

		$users = User::all([
			'page' => 2
		]);
		$this->assertNotEmpty($users);
		$this->assertEquals(2, count($users));
	}

	/**
	 * Test that only records are properly sorted
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function listWithSorting()
	{
		$lastNames = ['Andrews', 'Berry', 'Clark'];

		foreach ($lastNames as $lastName) {
			UserFactory::create([
				'lastName' => $lastName
			]);
		}

		$users = User::all([
			'orderBy' => 'lastName',
			'sort' => 'desc'
		]);

		$this->assertNotEmpty($users);
		$this->assertEquals('Clark', $users[0]->lastName);
	}

	/**
	 * Test that a user is succesfully deleted
	 *
	 * @return void
	 * @author
	 * @test
	 **/
	function deleteExisting()
	{
		$user = UserFactory::create();
		$user->delete();
		$user = User::get($user->id);

		$this->assertEmpty($user);
	}

}
