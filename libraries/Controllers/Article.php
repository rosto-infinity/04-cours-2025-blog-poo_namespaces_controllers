<?php

namespace Libraries\Controllers;

use Libraries\Http;
use Libraries\Utils;
use Libraries\Renderer;
use JasonGrimes\Paginator;


require_once 'libraries/Renderer.php'; 
require_once 'libraries/Http.php'; 
require_once 'libraries/Utils.php'; 
session_start();
require_once 'vendor/autoload.php';
require_once 'libraries/database.php';
require_once 'libraries/Models/Article.php';


class Article
{

  public function index()
  {

    $modelArticle = new \Libraries\Models\Article();
    
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $itemsPerPage = 6;

    // Récupération des articles pour la page actuelle
    $articlesByPaginator = $modelArticle->findByPaginator($currentPage, $itemsPerPage);

    // Calcul du nombre total d'articles
    $totalItems = $modelArticle->countArticles();

    // Initialisation du paginator
    $paginator = new Paginator(
      $totalItems,
      $itemsPerPage,
      $currentPage,
      '?page=(:num)'
    );

    // Titre de la page
    $pageTitle = 'Accueil du Blog';

    // Rendu de la vue
    Renderer::render('articles/index', compact('pageTitle', 'articlesByPaginator', 'paginator'));
  }
  public function show()
  {
    //Montrer un article

  }
  public function delete()
  {
    //Supprimer un article

    $modelArticle = new \Libraries\Models\Article();

    // 1. Vérification de l'ID passé en GET
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === false) {
      Http::redirect("Location: error.php?message=Id de l'article non valide.");
    }

    $article = $modelArticle->findById($id);

    if (!$article) {
      Http::redirect(" error.php?message=L'article $id n'existe pas, vous ne pouvez donc pas le supprimer !");
    }

    // 3.- Suppression de l'article
    $modelArticle->deleteById($id);

    // 4.- Redirection vers la page d'accueil
    Http::redirect("admin.php");
  }

  public function update()
  {
    $modelArticle = new \Libraries\Models\Article();

    $error = "";
    $success = "";
    $article = []; // Initialisation de la variable article
    $currentImage = null; // Initialisation explicite
    // --tVérification et nettoyage des entrées
    function clean_input($data)
    {
      return htmlspecialchars(stripslashes(trim($data)));
    }
    /**
     * Éditer un article existant
     */

    $articleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    // Récupération des informations d'un article à modifier
    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {

      $article = $modelArticle->findById($articleId);

      // Récupération des données APRÈS la requête
      $title = $article['title'] ?? "";
      $slug = $article['slug'] ?? "";
      $introduction = $article['introduction'] ?? "";
      $content = $article['content'] ?? "";
      $currentImage = $article['image'] ?? null; // Utilisez 'image' ou 'a_image' selon votre BDD
    }

    // Traitement de la soumission du formulaire
    if (isset($_POST['update'])) {
      // Récupération de l'ID et nettoyage
      $articleId = clean_input($_POST['id']);

      // ----Nettoyage des entrées
      $title = clean_input(filter_input(INPUT_POST, 'title', FILTER_DEFAULT));
      $slug = strtolower(str_replace(' ', '-', $title)); // Mise à jour du slug à partir du titre
      $introduction = clean_input(filter_input(INPUT_POST, 'introduction', FILTER_DEFAULT));
      $content = clean_input(filter_input(INPUT_POST, 'content', FILTER_DEFAULT));
      $articleId = clean_input(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));

      // Traitement de l'image uploadée
      if (isset($_FILES['a_image']) && $_FILES['a_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/articles/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($_FILES['a_image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($extension, $allowedExtensions)) {
          // Suppression de l'ancienne image si elle existe
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

      // Validation des données
      if (empty($title) || empty($slug) || empty($introduction) || empty($content)) {
        $error = $error ?: "Veuillez remplir tous les champs obligatoires du formulaire !";
      } else {
        // Mise à jour de l'article dans la base de données

        $update = $modelArticle->update(
          $articleId,
          $title,
          $slug,
          $introduction,
          $content,
          $currentImage ?? '' // Fournit une chaîne vide si null
        );
        if (!$update) {
          $success = "Article mis à jour avec succès!";
          // Rafraîchir les données
          $article = $modelArticle->findById($articleId);

          $currentImage = $article['image'] ?? null;
        } else {
          $error = $error ?: "Aucune modification détectée ou erreur lors de la mise à jour";
        }
      }
    
      Http::redirect("admin.php");
    }

    $pageTitle = 'Éditer un article';
    Renderer::render('articles/edit-article', compact('title', 'slug', 'pageTitle', 'articleId', 'introduction', 'content', 'error', 'success'));
  }
  public function insert()
{
    // Initialisation
    $modelArticle = new \Libraries\Models\Article();
    $error = "";
    $success = "";

    // Vérification des permissions
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        Http::redirect('index.php');
    }

    // Traitement du formulaire
    if (isset($_POST['add-article'])) {
        try {
            // Nettoyage des entrées
            $title = Utils::cleanInput($_POST['title']);
            $slug = Utils::createSlug($title);
            $introduction = Utils::cleanInput($_POST['introduction']);
            $content = Utils::cleanInput($_POST['content']);
            $imagePath = null;

            // Gestion de l'image
            $imagePath = $this->handleImageUpload($_FILES['image'] ?? null);

            // Validation
            if (empty($title) || empty($introduction) || empty($content)) {
                throw new \Exception("Tous les champs obligatoires doivent être remplis");
            }

            // Insertion
            if ($modelArticle->insert($title, $slug, $introduction, $content, $imagePath)) {
                $success = "Article créé avec succès!";
            } else {
                throw new \Exception("Erreur lors de la création de l'article");
            }

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }

    // Récupération des articles
    $allArticles = $modelArticle->findAll();

    // Affichage
    Renderer::render('adminfghghhjfhf/admin_dashboardgfdgdqsfqqssqs', [
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

    // Vérification du type MIME réel
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
// Récupération de tous les articles avec gestion des images

    return $destination;
}

}
