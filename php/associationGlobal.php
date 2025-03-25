<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolunTime - Associations</title>
</head>
<body>
    <?php
//     session_start();
include '../include/connect_bdd.php';

// // Vérifier si l'utilisateur est connecté
// if (!isset($_SESSION['identifiant'])) {
//     header('Location: connexion.php');
//     exit();
// }

// // Récupérer les informations de l'utilisateur connecté
// $id_utilisateur = $_SESSION['id_utilisateur'];
// $role = isset($_SESSION['role']) ? $_SESSION['role'] : '1'; // Par défaut, rôle bénévole

// // Mode pour gérer les représentants (pour les administrateurs)
// $mode = isset($_GET['mode']) ? $_GET['mode'] : 'default';
// $id_organisation = isset($_GET['id']) ? intval($_GET['id']) : 0;

    
    $sql = "SELECT * FROM organisation";
    $requete = $db->prepare($sql);
    $requete->execute();

    $association=$requete->fetchAll();

    foreach($association as $associations){
        echo '<h1>'.$associations['nom'].'</h1>'; //nom de l'association
        echo '<h3>'.$associations['description'].'</h3>'; //description de l'associations
        echo '<img src="'.$associations['logo'].'" alt="'.$associations['nom'].'">'
        echo '<p>Adresse : '.$associations['adresse'].' '.$associations['code_postal'].' '.$associations['ville'].'</p>'; //adresse de l'association
        echo '<p> Email : '.$associations['email_contact'].'</p>'; //contact 1 email
        echo '<p>Téléphone : '.$associations['telephone'].'</p>'; //contact 2 téléphone
        echo '<p> Site Web : '.$associations['site_web'].'</p>'; //contact 1 email
        echo '<p> Date de création : '.$associations['date_creation'].'</p>'; //date de création de l'association

    }
    
    ?>
</body>
</html>