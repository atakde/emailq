<?php

namespace EmailQ\Core;

use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv;

class Bootstrap
{
    public function initAll()
    {
        $this->initEnv();
        $this->initCapsule();
    }

    public function initCapsule()
    {

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => $_ENV['DB_DRIVER'],
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_NAME'],
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset' => $_ENV['DB_CHARSET'],
            'collation' => $_ENV['DB_COLLATION'],
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function initEnv()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }
}
