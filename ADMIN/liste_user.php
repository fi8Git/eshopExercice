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
        $contenu .= "<th scope='col'>" . ucfirst(str_replace('_', ' ', $colonne['name'])) . "</th>";
    
    }

    $contenu .= "<th colspan='2'>Actions</th>";
    $contenu .= "</tr></thead><tbody>";

    foreach($membres as $membre)
    {

        $contenu .= "<tr>";
        foreach ($membre as $key => $value) 
        {

            $contenu .= "<td>" . $value . "</td>";  
            
        }

        //$contenu .= "<td><a href='formulaire_produit.php?id=" . $produit['id_produit'] . "'><i class='fas fa-pen'></i></a></td>";

        $contenu .= "<td><a data-toggle='modal' data-target='#deleteModal" . $membre['id_membre'] . "'><i class='fas fa-trash-alt'></i></a></td>";

        # J'appelle ma modal de supression (fonction créée dans fonction.php)
        //deleteModal($membre['id_membre'], $membre['pseudo'], $membre['reference']);

        $contenu .= "</tr>";
    }

?>

<?= $msg ?>
<?= $contenu ?>

<?php require_once("inc/footer_back.php"); ?>