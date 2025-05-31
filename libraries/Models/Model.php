<?php
namespace Libraries\Models;

require_once "libraries/database.php";

abstract class Model
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = getPdo();
    }

    // Méthode abstraite pour forcer les classes enfants à implémenter une méthode spécifique
    abstract protected function getTableName(): string;

  

    // Méthode générique pour trouver un enregistrement par ID
    public function findById(int $id): ?array
    {
        $query = $this->pdo->prepare("SELECT * FROM " . $this->getTableName() . " WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->fetch() ?: null;
    }

    // Méthode générique pour supprimer un enregistrement par ID
    public function deleteById(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM " . $this->getTableName() . " WHERE id = :id");
        return $query->execute(['id' => $id]);
    }
}
