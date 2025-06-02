<?php
namespace Libraries\Controllers;

session_start();
use Libraries\Http;
use Libraries\Renderer;
use InvalidArgumentException;


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

  public function logout() {
  
session_unset(); // -Détruire toutes les variables de session
session_destroy(); // Détruire la session

Http::redirect('index.php'); // Rediriger vers la page de connexion


   }

   public function register() {
    $modelUser = new \Libraries\Models\User();
  

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
Renderer::render('articles/register',['pageTitle' =>$pageTitle ]);

   }
}






// <?php
// namespace Libraries\Controllers;

// use Libraries\Http;
// use Libraries\Renderer;
// use InvalidArgumentException;

// require_once 'libraries/Controllers/Controller.php';
// require_once 'libraries/database.php';
// require_once 'libraries/Models/User.php';
// require_once 'libraries/Renderer.php'; 
// require_once 'libraries/Http.php'; 
// require_once 'libraries/Utils.php'; 

// class User extends Controller
// {
//     public function login()
//     {
//         $modelUser = new \Libraries\Models\User();
//         $errors = [];

//         if (isset($_POST['login'])) {
//             $identifier = $_POST['email'] ?? '';
//             $password = $_POST['password'] ?? '';

//             if (!empty($identifier) && !empty($password)) {
//                 $user = $modelUser->getUserByEmailOrUsername($identifier);

//                 if ($user && $modelUser->authenticateUser($user, $password)) {
//                     $_SESSION['role'] = $user['role'];
//                     $_SESSION['auth'] = $user;

//                     // Redirection en fonction du rôle
//                     switch ($user['role']) {
//                         case 'admin':
//                             $this->redirect("admin.php");
//                             break;
//                         default:
//                             $this->redirect("user.php");
//                             break;
//                     }
//                     exit();
//                 } else {
//                     $errors['email'] = "Email ou mot de passe incorrect.";
//                 }
//             } else {
//                 $errors['login'] = "Tous les champs doivent être remplis.";
//             }
//         }

//         // Définir le titre de la page
//         $pageTitle = "Se connecter dans le Blog";

//         // Afficher le formulaire de connexion avec les erreurs éventuelles
//         $this->render('articles/login', [
//             'errors' => $errors,
//             'pageTitle' => $pageTitle  
//         ]);
//     }

//     public function logout()
//     {
//         session_unset(); // Détruire toutes les variables de session
//         session_destroy(); // Détruire la session

//         $this->redirect('index.php'); // Rediriger vers la page d'accueil
//     }

//     public function register()
//     {
//         $modelUser = new \Libraries\Models\User();
//         $errors = [];

//         if (isset($_POST['register'])) {
//             $username = $_POST['username'] ?? '';
//             $email = $_POST['email'] ?? '';
//             $password = $_POST['password'] ?? '';
//             $confirm_password = $_POST['confirm_password'] ?? '';

//             // Validation du pseudo
//             if (empty($username) || !preg_match("#^[a-zA-Z0-9_]+$#", $username)) {
//                 $errors['username'] = "Pseudo non valide";
//             } else {
//                 try {
//                     if ($modelUser->existsByField('username', $username)) {
//                         $errors['username'] = "Ce pseudo n'est plus disponible";
//                     }
//                 } catch (InvalidArgumentException $e) {
//                     $errors['username'] = "Erreur lors de la vérification du pseudo.";
//                 }
//             }

//             // Validation de l'email
//             if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
//                 $errors['email'] = "Email non valide";
//             } else {
//                 try {
//                     if ($modelUser->existsByField('email', $email)) {
//                         $errors['email'] = "Cet email est déjà pris";
//                     }
//                 } catch (InvalidArgumentException $e) {
//                     $errors['email'] = "Erreur lors de la vérification de l'email.";
//                 }
//             }

//             // Validation du mot de passe
//             if (empty($password)) {
//                 $errors['password'] = "Vous devez entrer un mot de passe";
//             } elseif ($password !== $confirm_password) {
//                 $errors['password'] = "Votre mot de passe ne correspond pas !";
//             }

//             // Insertion dans la base de données si aucune erreur
//             if (empty($errors)) {
//                 if ($modelUser->insert($username, $email, $password)) {
//                     $this->redirect('login.php');
//                 } else {
//                     $errors['general'] = "Une erreur est survenue lors de l'inscription.";
//                 }
//             }
//         }

//         // Titre de la page
//         $pageTitle = "S'inscrire dans le Blog";
//         $this->render('articles/register', [
//             'pageTitle' => $pageTitle,
//             'errors' => $errors
//         ]);
//     }
// }
