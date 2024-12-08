<?php

require __DIR__.'/../server.php';

$_SESSION['auth'] = false;
$_SESSION['user'] = null;

redirect('/auth.html');