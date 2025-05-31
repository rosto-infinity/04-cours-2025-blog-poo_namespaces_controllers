<?php
use Libraries\Http;
use Libraries\Renderer;
use Libraries\Models\User;

session_start();
require_once 'libraries/database.php';
require_once 'libraries/Renderer.php'; 
require_once 'libraries/Http.php'; 
require_once 'libraries/Utils.php'; 

require_once 'libraries/Models/User.php';

$modelUser = new User();

$errors = [];
if (isset($_POST['register'])) {

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation du pseudo
    if (empty($username) || !preg_match("#^[a-zA-Z0-9_]+$#", $username)) {
        $errors['username'] = "Pseudo non valide";
    } else {
        try {
            if ($modelUser->existsByField('username', $username)) {
                $errors['username'] = "Ce pseudo n'est plus disponible";
            }
        } catch (InvalidArgumentException $e) {
            $errors['username'] = "Erreur lors de la vérification du pseudo.";
        }
    }

    // Validation de l'email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email non valide";
    } else {
        try {
            if ($modelUser->existsByField('email', $email)) {
                $errors['email'] = "Cet email est déjà pris";
            }
        } catch (InvalidArgumentException $e) {
            $errors['email'] = "Erreur lors de la vérification de l'email.";
        }
    }

    // Validation du mot de passe
    if (empty($password)) {
        $errors['password'] = "Vous devez entrer un mot de passe";
    } elseif ($password !== $confirm_password) {
        $errors['password'] = "Votre mot de passe ne correspond pas !";
    }

    // Insertion dans la base de données si aucune erreur
    if (empty($errors)) {
        if ($modelUser->insert($username, $email, $password)) {
            Http::redirect('login.php');
        } else {
            $errors['general'] = "Une erreur est survenue lors de l'inscription.";
        }
    }
}

// Titre de la page
$pageTitle = "S'inscrire dans le Blog";
Renderer::render('articles/register');
