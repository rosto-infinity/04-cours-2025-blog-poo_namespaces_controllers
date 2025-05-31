<?php

use Libraries\Controllers\User;
require_once "libraries/Controllers/User.php";

$controllerUser = new User();
$controllerUser->login();
