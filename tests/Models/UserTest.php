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
	 * Test that ID is a number
	 *
	 * @return void
	 * @author 
	 * @test
	 **/
	function validatesNumericalityOfId()
	{
		$user = new User(["id"=>"NaN"]);
		$this->assertFalse($user->valid());
		$this->assertArrayHasKey("id", $user->getValidationErrors());
	}
}