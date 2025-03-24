<?php
    session_start();
    include '../include/connect_bdd.php';

    $sql = "SELECT utilisateur.*, competence.* FROM utilisateur_competence JOIN utilisateur ON utilisateur_competence.id_utilisateur=utilisateur.id_utilisateur JOIN competence ON utilisateur_competence.id_competence=competence.id_competence";
    $stmt = $db->prepare($sql);
    //Modifier execute pour récupérer les données de la personne recherchée, par défaut prendre celles de l'utilisateur connecté
    
    $stmt->execute();

    $profils = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profil - </title>
    </head>
    <body>
        <section class="profil">
            
        </section>
    </body>
</html>



// REQUETE SQL AFFICHE TOUT PROFIL
//SELECT utilisateur.*, competence.* FROM utilisateur_competence JOIN utilisateur ON utilisateur_competence.id_utilisateur=utilisateur.id_utilisateur JOIN competence ON utilisateur_competence.id_competence=competence.id_competence 


