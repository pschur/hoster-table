<?php

require __DIR__.'/../server.php';

if (check_auth()) {
    redirect('/');
}

use Jumbojett\OpenIDConnectClient;

$empty = function($var) {
    return !isset($var) || empty($var) || $var === null || $var === '';
};

if ($empty($_ENV['OPENID_DISCOVER_URL']) || $empty($_ENV['OPENID_CLIENT_ID']) || $empty($_ENV['OPENID_SECRET'])) {
    die('ERROR: Some parameters are missing.');
}

$oidc = new OpenIDConnectClient($_ENV['OPENID_DISCOVER_URL'], $_ENV['OPENID_CLIENT_ID'], $_ENV['OPENID_SECRET']);
$oidc->authenticate();

$_SESSION['auth'] = true;
$_SESSION['user'] = $oidc->requestUserInfo();

redirect('/');