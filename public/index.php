<?php

# Bootstrap the app:
#
# The following lines will load the composer autoloader and then instantiante
# our main application respectively. If we follow the PSR loading standards
# there is no need to do requires elsewhere!
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Application.php';

$app = new Sw0rdfish\Application([
        'settings' => [
			  'displayErrorDetails' => true
			]
        ]);

if (empty(getenv('SW0RDFISH_CONSOLE'))) {
    $app->run();
}
