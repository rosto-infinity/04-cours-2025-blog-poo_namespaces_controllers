<?php
require_once __DIR__ . '/vendor/autoload.php';
use Libraries\Controllers\User;


$controllerUser = new User();
$controllerUser->login();
