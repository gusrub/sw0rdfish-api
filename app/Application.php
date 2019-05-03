<?php

namespace Sw0rdfish;

use Sw0rdfish\Helpers\I18n;

/**
* Main application
*/
class Application extends \Slim\App
{

    function __construct(Array $options = null)
    {
        parent::__construct($options);

        # environment mode should always be set outside the app
        $mode = getenv('SW0RDFISH_ENV');
        if (empty($mode)) {
            exit(I18n::translate("No environment set. Make sure that the SW0RDISH_ENV environment variable is set"));
        }

        # Load configuration but only if there is a file, else we
        # will just rely on the actual system ENV vars
        $configPath = __DIR__ . "/../";
        $configFile = ".env.$mode";
        if (file_exists($configPath . $configFile)) {
            $dotenv = new \Dotenv\Dotenv($configPath, $configFile);
            $dotenv->load();
        }

        # Load the routes
        $this->loadRoutes();
    }

    private function loadRoutes()
    {
        require __DIR__ . '/Routes.php';
    }
}


