<?php

    # Règles SEO
    $page = "Inscription";
    $seo_description = "Rejoignez le club des meilleures affaires en ligne: jusqu'à -80%";

    require_once("inc/header.php");

    // Dans le formulaire ...
    if($_POST)
    {

        # Vérification pseudo : code OK
        if(!empty($_POST['pseudo']))
        {
            $pseudo_verif = preg_match("#^[a-zA-Z0-9-._]{3,20}$#", $_POST['pseudo']);
            # Ici, nous allons utiliser une expression régulière (REGEX). Une REGEX nous permet de vérifier une condition.
            # la fonction preg_match() nous permet de vérifier si une variable respecte la REGEX rentrée. Elle prend 2 arguments : REGEX + le résultat à vérifier. Elle nous retourne un TRUE/FALSE

            if(!$pseudo_verif) # équivaut à dire $pseudo_verif est FALSE
            {
                $msg .= "<div class='alert alert-danger'>Votre pseudo doit contenir des lettres (minuscules ou majuscules), un chiffre et doit posséder entre 3 et 20 caractères. Vous pouvez utiliser un caractère spécial ('-', '.', '_'). Veuillez réessayer !</div>";
            }

        }
        else 
        {
            $msg .= "<div class='alert alert-danger'>Veuillez rentrer un pseudo.</div>";
        }

        # Vérification password : code QUASI OK
        if(!empty($_POST['password']))
        {
            $password_verif = preg_match('#^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*\'\?$@%_])([-+!*\?$\'@%_\w]{6,15})$#', $_POST['password']);

            if(!$password_verif)
            {
                // si mot de passe incorrectement rentré
                $msg .= "<div class='alert alert-danger'>Votre mot de passe doit contenir entre 6 et 15 caractères avec au moins une majuscule, une minuscule, un nombre et un symbole. Veuillez réessayer !</div>";
            }

            # Si j'ai un nouveau password ...
            if(!empty($_POST['new_passord']) && !empty($_POST['confirm_password']) ){
                
                if($_POST['new_password'] == $_POST['confirm_password'])
                {
                    $_POST['password'] = $_POST['new_password']; # Besoin de confirmation
                }
                else
                {
                    $msg .= "<div class='alert alert-danger'>Veuillez rentrer le même mot de passe dans les deux champs.</div>";
                }
                
            }
        }
        else 
        {
            $msg .= "<div class='alert alert-danger'>Veuillez rentrer un mot de passe.</div>";
        }


        # Vérification email : code OK
        if(!empty($_POST['email']))
        {
            $email_verif = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            # la fonction filter_var() me permet de vérifier un résultat (email, URL ...). Elle prend 2 arguments : le résultat à vérifier + la méthode. Nous avons un retour un BOOL (TRUE/FALSE)

            $email_interdits = [
                'mailinator.com',
                'yopmail.com',
                'mail.com'
            ];

            $email_domain = explode('@', $_POST['email']); # On utilise la function explode() pour exploser un résultat en 2 partie selon le caractère choisit. Elle prend 2 arguments : le caractère ciblé, le résultat à analyser 
            
            if(!$email_verif || in_array($email_domain[1], $email_interdits))
            # la fonction in_array() nous permet de vérifier que le résultat ciblé fait bien partie de l'ARRAY ciblé. Elle prends 2 arguments: le résultat à vérifier + le tableau ciblé
            {
                $msg .= "<div class='alert alert-danger'>Veuillez rentrer un email valide.</div>";
            }

        }
        else 
        {
            $msg .= "<div class='alert alert-danger'>Veuillez rentrer un email.</div>";
        }

        # Vérification civilité : code OK
        if(!isset($_POST['civilite']) || ($_POST['civilite'] != "m" && $_POST['civilite'] != "f" && $_POST['civilite'] != "o"))
        {
            $msg .= "<div class='alert alert-danger'>Veuillez rentrer votre civilité.</div>";
        }

        # Vérification photo : code ??
        if(!empty($_FILES['photo']['name']))
        {

            # Nom
            $nom_photo = $_POST['pseudo'] . '_' . time() . '-' . rand(1,999) . $_FILES['photo']['name'];
            $nom_photo = str_replace(' ', '-', $nom_photo);
            $nom_photo = str_replace(array('é','è','à','ç','ù'), 'x', $nom_photo);

            // Enregistrer
            $chemin_photo = RACINE . 'assets/uploads/user/' . $nom_photo;

            $taille_max = 2*1048576;

            if($_FILES['photo']["size"] > $taille_max || empty($_FILES['photo']["size"]))
            {
                $msg .= "<div class='alert alert-danger'>Veuillez sélectionner un fichier de 2Mo maximum.</div>";
            }

            $type_photo = [
                'image/jpeg',
                'image/png',
                'image/gif'
            ];

            if (!in_array($_FILES['photo']["type"], $type_photo) || empty($_FILES['photo']["type"])) 
            {
                $msg .= "<div class='alert alert-danger'>Veuillez sélectionner un fichier JPEG/JPG, PNG ou GIF.</div>";
            }

        }
        else 
        {
            $chemin_photo = RACINE . 'assets/uploads/user/default.png'; # Besoin de confirmation
        }

        # Si pas d'erreur : code 
        if(empty($msg))
        {

            # Et modification membre
            if(!empty($_POST['id_membre']))
            {
                $result = $pdo->prepare("UPDATE membre SET pseudo=:pseudo,photo=:photo ,mdp=:mdp, nom=:nom, prenom=:prenom, email=:email, civilite=:civilite, ville=:ville, adresse=:adresse, code_postal=:code_postal WHERE id_membre = :id_membre");

                // Valeurs enregistrés
                $result->bindValue(":id_membre", $_POST['id_membre'], PDO::PARAM_INT);
                $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $result->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
                $result->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                $result->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $result->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                $result->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                $result->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                $result->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);
                $result->bindValue(':photo', $_POST['photo'], PDO::PARAM_STR); # Besoin de confirmation

            }
            else # Première fois
            { 
                // pseudo = dispo ?
                $result = $pdo->prepare("SELECT pseudo FROM membre WHERE pseudo = :pseudo");
                $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $result->execute();

                if($result->rowCount() == 1)
                {
                    $msg .= "<div class='alert alert-danger'>Le pseudo $_POST[pseudo] est déjà pris, veuillez en choisir un autre.</div>";
                }
                else 
                {
                    $result = $pdo->prepare("INSERT INTO membre (pseudo, photo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse, statut) VALUES (:pseudo, :photo, :mdp, :nom, :prenom, :email, :civilite, :ville, :code_postal, :adresse, 0)"); # Besoin de confirmation

                    # Password : hash
                    $password_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);
                    $result->bindValue(':mdp', $password_hash, PDO::PARAM_STR); # Besoin de confirmation

                    // Valeurs enregistrés
                    $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                    $result->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
                    $result->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                    $result->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                    $result->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                    $result->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                    $result->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                    $result->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);

                }
            }

            # Si tout fonctionne comme il faut ...
            if($result->execute())
            {

                # Besoin de vérification
                if(!empty($_FILES['photo']['name']))
                {
                    copy($_FILES['photo']['tmp_name'], $chemin_photo);
                }
    
                # Besoin de vérification
                if(!empty($_POST['id_membre']))
                {
                    $req = "SELECT * FROM membre WHERE id_membre = :id";

                    $result = $pdo->prepare($req);
                    $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
                    $result->execute();

                    $user = $result->fetch();

                    $_SESSION['user']['pseudo'] = $user['pseudo'];
                    $_SESSION['user']['prenom'] = $user['prenom'];
                    $_SESSION['user']['nom'] = $user['nom'];
                    $_SESSION['user']['email'] = $user['email'];
                    $_SESSION['user']['adresse'] = $user['adresse'];
                    $_SESSION['user']['ville'] = $user['ville'];
                    $_SESSION['user']['code_postal'] = $user['code_postal'];
                    $_SESSION['user']['civilite'] = $user['civilite'];

                    header("location:profil.php?m=update");
                }
                else
                {
                    header("location:connexion.php?m=success");

                }

            }
        }

    }

    if($_GET)
    {

       if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']))
        {
            $req = "SELECT * FROM membre WHERE id_membre = :id";

            $result = $pdo->prepare($req);
            $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
            $result->execute();

            if($result->rowCount() == 1)
            {
                $modif_membre = $result->fetch();

                // debug($modif_membre);
            }
            else 
            {
                $msg .= "<div class='alert alert-danger'>Aucune correspondance en base de donnée.</div>";
            }
        }
        else 
        {
            $msg .= "<div class='alert alert-danger'>Aucune correspondance en base de donnée.</div>";
        } 


    }

    # Conserver valeurs rentrées par l'utilisateur : code OK
    $photo = (isset($modif_membre)) ? $modif_membre['photo'] : '';
    $pseudo = (isset($modif_membre['pseudo'])) ? $modif_membre['pseudo'] : '';
    $prenom = (isset($modif_membre['prenom'])) ? $modif_membre['prenom'] : '';
    $nom = (isset($modif_membre['nom'])) ? $modif_membre['nom'] : '';
    $email = (isset($modif_membre['email'])) ? $modif_membre['email'] : '';
    $adresse = (isset($modif_membre['adresse'])) ? $modif_membre['adresse'] : '';
    $code_postal = (isset($modif_membre['code_postal'])) ? $modif_membre['code_postal'] : '';
    $ville = (isset($modif_membre['ville'])) ? $modif_membre['ville'] : '';
    $civilite = (isset($modif_membre['civilite'])) ? $modif_membre['civilite'] : '';
    
    $id_membre = (isset($modif_membre)) ? $modif_membre['id_membre'] : ''; # Besoin de confirmation
    $action = (isset($modif_membre)) ? "Modifier" : "Inscription";

?>

    <div class="starter-template">
    <h1><?= $page ?></h1>
        <form action="" method="post" enctype="multipart/form-data">
            <small class="form-text text-muted">Vos données ne seront pas revendues à des services tiers.</small>
            <?= $msg ?>
            <input type="hidden" name="id_membre" value="<?=$id_membre?>">
            <div class="form-group">
            <label for="photo">Photo de profil</label>
            <input type="file" class="form-control-file" id="photo" name="photo">

            <?php
    // Besoin de modification
                if(isset($modif_membre))
                {
                    echo "<input name='photo_actuelle' value='$photo' type='hidden'>";
                    echo "<img style='width:25%;' src='" . URL . "/assets/uploads/user/$photo'>";
                }

            ?>

            </div>
            <div class="form-group">
                <label for="pseudo">Pseudo</label>
                <input type="text" class="form-control" id="pseudo" placeholder="Choisissez votre pseudo ..." name="pseudo" required value="<?= $pseudo ?>">
                
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" placeholder="Choisissez votre mot de passe ..." name="password" required>
            </div>

            <?php  if(isset($_GET['a']) && $_GET['a'] == 'modifier') : ?>
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="new_password" placeholder="Entrez votre nouveau mot de passe ..." name="new_password">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="confirm_password" placeholder="Confirmez votre nouveau mot de passe ..." name="confirm_password">
                </div>
            <?php endif ?>
            
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" class="form-control" id="prenom" placeholder="Quel est votre prénom ..." name="prenom" value="<?= $prenom ?>">
            </div>
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" class="form-control" id="nom" placeholder="Quel est votre nom ..." name="nom" value="<?= $nom ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Entrez votre email ..." name="email" value="<?= $email ?>">
            </div>
            <div class="form-group">
                <label for="civilite">Civilité</label>
                <select class="form-control" id="civilite" name="civilite">
                    <option value="f" <?php if($civilite == 'f'){echo 'selected';} ?> >Femme</option>
                    <option value="m" <?php if ($civilite == 'm') {echo 'selected';} ?> >Homme</option>
                    <option value="o" <?php if ($civilite == 'o') {echo 'selected';} ?> >Je ne souhaite pas le préciser</option>
                </select>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse</label>
                <input type="text" class="form-control" id="adresse" placeholder="Quelle est votre adresse ..." name="adresse" value="<?= $adresse ?>">
            </div>
            <div class="form-group">
                <label for="code_postal">Code postal</label>
                <input type="text" class="form-control" id="code_postal" placeholder="Quel est votre code postal ..." name="code_postal" value="<?= $code_postal ?>">
            </div>
            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" class="form-control" id="ville" placeholder="Quelle est votre ville ..." name="ville" value="<?= $ville ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block"><?= $action ?></button>
        </form>
    </div>

<?php require_once("inc/footer.php"); ?>