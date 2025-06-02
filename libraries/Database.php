<?php

namespace Libraries;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    // Valeurs par défaut encapsulées dans la classe
    private const DEFAULT_CONFIG = [
        'DB_SERVERNAME' => '127.0.0.1',
        'DB_USERNAME'   => 'valet',
        'DB_PASSWORD'   => 'valet',
        'DB_DATABASE'   => 'blog-cfpc'
    ];

    /**
     * Retourne la connexion PDO (singleton)
     *
     * @return PDO
     */
    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    sprintf(
                        "mysql:host=%s;dbname=%s;charset=utf8",
                        self::getConfigValue('DB_SERVERNAME'),
                        self::getConfigValue('DB_DATABASE')
                    ),
                    self::getConfigValue('DB_USERNAME'),
                    self::getConfigValue('DB_PASSWORD')
                );
                
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw new \RuntimeException("Database connection error", 0, $e);
            }
        }
        return self::$pdo;
    }

    /**
     * Récupère une valeur de configuration avec fallback
     * 
     * @param string $key
     * @return mixed
     */
    private static function getConfigValue(string $key)
    {
        return defined($key) ? constant($key) : self::DEFAULT_CONFIG[$key];
    }

    /**
     * Récupère l'ID utilisateur d'un commentaire
     *
     * @param int $comment_id
     * @return int|null
     */
    public static function getCommentUserId(int $comment_id): ?int
    {
        $pdo = self::getPdo();
        $query = $pdo->prepare('SELECT user_id FROM comments WHERE id = :comment_id');
        $query->execute(['comment_id' => $comment_id]);
        
        return ($comment = $query->fetch()) ? (int)$comment['user_id'] : null;
    }
}
