<?php
    include '../include/connect_bdd.php'; // Fichier de configuration pour la connexion à la base de données
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

    $sql = "SELECT * FROM organisation";
    $requete = $db->prepare($sql);
    $requete->execute();

    $association=$requete->fetchAll();

    foreach($association as $associations){
        echo '<h1>'.$associations['nom'].'</h1>'; //nom de l'association
        echo '<h3>'.$associations['description'].'</h3>'; //description de l'associations
        echo '<img src="'.$associations['logo'].'" alt="'.$associations['nom'].'">';
        echo '<p>Adresse : '.$associations['adresse'].' '.$associations['code_postal'].' '.$associations['ville'].'</p>'; //adresse de l'association
        echo '<p> Email : '.$associations['email_contact'].'</p>'; //contact 1 email
        echo '<p>Téléphone : '.$associations['telephone'].'</p>'; //contact 2 téléphone
        echo '<p> Site Web : '.$associations['site_web'].'</p>'; //contact 1 email
        echo '<p> Date de création : '.$associations['date_creation'].'</p>'; //date de création de l'association

    }
    ?>
</body>
</html>