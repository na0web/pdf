<?php
$dotenv = Dotenv\Dotenv::create(__DIR__ . '/../..');
$dotenv->load();
$dotenv->required(['APP_DEBUG'])->isBoolean();
$dotenv->required(['REDIS_HOST'])->notEmpty();
$dotenv->required(['REDIS_PORT'])->isInteger();

return [
    'settings' => [
        'displayErrorDetails' => (boolean)json_decode(strtolower(getenv('APP_DEBUG'))), // set to false in production
        'addContentLengthHeader' => true, // Allow the web server to send the content-length header

        // Monolog settings
        'logger' => [
            'name' => 'pdfservice',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        // Redis settings
        'redis' => [
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
        ],

    ]
];
