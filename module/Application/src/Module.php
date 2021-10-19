<?php

declare(strict_types=1);

namespace Application;

class Module
{
    public const VERSION = '3.1.3';

    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
