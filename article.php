<?php

use Libraries\Renderer;
use Libraries\Models\Article;
use Libraries\Models\Comment;
session_start();
require_once 'libraries/database.php';
require_once 'libraries/Models/Article.php';
require_once 'libraries/Models/Comment.php';
require_once 'libraries/Renderer.php'; 
require_once 'libraries/Http.php'; 
require_once 'libraries/Utils.php'; 

$modelArticle = new Article();
$modelComment = new Comment();

$error = [];

$article_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($article_id === NULL || $article_id === false) {
    $error['article_id'] = "Le parametre id  est invalide.";
}

$article = $modelArticle->findById($article_id);

// echo "<pre>";
// print_r($article);
// echo "</pre>";

$commentaires  =$modelComment ->findAll($article_id);
// / 1--On affiche le titre autre

$pageTitle = 'Accueil des articles';
Renderer::render('articles/show',compact('article','commentaires','article_id'));

