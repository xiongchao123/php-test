<?php

use App\Foundation\Application;
use App\Providers\ConsoleServiceProvider;

define('ROOT_PATH', __DIR__ . '/../');
define('APP_START', microtime(true));

require_once ROOT_PATH . 'vendor/autoload.php';

$app = new Application(ROOT_PATH);

$app->addServiceProvider(ConsoleServiceProvider::class);

return $app;