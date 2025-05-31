<?php
namespace Libraries\Models;

use PDO;
use Libraries\Models\Model;
require_once "libraries/Models/Model.php";

class Article extends Model
{
    protected function getTableName(): string
    {
        return 'articles';
    }

    public function countArticles(): int
    {
        $sql = "SELECT COUNT(*) FROM articles";
        $stmt = $this->pdo->query($sql);
        return (int) $stmt->fetchColumn();
    }
    public function findAll()
    {
     
      // Récupération de tous les articles avec gestion des images
      $query = "SELECT *
  FROM articles ORDER BY created_at DESC";
  
      $resultats = $this->pdo->prepare($query);
      $resultats->execute();
      $articles = $resultats->fetchAll();
      return $articles;
    }
    public function findByPaginator(int $currentPage = 1, int $itemsPerPage = 3): array
    {
        $offset = ($currentPage - 1) * $itemsPerPage;
        $sql = "SELECT articles.id, articles.title, articles.introduction, articles.created_at, articles.image, 
                       (SELECT COUNT(*) FROM comments WHERE comments.article_id = articles.id) AS comment_count
                FROM articles
                ORDER BY articles.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $articleId, string $title, string $slug, string $introduction, string $content,  ?string $currentImage = null ): bool
    {
        $sql = "UPDATE articles SET 
                    title = :title, 
                    slug = :slug, 
                    introduction = :introduction, 
                    content = :content,
                    image = :image,
                    updated_at = NOW()
                WHERE id = :articleId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'title' => $title,
            'slug' => $slug,
            'introduction' => $introduction,
            'content' => $content,
            'image' => $currentImage,
            'articleId' => $articleId
        ]);
    }
public function insert($title, $slug, $introduction, $content, $imagePath)
    {
        $query = $this->pdo->prepare('INSERT INTO articles 
            (title, slug, introduction, content, image, created_at) 
            VALUES (:title, :slug, :introduction, :content, :image, NOW())');
$insertArticle = $query->execute([
            'title' => $title,
            'slug' => $slug,
            'introduction' => $introduction,
            'content' => $content,
            'image' => $imagePath
        ]);
        return  $insertArticle;
    }

    public function slugExists($slug)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM articles WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchColumn() > 0;
    }


}
