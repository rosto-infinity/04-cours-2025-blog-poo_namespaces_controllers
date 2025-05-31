<?php
namespace Libraries\Models;

use InvalidArgumentException;

require_once "libraries/Models/Model.php";
class User extends Model
{
    protected function getTableName(): string
    {
        return 'users';
    }

    /**
     * Vérifie si une valeur existe pour un champ donné dans la table users.
     *
     * @param string $field Le nom du champ (par exemple, 'username' ou 'email').
     * @param string $value La valeur à vérifier.
     * @return bool Retourne true si la valeur existe, sinon false.
     */
    public function existsByField(string $field, string $value): bool
    {
        $allowedFields = ['username', 'email'];
        if (!in_array($field, $allowedFields)) {
            throw new InvalidArgumentException("Champ non autorisé : $field");
        }

        $query = "SELECT COUNT(*) FROM {$this->getTableName()} WHERE $field = :value";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['value' => $value]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Insère un nouvel utilisateur dans la base de données.
     *
     * @param string $username Le nom d'utilisateur.
     * @param string $email L'adresse e-mail.
     * @param string $password Le mot de passe en clair.
     * @return bool Retourne true si l'insertion a réussi, sinon false.
     */
    public function insert(string $username, string $email, string $password): bool
    {
        $query = "INSERT INTO {$this->getTableName()} (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->pdo->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);
    }

    /**
     * Récupère un utilisateur par son e-mail ou nom d'utilisateur.
     *
     * @param string $identifier L'e-mail ou le nom d'utilisateur.
     * @return array|null Retourne les informations de l'utilisateur ou null si non trouvé.
     */
    public function getUserByEmailOrUsername(string $identifier): ?array
    {
        $query = "SELECT * FROM {$this->getTableName()} WHERE email = :identifier OR username = :identifier";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['identifier' => $identifier]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Authentifie un utilisateur en vérifiant son mot de passe.
     *
     * @param array $user Les données de l'utilisateur.
     * @param string $password Le mot de passe fourni.
     * @return bool Retourne true si le mot de passe est correct, sinon false.
     */
    public function authenticateUser(array $user, string $password): bool
    {
        return password_verify($password, $user['password']);
    }
}

