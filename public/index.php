<?php

# Bootstrap the app
require __DIR__ . '/../app/Application.php';
$app = new Sw0rdfish\Application([
        'settings' => [
			  'displayErrorDetails' => true
			]
        ]);

if (empty(getenv('SW0RDFISH_CONSOLE'))) {
    $app->run();
}

