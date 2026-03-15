<?php

namespace App\Controllers;

use Illuminate\Database\Capsule\Manager as Capsule;

class Repository
{

    public static function start(): void
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => '192.168.1.2:3306',
            'database' => 'BeTalent',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

}