<?php

    # Définir mon nom de page
    $page = "Historique des commandes utilisateurs";

    require_once("inc/header_back.php");

    if($_POST){
        
        if(empty($msg)){
            if(!empty($_POST['id_commande'])) # Je suis en train de modifier une commande
                {
                    $result = $pdo->prepare("UPDATE commande SET etat=:etat WHERE id_commande = :id_commande");

                    $result->bindValue(":id_commande", $_POST['id_commande'], PDO::PARAM_INT);
                }
            
                $result->bindValue(':etat', $_POST['etat'], PDO::PARAM_STR);

                if($result->execute()){

                    $msg .= "<div class='alert alert-success'>L'etat de la commande a bien été modifié !</div>";
   
            }
            header('location:historique_user.php');
        }
    //debug($_POST);
    }// fin $_POST

    if($_GET)
    {

       if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']))
        {
            $req = "SELECT * FROM commande WHERE id_commande = :id";

            $result = $pdo->prepare($req);
            $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
            $result->execute();

            if($result->rowCount() == 1)
            {
                $modif_commande = $result->fetch();

                //debug($modif_membre);
                
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

        if(isset($_GET['a']) && isset($_GET['id']) && $_GET['a'] == "delete" && is_numeric($_GET['id'])) # la fonction is_numeric() me permet de vérifier que le paramètre rentré est bien un chiffre
        {
        $req = "SELECT * FROM commande WHERE id_commande = :id";
        $result = $pdo->prepare($req);
        $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $result->execute();
        // debug($result);

        if($result->rowCount() == 1)
        {
            $id_commande = $result->fetch();
            
            //debug($id_commande);
            
            $delete_req = "DELETE FROM commande WHERE id_commande = $id_commande[id_commande]";
            
            $delete_result = $pdo->exec($delete_req); 
        }

        header("location:historique_user.php");
    } 
}// fin $_GET

    $result = $pdo->query('SELECT c.id_commande, c.date_enregistrement, c.montant, p.titre, p.photo, d.quantite, m.ville, m.code_postal, m.adresse, c.etat FROM membre m, produit p, detail_commande d, commande c WHERE c.id_commande = d.id_commande AND m.id_membre = c.id_membre AND p.id_produit = d.id_produit');
    $lignes = $result->fetchAll();

    $contenu .= "<div class='table-responsive'>";
    $contenu .= "<table class='table table-striped table-sm'>";
    $contenu .= "<thead class='thead-dark'><tr>";

    for($i= 0; $i < $result->columnCount(); $i++)
    {
        
        $colonne = $result->getColumnMeta($i);

        $contenu .= "<th scope='col'>" . ucfirst(str_replace('_', ' ', $colonne['name'])) . "</th>";

        
    }

    $contenu .= "<th colspan='2'>Actions</th>";
    $contenu .= "</tr></thead><tbody>";

    foreach($lignes as $ligne)
    {

        $contenu .= "<tr>";
        foreach ($ligne as $key => $value) 
        {

                $contenu .= "<td>" . $value . "</td>"; 
              
        }

        $contenu .= "<td><a href='historique_user.php?a=modifier&id=" . $ligne['id_commande'] . "'><i class='fas fa-pen'></i></a></td>";

        $contenu .= "<td><a data-toggle='modal' data-target='#deleteModal" . $ligne['id_commande'] . "'><i class='fas fa-trash-alt'></i></a></td>";

        // # J'appelle ma modal de supression (fonction créée dans fonction.php)
        deleteModal($ligne['id_commande'], $ligne['id_commande'], ' la commande ');

        $contenu .= "</tr>";
    } 

    $contenu .= "</tbody></table>";
    $contenu .= "</div>";

    $id_commande = (isset($modif_commande)) ? $modif_commande['id_commande'] : "";
?>

<?php  if(isset($_GET['a']) && $_GET['a'] == 'modifier') : ?>
<form action="" method="post" class="container">
<input type="hidden" name="id_commande" value="<?=$id_commande?>">
    <div class="row">
        <div class="form-group col-5">
            <label for="etat">Etat de la commande</label>
            <select class="form-control" name="etat" id="etat">
                <option value="en préparation">en préparation</option>
                <option value="envoyé">envoyé</option>
                <option value="livré">livré</option>
            </select>
        </div>
        <div class="col-2">
            <input type="submit" value="Modifier" class="btn btn-info  ">
        </div>
    </div>
</form>
<?php endif ?>


<?= $msg ?>
<?= $contenu ?>

<?php require_once("inc/footer_back.php"); ?>