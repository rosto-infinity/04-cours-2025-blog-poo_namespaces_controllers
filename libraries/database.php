<?php

/**
 *Retourne la connxion de la DBase
 *
 *@return PDO
 */
function getPdo(): PDO
{
  // Définir les constantes pour la connexion à la base de données
  if (!defined('DB_SERVERNAME')) {
    define('DB_SERVERNAME', '127.0.0.1');
  }

  if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'valet'); // Utilisez 'root' si c'est votre utilisateur MySQL
  }

  if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', 'valet'); // Utilisez le mot de passe correct
  }

  if (!defined('DB_DATABASE')) {
    define('DB_DATABASE', 'blog-cfpc');
  }
  try {
    // Établir la connexion à la base de données
    $pdo = new PDO("mysql:host=" . DB_SERVERNAME . ";dbname=" . DB_DATABASE . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    // Configurer le mode d'erreur pour lancer des exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Message de succès
    echo "<div style='background-color:#3c763d; color:white;'>Connexion à la base de données réussie</div>";
  } catch (PDOException $e) {
    // Gérer les erreurs de connexion
    echo "<div style='color:red;'>La connexion à la base de données a échoué :</div> " . $e->getMessage();
  }
  return $pdo;
}

function getCommentUserId (int $comment_id): ?int {
  $pdo =getPdo();
  
  $query = $pdo->prepare('SELECT user_id FROM comments WHERE id = :comment_id');
  $query->execute(['comment_id' => $comment_id]);
  $comment = $query->fetch(PDO::FETCH_ASSOC);
  return $comment ? (int)$comment['user_id'] : null;
}
