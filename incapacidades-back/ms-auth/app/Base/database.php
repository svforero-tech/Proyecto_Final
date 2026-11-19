<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$dbHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$dbName = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: '';
$dbUser = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root';
$dbPass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $dbHost,
    'port'      => $dbPort,
    'database'  => $dbName,
    'username'  => $dbUser,
    'password'  => $dbPass,
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();