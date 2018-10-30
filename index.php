<?php

    # Règles SEO
    $page = "Un choix hallucinant de produits";
    $seo_description = "Un choix très large de produits assemblés en France par des travailleurs non déclarés.";

    require_once("inc/header.php");

    $result_cat = $pdo->query("SELECT DISTINCT(categorie) FROM produit");
    $categories = $result_cat->fetchAll();

    // debug($categories);

    if(isset($_GET['m']) && $_GET['m'] == "delete"){
      $msg .= "<div class='alert alert-warning' style='text-align:center;'>Votre profil est bien supprimé, nous espèrons vous revoir bientôt!</div>";
    }

    if(isset($_GET['cat']) && $_GET['cat'] != "all")
    {
      $result = $pdo->prepare('SELECT * FROM produit WHERE categorie = :categorie');
      $result->bindValue(":categorie", $_GET['cat'], PDO::PARAM_STR);

      $result->execute();

      $produits = $result->fetchAll();
    }
    else 
    {
      $result_all = $pdo->query('SELECT * FROM produit');
      $produits = $result_all->fetchAll();
    }

    // debug ($produits);

?>

      <div class="starter-template">
        <h1><?= $page ?></h1>
        <p class="lead">Profitez de nos super prix toute l'année !</p>
      </div>
      <?= $msg ?>
      <div class="row">
        <div class="col">
          <a href="index.php" class="titre">Eshop.com</a>
        </div>
        <div class="col">
          <a class="index" href="?cat=all">Tous</a>
        </div>
        <div class="col">
          <?php foreach($categories as $categorie) : ?>
                  <a class="index" href="?cat=<?= $categorie['categorie'] ?>">
                    <?= $categorie['categorie'] ?>
                  </a>
              <?php endforeach; ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-9">
          <div class="card-columns">
            <?php foreach($produits as $produit) : ?>
              <div class="card">
                <img class="card-img-top" src="<?= URL ?>assets/uploads/admin/<?=$produit['photo']?>" alt="<?=$produit['titre']?>">
                <div class="card-body">
                  <h5 class="card-title"><?= $produit['titre'] ?></h5>
                  <p class='card-text'><?= $produit['description'] ?></p>
                  <h6><?= $produit['prix'] ?> €</h6>
                  <a href="page_produit.php?id=<?= $produit['id_produit'] ?>" class="btn btn-primary">Voir le produit</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

<?php require_once("inc/footer.php"); ?>