<?php

return [
    'host' => env('RABBITMQ_HOST', 'localhost'),
    'port' => env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'guest'),
    'password' => env('RABBITMQ_PASS', 'guest'),
    'vhost' => env('RABBITMQ_VHOST', '/'),

    'exchange' => env('RABBITMQ_EXCHANGE', 'training_events'),
    'queue_prefix' => env('RABBITMQ_QUEUE_PREFIX', 'training_'),

    'connection_timeout' => env('RABBITMQ_CONNECTION_TIMEOUT', 3.0),
    'read_write_timeout' => env('RABBITMQ_READ_WRITE_TIMEOUT', 3.0),
];
