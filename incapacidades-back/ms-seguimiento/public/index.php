<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . '=' . trim($value));
    }
}

require __DIR__ . '/../app/Base/database.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$cors = require __DIR__ . '/../app/Base/cors.php';
$cors($app);

$routes = require __DIR__ . '/../app/Presentation/Routers/endpoints.php';
$routes($app);

$app->run();