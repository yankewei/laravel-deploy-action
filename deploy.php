<?php

// Get the required information when connect remote server
$host     = getenv('HOST');
$username = getenv('USERNAME');
$password = getenv('PASSWORD');

// Create a session between with the remote server
$ssh_session = ssh2_connect($host);

// Use password to connect server
if (!ssh2_auth_password($ssh_session, $username, $password)) {
    exit('Failed to connect the remote server');
}

// Get the application environment variable from github action env
$env       = getenv('APP_ENV_DATA');
$env_array = explode("\n", $env);

// Put the env data into .env file
$cmd = 'cd /var/www/site;cat /dev/null > .env;';
foreach ($env_array as $value) {
    $cmd .= "echo $value >> .env;";
}

// Run command and deploy application
$cmd .= 'php artisan down;';
$cmd .= 'git pull origin main;';
$cmd .= 'composer install --no-dev;';
$cmd .= 'php artisan migrate --force;';
$cmd .= 'php artisan optimize;';
$cmd .= 'php artisan up;';

$stream = ssh2_exec($ssh_session, $cmd);

echo stream_get_contents($stream);

ssh2_disconnect($ssh_session);