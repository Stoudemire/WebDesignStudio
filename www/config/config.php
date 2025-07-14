<?php
return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'reino_habbo',
        'username' => 'reino_habbo',
        'password' => 'wpgsuBNY!BnGnY_C',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],
    'app' => [
        'name' => 'Reino de Habbo',
        'version' => '1.0.0',
        'timezone' => 'America/Mexico_City'
    ]
];
?>