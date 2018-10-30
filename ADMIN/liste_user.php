<?php

    $page = "Gestion des utilisateurs";

    require_once("inc/header_back.php");

    if($_POST){
        
        if(empty($msg)){
            if(!empty($_POST['id_membre'])) # Je suis en train de modifier un membre
                {
                    $result = $pdo->prepare("UPDATE membre SET pseudo=:pseudo, statut=:statut WHERE id_membre = :id_membre");

                    $result->bindValue(":id_membre", $_POST['id_membre'], PDO::PARAM_INT);
                }
            
                $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $result->bindValue(':statut', $_POST['statut'], PDO::PARAM_INT);

                if($result->execute()){

                    $msg .= "<div class='alert alert-success'>Le membre a bien été modifié !</div>";
   
            }
            header('location:liste_user.php');
        }
    
    }// fin $_POST

       
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
        $req = "SELECT * FROM membre WHERE id_membre = :id";
        $result = $pdo->prepare($req);
        $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $result->execute();
        // debug($result);

        if($result->rowCount() == 1)
        {
            $id_membre = $result->fetch();
            
            //debug($id_membre);
            
            $delete_req = "DELETE FROM membre WHERE id_membre = $id_membre[id_membre]";
            
            $delete_result = $pdo->exec($delete_req); 
        }

        header("location:liste_user.php");
    } 
}// fin $_GET

    
    $result = $pdo->query('SELECT * FROM membre');
    $membres = $result->fetchAll();

    $contenu .= "<div class='table-responsive'>";
    $contenu .= "<table class='table table-striped table-sm'>";
    $contenu .= "<thead class='thead-dark'><tr>";

    for($i= 0; $i < $result->columnCount(); $i++)
    {
        
        $colonne = $result->getColumnMeta($i);
        if( $colonne['name'] == "mdp"){
            continue;
        }else{
            $contenu .= "<th scope='col'>" . ucfirst(str_replace('_', ' ', $colonne['name'])) . "</th>";
        }
        
    }

    $contenu .= "<th colspan='2'>Actions</th>";
    $contenu .= "</tr></thead><tbody>";

    foreach($membres as $membre)
    {

        $contenu .= "<tr>";
        foreach ($membre as $key => $value) 
        {
            if($value == $membre['mdp']){
                continue;
            }else{
                $contenu .= "<td>" . $value . "</td>"; 
            }
              
        }

        $contenu .= "<td><a href='liste_user.php?a=modifier&id=" . $membre['id_membre'] . "'><i class='fas fa-pen'></i></a></td>";

        $contenu .= "<td><a data-toggle='modal' data-target='#deleteModal" . $membre['id_membre'] . "'><i class='fas fa-trash-alt'></i></a></td>";

        # J'appelle ma modal de supression (fonction créée dans fonction.php)
        deleteModal($membre['id_membre'], $membre['pseudo'], ' le membre ');

        $contenu .= "</tr>";
    } 

    $contenu .= "</tbody></table>";
    $contenu .= "</div>";


    $pseudo = (isset($modif_membre)) ? $modif_membre['pseudo'] : "";
    $statut = (isset($modif_membre)) ? $modif_membre['statut'] : "";
    $id_membre = (isset($modif_membre)) ? $modif_membre['id_membre'] : "";

?>

<?= $msg ?>
<?= $contenu ?>


<?php  if(isset($_GET['a']) && $_GET['a'] == 'modifier') : ?>
<form action="" method="post" class="container">
<input type="hidden" name="id_membre" value="<?=$id_membre?>">
    <div class="row">
    <div class="form-group col-5">
            <label for="pseudo">Pseudo</label>
            <input type="text" value="<?= $pseudo ?>" name="pseudo" class="form-control" id="pseudo">
        </div>        
        <div class="form-group col-5">
            <label for="statut">Statut</label>
            <select class="form-control" name="statut" id="statut">
                <option <?php if ($statut == "0") {echo "selected";} ?>>0</option>
                <option <?php if ($statut == "1") {echo "selected";} ?>>1</option>
            </select>
        </div>
        <div class="col-2">
            <input type="submit" value="Modifier" class="btn btn-info  ">
        </div>
    </div>
</form>
<?php endif ?>

<?php require_once("inc/footer_back.php"); ?>