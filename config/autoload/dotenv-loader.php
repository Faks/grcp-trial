<?php

declare(strict_types=1);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');

foreach ($dotenv->load() as $key => $value) {
    putenv("$key=$value");
}
