<?php
//session_start();
include '../include/connect_bdd.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolumnTime - Favoris</title>
</head>
<body>
    <?php

    $sql = "SELECT mission.titre FROM favoris_mission JOIN mission ON favoris_mission.id_mission=mission.id_mission";
    $requete = $db->prepare($sql);
    $requete->execute();

    $mission=$requete->fetchAll();
    foreach($mission as $missions){

        echo '<p>'.$missions['titre'].'</p>';
    
    }
    ?>
</body>
</html>