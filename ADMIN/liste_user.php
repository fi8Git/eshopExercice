<?php

    $page = "Gestion des utilisateurs";

    require_once("inc/header_back.php");

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
        //deleteModal($membre['id_membre'], $membre['pseudo'], $membre['reference']);

        $contenu .= "</tr>";
    } 

    $contenu .= "</tbody></table>";
    $contenu .= "</div>";

    if($_POST){

        if(!empty($_POST['id_membre'])) # Je suis en train de modifier un membre
            {
                $result = $pdo->prepare("UPDATE membre SET pseudo=:pseudo, statut=:statut WHERE id_membre = :id_membre");

                $result->bindValue(":id_membre", $_POST['id_membre'], PDO::PARAM_INT);
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

                debug($modif_membre);
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

    }// fin $_GET

    $pseudo = (isset($modif_membre)) ? $modif_membre['pseudo'] : "";
    $statut = (isset($modif_membre)) ? $modif_membre['statut'] : "";
?>

<?= $msg ?>
<?= $contenu ?>

<?php  if(isset($_GET['a']) && $_GET['a'] == 'modifier') : ?>
<form action="" method="post" class="container">
    <div class="row">
    <div class="form-group col-5">
            <label for="pseudo">Pseudo</label>
            <input type="text" name="pseudo" class="form-control" id="pseudo">
        </div>        
        <div class="form-group col-5">
            <label for="statut">Statut</label>
            <select class="form-control" name="statut" id="statut">
                <option>0</option>
                <option>1</option>
            </select>
        </div>
        <div class="col-2">
            <input type="submit" value="Modifier" class="btn btn-info  ">
        </div>
    </div>
</form>
<?php endif ?>

<?php require_once("inc/footer_back.php"); ?>