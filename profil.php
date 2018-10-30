<?php

    # Règles SEO
    $page = "Mon profil";
    $seo_description = "Regardez votre profil qui est sublime, magnifique, vous êtes une star !";

    require_once("inc/header.php");

    if(!userConnect())
    {
        header("location:connexion.php");
        exit(); // die() fonctionne aussi
    }

    if(!empty($_FILES['photo']['name']))
    {
    // $photo = uniqid(md5($_FILES['photo']['name']));
        // si on fait comme ça on perd l'extension du fichier

        $photo = $_POST['pseudo'] .'_' . time() . '-' . rand(1,999) . '_' . $_FILES['photo']['name'];
            // sert à donner un nom unique à la photo

        $photo = str_replace(' ', '-', $photo);
            // je m'assure qu'il n'y a pas d'espaces entre les mots

        copy($_FILES['photo']['tmp_name'], "/assets/uploads/user/" . $photo);
            // permet de copier/coller un fichier : endroit du fichier+ endroit ciblé 
            
    } else {
    $photo = "/assets/uploads/user/default.png";
    }

    // debug($_SESSION, 2);
    foreach($_SESSION['user'] as $key => $value)
    {
        $info[$key] = htmlspecialchars($value); # nous vérifions que les informations à afficher ne comporte pas d'injections et ne perturberont pas notre service
    }

    debug($_SESSION);

    if(isset($_GET['membre']) && $_GET['membre'] == 'delete') {

        $req = "SELECT * FROM membre WHERE id_membre = :id";

        $result = $pdo->prepare($req);
        $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $result->execute();

        if($result->rowCount() == 1)
        {
            $user = $result->fetch();

            $resultat = $pdo -> exec("DELETE FROM membre WHERE id_membre = $user[id_membre]");
    
            unset($_SESSION['user']);
            header('location:index.php?m=delete');
        }
    }

?>

    <div class="starter-template">
        <h1><?= $page ?></h1>
        <div class="card">
            <img class="card-img-top img-thumbnail rounded mx-auto d-block" src="<?=URL?>/assets/uploads/user/<?=$info['photo']?>" alt="<?= $info['pseudo'] ?>" style="width:25%;">
            <div class="card-body">
                <h5 class="card-title">Bonjour <?= $info['pseudo'] ?></h5>
                <p class="card-text">Nous sommes râvi de vous revoir sur notre plateforme.</p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Prénom: <?= $info['prenom'] ?></li>
                <li class="list-group-item">Nom: <?= $info['nom'] ?></li>
                <li class="list-group-item">Email: <?= $info['email'] ?></li>

                <li class="list-group-item">Civilité: <?php switch($info['civilite']){case "m": echo "homme"; break; case "f": echo "femme"; break; default: echo "Non défini"; break;} ?></li>
                
                <li class="list-group-item">Adresse: <?= $info['adresse'] ?></li>
                <li class="list-group-item">Code postal: <?= $info['code_postal'] ?></li>
                <li class="list-group-item">Ville: <?= $info['ville'] ?></li>
            </ul>
            <div class="card-body">
                <a href="inscription.php?a=modifier&id=<?= $info['id_membre']?>" class="card-link">Modifier</a>
                <a href="?membre=delete&id=<?= $info['id_membre']?>" class="card-link">Supprimer</a>
            </div>
        </div>
    </div>

<?php require_once("inc/footer.php"); ?>