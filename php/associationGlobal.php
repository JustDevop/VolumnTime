<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleAssoc.css">
    <title>VolunTime - Associations</title>
</head>
<body>
<header>
        <img class="logo" src="../asset/logo/logo-voluntime_version-finale.png" alt="logo volunTime">
        <nav class="burger">
            <span class="hamburger">☰</span>
            <ul>
                <li><a href="associationGlobal.php">Associations</a></li>
                <li><a href="mission.php">Missions</a></li>
                <li><a href="conversation.php">Discussions</a></li>
                <li><a href="contact.php">Contacts</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </nav>
        <div class="profil">
            <img class="iconeProfil" src="../asset/icones/icone-profil.png">
        </div>
        <div class="container">
            <form action="" class="search-form">
                <input type="text" placeholder="Type to search" class="search-input" />
                <div class="search-button">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <i class="fa-solid fa-xmark search-close"></i>
                </div>
            </form>
        </div>
    </header>
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

//Mode pour gérer les représentants (pour les administrateurs)
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'default';
$id_organisation = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Récupérer les informations de l'utilisateur connecté
$id_utilisateur = $_SESSION['id_utilisateur'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '1'; // Par défaut, rôle bénévole

    $sql = "SELECT * FROM organisation WHERE id_organisation = :id_organisation";
    $requete = $db->prepare($sql);
    $requete->execute(['id_organisation' => $id_organisation]);

    $association=$requete->fetchAll();

    foreach($association as $associations){
        echo '<p>'.$associations['nom'].'   -    Situé à : '.$associations['ville'].'     -     Contactez-nous au : '.$associations['telephone'].'</p>';
    }
    
    ?>
</body>
</html>