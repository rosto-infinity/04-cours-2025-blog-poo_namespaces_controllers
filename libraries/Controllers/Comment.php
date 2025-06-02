<?php                                                     
namespace Libraries\Controllers;

require_once 'libraries/Controllers/Controller.php';
require_once 'libraries/database.php';


class Comment extends Controller
{
    public function save()
    {
        $modelComment = new \Libraries\Models\Comment();

        if (!$this->isAuthenticated()) {
            $this->redirect("login.php");
        }

        $user_auth = $_SESSION['auth']['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = htmlspecialchars($_POST['content'] ?? null);
            $article_id = $_POST['article_id'] ?? null;

            $modelComment->insert($content, $article_id, $user_auth);

            // Rediriger vers la page de l'article après l'ajout du commentaire
            $this->redirect("article.php?id=" . $article_id);
        }
    }

    public function delete()
    {
        $modelComment = new \Libraries\Models\Comment();

        if (!$this->isAuthenticated()) {
            $this->redirect('login.php');
        }

        $user_id = $_SESSION['auth']['id'];
        $comment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($comment_id === null || $comment_id === false) {
            die('ID de commentaire invalide.');
        }

        // Vérifier si le commentaire appartient à l'utilisateur connecté
        $commentAuthorId = $modelComment->getCommentAuthorId($comment_id);

        if ($user_id === $commentAuthorId) {
            $modelComment->deleteById($comment_id);
        } else {
            die('Vous ne pouvez pas supprimer ce commentaire.');
        }

        $this->redirect("article.php?id=" . $_GET['article_id']);
    }
}
