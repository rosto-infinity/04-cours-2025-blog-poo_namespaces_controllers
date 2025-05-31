<?php
namespace Libraries\Models;

require_once "libraries/Models/Model.php";

class Comment extends Model
{
    protected function getTableName(): string
    {
        return 'comments';
    }

    public function findAll(int $article_id): array
    {
        $sql = "SELECT comments.*, users.username 
                FROM comments
                JOIN users ON comments.user_id = users.id
                WHERE article_id = :article_id";
        $query = $this->pdo->prepare($sql);
        $query->execute(["article_id" => $article_id]);
        return $query->fetchAll();
    }

    public function getCommentAuthorId(int $comment_id): ?int
    {
        $query = $this->pdo->prepare('SELECT user_id FROM comments WHERE id = :comment_id');
        $query->execute(['comment_id' => $comment_id]);
        $comment = $query->fetch();
        return $comment ? (int)$comment['user_id'] : null;
    }

    public function insert(string $content, int $article_id, int $user_auth): bool
    {
        $query = $this->pdo->prepare("INSERT INTO comments (content, article_id, user_id, created_at)
                                      VALUES (:content, :article_id, :user_auth, NOW())");
        return $query->execute([
            'content' => $content,
            'article_id' => $article_id,
            'user_auth' => $user_auth
        ]);
    }
    public function getUserByEmailOrUsername(string $emailOrUsername): ?array {
 
        $query = $this->pdo->prepare("SELECT * FROM users WHERE email = :identifier OR username = :identifier");
        $query->execute(['identifier' => $emailOrUsername]);
        $user = $query->fetch();
        return $user ?: null;
      }
}
