<?php
namespace Libraries\Controllers;

use Libraries\Renderer;


session_start();
require_once 'libraries/database.php';
require_once 'libraries/Models/User.php';
require_once 'libraries/Renderer.php'; 
require_once 'libraries/Http.php'; 
require_once 'libraries/Utils.php'; 

class User {

  public function login() {

$modelUser = new \Libraries\Models\User();
$errors = [];

if (isset($_POST['login'])) {
    $identifier = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($identifier) && !empty($password)) {
        $user = $modelUser->getUserByEmailOrUsername( $identifier);

        if ($user && $modelUser->authenticateUser($user, $password)) {
            $_SESSION['role'] = $user['role'];
            $_SESSION['auth'] = $user;

            // Redirection en fonction du rôle
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin.php");
                    break;
                default:
                    header("Location: user.php");
                    break;
            }
            exit();
        } else {
            $errors['email'] = "Email ou mot de passe incorrect.";
        }
    } else {
        $errors['login'] = "Tous les champs doivent être remplis.";
    }
}

// Définir le titre de la page
$pageTitle = "Se connecter dans le Blog";

// Afficher le formulaire de connexion avec les erreurs éventuelles
Renderer::render('articles/login', [
  'errors' => $errors,
  'pageTitle' => $pageTitle  
]);

  }
}
