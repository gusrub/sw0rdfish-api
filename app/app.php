<?php  

require __DIR__ .  '/../vendor/autoload.php';

# Load configuration
$dotenv = new Dotenv\Dotenv(__DIR__ . "/../");
$dotenv->load();

# Start the app
$app = new \Slim\App([
    'settings' => [
      'displayErrorDetails' => true
    ]
  ]);

require __DIR__ . '/../app/routes.php';

