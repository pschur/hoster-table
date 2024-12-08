<?php

require __DIR__.'/vendor/autoload.php';

session_start();

$env = Dotenv\Dotenv::createImmutable(__DIR__);
$env->safeLoad();

function check_auth(){
    return isset($_SESSION['auth']) && $_SESSION['auth'] == true;
}

function redirect(string $url) {
    header('Location: '.$url);
    exit;
}