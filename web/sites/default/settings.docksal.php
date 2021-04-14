<?php

/**
 * @file
 * Docksal db default.
 */

$databases = [
  'default' =>
    [
      'default' =>
      [
        'database' => 'default',
        'username' => 'user',
        'password' => 'user',
        'host' => 'db',
        'port' => '',
        'driver' => 'mysql',
        'prefix' => '',
      ],
    ],
];
// Prevent salt error on local.
$settings['hash_salt'] = 'de0399f83f27e88a0a05ab3ea964c6832b';
// Default docksal url.
if (isset($_SERVER['HTTP_HOST'])) {
  $settings['trusted_host_patterns'][] = '^' . $_SERVER['HTTP_HOST'] . '$';
}
// Workaround for permission issues with NFS shares in Vagrant.
$settings['file_chmod_directory'] = 0777;
$settings['file_chmod_file'] = 0666;

