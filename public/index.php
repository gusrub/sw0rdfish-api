<?php

# Bootstrap the app
require __DIR__ . '/../app/Application.php';
$app = new Sw0rdfish\Application("development", [
			'settings' => [
			  'displayErrorDetails' => true
			]
		]);
require __DIR__ . '/../app/Routes.php';
$app->run();
