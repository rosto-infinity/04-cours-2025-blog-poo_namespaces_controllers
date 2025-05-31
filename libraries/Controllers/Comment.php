<?php

namespace Libraries\Controllers;

use Libraries\Http;

session_start();
require_once 'libraries/database.php';
require_once 'libraries/Models/Comment.php';
require_once 'libraries/Renderer.php';
require_once 'libraries/Http.php';
require_once 'libraries/Utils.php';

require_once 'libraries/Models/Comment.php';
class Comment
{
  public function save()
  {
    $modelComment = new  \Libraries\Models\Comment();

    if (!isset($_SESSION['auth']['id'])) {
      // header("Location: login.php");
      // exit;
      Http::redirect(" login.php");
    }


    $user_auth = $_SESSION['auth']['id'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {


      $content = htmlspecialchars($_POST['content'] ?? null);
      $article_id = $_POST['article_id'] ?? null;

      $modelComment->insert($content, $article_id, $user_auth);

      //Rediriger vers la pages des articles apre l'ajout du commentaire

      Http::redirect("article.php?id=" . $article_id);
    }
  }
}
