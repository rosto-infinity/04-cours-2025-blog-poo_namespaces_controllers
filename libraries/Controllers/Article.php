<?php
namespace Libraries\Controllers;

use Libraries\Utils;
use JasonGrimes\Paginator;



class Article extends Controller
{
    public function index()
    {
        $modelArticle = new \Libraries\Models\Article();
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 6;

        $articlesByPaginator = $modelArticle->findByPaginator($currentPage, $itemsPerPage);
        $totalItems = $modelArticle->countArticles();

        $paginator = new Paginator(
            $totalItems,
            $itemsPerPage,
            $currentPage,
            '?page=(:num)'
        );

        $pageTitle = 'Accueil du Blog';
        $this->render('articles/index', compact('pageTitle', 'articlesByPaginator', 'paginator'));
    }

    public function show()
    {
        $modelArticle = new \Libraries\Models\Article();
        $modelComment = new \Libraries\Models\Comment();

        $error = [];

        $article_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($article_id === null || $article_id === false) {
            $error['article_id'] = "Le paramètre 'id' est invalide.";
            $this->render('errors/404', compact('error'));
            return;
        }

        $article = $modelArticle->findById($article_id);
        if (!$article) {
            $error['article'] = "L'article demandé n'existe pas.";
            $this->render('errors/404', compact('error'));
            return;
        }

        $commentaires = $modelComment->findAll($article_id);
        $pageTitle = $article['title'] ?? 'Article';

        $this->render('articles/show', compact('article', 'commentaires', 'article_id', 'pageTitle'));
    }

    public function delete()
    {
        $modelArticle = new \Libraries\Models\Article();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id === false) {
            $this->redirect("error.php?message=Id de l'article non valide.");
        }

        $article = $modelArticle->findById($id);

        if (!$article) {
            $this->redirect("error.php?message=L'article $id n'existe pas, vous ne pouvez donc pas le supprimer !");
        }

        $modelArticle->deleteById($id);
        $this->redirect("admin.php");
    }

    public function update()
    {
        $modelArticle = new \Libraries\Models\Article();

        $error = "";
        $success = "";
        $article = [];
        $currentImage = null;

        function clean_input($data)
        {
            return htmlspecialchars(stripslashes(trim($data)));
        }

        $articleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
            $article = $modelArticle->findById($articleId);
            $title = $article['title'] ?? "";
            $slug = $article['slug'] ?? "";
            $introduction = $article['introduction'] ?? "";
            $content = $article['content'] ?? "";
            $currentImage = $article['image'] ?? null;
        }

        if (isset($_POST['update'])) {
            $articleId = clean_input($_POST['id']);
            $title = clean_input(filter_input(INPUT_POST, 'title', FILTER_DEFAULT));
            $slug = strtolower(str_replace(' ', '-', $title));
            $introduction = clean_input(filter_input(INPUT_POST, 'introduction', FILTER_DEFAULT));
            $content = clean_input(filter_input(INPUT_POST, 'content', FILTER_DEFAULT));
            $articleId = clean_input(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));

            if (isset($_FILES['a_image']) && $_FILES['a_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/articles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = strtolower(pathinfo($_FILES['a_image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($extension, $allowedExtensions)) {
                    if ($currentImage && file_exists($currentImage)) {
                        unlink($currentImage);
                    }

                    $filename = uniqid('article_') . '.' . $extension;
                    $destination = $uploadDir . $filename;

                    if (move_uploaded_file($_FILES['a_image']['tmp_name'], $destination)) {
                        $currentImage = $destination;
                    } else {
                        $error = "Erreur lors du téléchargement de la nouvelle image";
                    }
                } else {
                    $error = "Format d'image non supporté. Utilisez JPG, PNG, GIF ou WEBP.";
                }
            }

            if (empty($title) || empty($slug) || empty($introduction) || empty($content)) {
                $error = $error ?: "Veuillez remplir tous les champs obligatoires du formulaire !";
            } else {
                $update = $modelArticle->update(
                    $articleId,
                    $title,
                    $slug,
                    $introduction,
                    $content,
                    $currentImage ?? ''
                );
                if (!$update) {
                    $success = "Article mis à jour avec succès!";
                    $article = $modelArticle->findById($articleId);
                    $currentImage = $article['image'] ?? null;
                } else {
                    $error = $error ?: "Aucune modification détectée ou erreur lors de la mise à jour";
                }
            }
            $this->redirect("admin.php");
        }

        $pageTitle = 'Éditer un article';
        $this->render('articles/edit-article', compact('title', 'slug', 'pageTitle', 'articleId', 'introduction', 'content', 'error', 'success'));
    }

    public function insert()
    {
        $modelArticle = new \Libraries\Models\Article();
        $error = "";
        $success = "";

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('index.php');
        }

        if (isset($_POST['add-article'])) {
            try {
                $title = Utils::cleanInput($_POST['title']);
                $slug = Utils::createSlug($title);
                $introduction = Utils::cleanInput($_POST['introduction']);
                $content = Utils::cleanInput($_POST['content']);
                $imagePath = null;

                $imagePath = $this->handleImageUpload($_FILES['image'] ?? null);

                if (empty($title) || empty($introduction) || empty($content)) {
                    throw new \Exception("Tous les champs obligatoires doivent être remplis");
                }

                if ($modelArticle->insert($title, $slug, $introduction, $content, $imagePath)) {
                    $success = "Article créé avec succès!";
                } else {
                    throw new \Exception("Erreur lors de la création de l'article");
                }

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        $allArticles = $modelArticle->findAll();

        $this->render('adminfghghhjfhf/admin_dashboardgfdgdqsfqqssqs', [
            'allArticles' => $allArticles,
            'pageTitle' => 'Tableau de bord Admin',
            'error' => $error,
            'success' => $success
        ]);
    }

    private function handleImageUpload(?array $file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = 'uploads/articles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mime, $allowedMimes)) {
            throw new \Exception("Format d'image non supporté");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('article_') . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception("Erreur lors du téléchargement de l'image");
        }

        return $destination;
    }
}
