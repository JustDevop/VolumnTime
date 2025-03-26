<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolunTime - Associations</title>
</head>
<body>
    <?php
    session_start();
    include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant'])) {
    header('Location: connexion.php');
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$id_utilisateur = $_SESSION['id_utilisateur'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '1'; // Par défaut, rôle bénévole

// Mode pour gérer les représentants (pour les administrateurs)
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'default';
$id_organisation = isset($_GET['id']) ? intval($_GET['id']) : 0;

    
    $sql = "SELECT * FROM organisation";
    $requete = $db->prepare($sql);
    $requete->execute();

    $association=$requete->fetchAll();

    foreach($association as $associations){
        echo '<p>'.$associations['nom'].'   -    Situé à : '.$associations['ville'].'     -     Contactez-nous au : '.$associations['telephone'].'</p>';
    }
    
    ?>
</body>
</html>