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

  public function delete()
  {
    $modelComment = new \Libraries\Models\Comment();
if (!isset($_SESSION['auth'])) {
    Http::redirect('login.php');
   
}

$user_id = $_SESSION['auth']['id'];
$comment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($comment_id === null || $comment_id === false) {
    die('ID de commentaire invalide.');
}

// Vérifier si le commentaire appartient à l'utilisateur connecté

$commentAuthorId =$modelComment -> getCommentAuthorId($comment_id);

if (isset($_SESSION['auth']) && $_SESSION['auth']['id'] === $commentAuthorId) {
    // L'utilisateur est autorisé à supprimer ce commentaire
    // -Supprimer le commentaire
    // Code de suppression ici
    $modelComment -> deleteById($comment_id);
}else {
    // L'utilisateur n'est pas autorisé à supprimer ce commentaire
    // Afficher un message d'erreur ou rediriger
    die('Vous ne pouvez pas supprimer ce commentaire.');
}

// header('Location: article.php?id=' . $_GET['article_id']);
// exit;
Http::redirect("Location: article.php?id=" . $_GET['article_id']);


}
}
