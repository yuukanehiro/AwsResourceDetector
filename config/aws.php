<?php
require 'vendor/autoload.php';
// .env読み込み
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

return [
    'credentials' => [
        'secret_key_id' => $_ENV['AWS_CREDENTIALS_SECRET_KEY_ID'],
        'secret' => $_ENV['AWS_CREDENTIALS_SECRET'],
        'region' => $_ENV['AWS_DEFAULT_REGION'],
    ],
    'client' => [
        'ec2' => [
            'version' => '2016-09-15',
            'region' => $_ENV['AWS_DEFAULT_REGION'],
            // 検索対象のキー
            'target_key' => [
                'PrivateDnsName',
            ],
        ],
    ],
];
