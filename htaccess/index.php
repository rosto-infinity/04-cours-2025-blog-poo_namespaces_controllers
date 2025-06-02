<?php 
require_once __DIR__ . '/vendor/autoload.php';
use Libraries\Controllers\Article;


$controllerArticle = new Article();
$controllerArticle->index();
