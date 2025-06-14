<h1><u>Gestions des articles</u></h1>
<!-- Contenu spécifique à l'admin -->
<h3>Hello <?= isset($_SESSION["auth"]['username']) ? $_SESSION["auth"]['username'] : "" ?></h3>

<div class="admin">

  <div class="article adm">
    <span style='color:#FF6600 ; font-size:4rem; text-align:center; font-weight: 700;'>Adminitrateur : </span>
  </div>

  <?php
  if (isset($error)) {
    echo "<p style='color:red; font-size : 1.5rem ; width:500px'>$error</p>";
  }elseif(isset($success))
  {
    echo "<p style='color: white;padding:10px; text-align:center; background:#green; width:400px'>$success</p>";
  }
  ?>
  <h1>Ajouter un nouvel article</h1>
 
  

  <form class="form" id="form" method="post" enctype="multipart/form-data" action="admin.php">
    <div class="form-control">
      <label for="title">Title:</label>
      <input type="text" name="title" id="title">
    </div>
    <div class="form-control" hidden>
      <label for="slug">Slug:</label>
      <input type="text" name="slug" id="slug">
    </div>
    <div class="form-control">
      <label for="introduction">Introduction:</label>
      <textarea name="introduction" id="introduction"></textarea>
    </div>

    <div class="form-control">
      <label for="content">Content:</label>
      <textarea name="content" id="content"></textarea>
    </div>

    <div class="form-control">
        <label for="image">Image de l'article:</label>
        <input type="file" name="image" id="image" accept="image/*">
    </div>
    <div class="form-control">
      <button type="submit" name="add-article" value="add-article">Ajouter</button>
    </div>
  </form>
</div>



<h1>Nos articles</h1>
<p>Il y a <?= count($allArticles); ?> articles</p>

<div class="article-grid">
  <?php foreach ($allArticles as $article) : ?>
    <div class="article">
    <?php if (!empty($article['image'])) : ?>
        <img src="<?= $article['image'] ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="article-image">
      <?php endif; ?>
      <h2><?= $article['title'] ?></h2>
      <p><?= $article['introduction'] ?></p>
      <small> Ecrit le<?= $article['created_at'] ?> </small> <br />
      <a href="article.php?id=<?= urlencode($article['id']); ?>">voir</a>
      <a href="edit-article.php?id=<?= urlencode($article['id']); ?>">Éditer</a>
      <a href="delete-article.php?id=<?= urlencode($article['id']); ?>"
        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?!')">Supprimer</a>
    </div>
  <?php endforeach ?>
</div>
</div>

</div>
