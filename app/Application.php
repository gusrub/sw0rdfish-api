<?php  

namespace Sw0rdfish;

require __DIR__ .  '/../vendor/autoload.php';

/**
* Main application
*/
class Application extends \Slim\App
{
	
	function __construct($mode, Array $options = null)
	{
		parent::__construct($options);

		# Load configuration
		$dotenv = new \Dotenv\Dotenv(__DIR__ . "/../", ".env.$mode");
		$dotenv->load();		
	}
}


