<?php

$config = [
    'target_php_version' => null,
    'directory_list' => [
        'src',
    ],
    'exclude_analysis_directory_list' => [],
];

// workaround for phan on codeclimate
if (file_exists(dirname(__DIR__) . '/vendor/psr')) {
    $config['directory_list'] [] = 'vendor/psr';
    $config['exclude_analysis_directory_list'] [] = 'vendor/';
}

return $config;
