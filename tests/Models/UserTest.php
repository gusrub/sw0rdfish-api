<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Sw0rdfish\Application as Application;
use Sw0rdfish\Models\DatabaseManager as DatabaseManager;
use Tests\Models\BaseTestCase;
use Sw0rdfish\Models\User as User;

/**
*
*/
class UserTest extends BaseTestCase
{

    const CLEANUP_TABLES = ["users", "user_tokens", "secrets"];

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
			defined("Sw0rdfish\Models\User::TABLE_NAME"),
			"User has a TABLE_NAME constant defined"
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
}
