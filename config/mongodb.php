<?php declare(strict_types=1);

use yii\mongodb\Connection;

return [
    'class' => Connection::class,
    'dsn' => 'mongodb://root:root@db:27017/local?authSource=admin',
//    'options' => [
//        'username' => 'root',
//        'password' => 'root'
//    ]
];
