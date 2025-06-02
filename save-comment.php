<?php
require_once __DIR__ . '/vendor/autoload.php';
use Libraries\Controllers\Comment;


$controllerComment= new Comment();
$controllerComment->save();
