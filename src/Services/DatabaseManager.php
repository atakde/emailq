<?php

namespace EmailQ\Services;

use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseManager
{
    private array $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function connect(): void
    {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => $this->config['DB_DRIVER'] ?? '',
            'host' => $this->config['DB_HOST'] ?? '',
            'database' => $this->config['DB_NAME'] ?? '',
            'username' => $this->config['DB_USER'] ?? '',
            'password' => $this->config['DB_PASSWORD'] ?? '',
            'charset' => $this->config['DB_CHARSET'] ?? '',
            'collation' => $this->config['DB_COLLATION'] ?? '',
            'prefix' => $this->config['DB_PREFIX'] ?? '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
