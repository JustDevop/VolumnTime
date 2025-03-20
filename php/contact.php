<?php
    session_start();
    include '../include/connect_bdd.php'; // Fichier de configuration pour la connexion à la base de données

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['identifiant'])) {
        header('Location: connexion.php');
        exit();
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleContact.css" >
    <title>VolumnTime - Contacts</title>
</head>
<body>
    <header>
        <nav class="burger">
            <span class="hamburger">☰</span>
            <ul>
                <li><a href="conversation.php">Discussions</a></li>
                <li><a href="favoris.php">Favoris</a></li>
                <li><a href="dashboard.php">Contacts</a></li>
            </ul>
        </nav>
        <div class="profil">
            <img class="iconeProfil" src="../asset/icones/icone-profil.png">
        </div>
        <div class="loupe">
            <img class="iconeLoupe" src="../asset/icones/icone-loupe.png">
        </div>
    </header>
    
    <script src="../Js/menuhamburger.js"></script>

    <div class="fav">
        <h1>Contacts</h1>
        <button class="add-contact"><img src="../asset/icones/icone-plus.png" alt="Add Contact"></button>
    </div>

    <section>
        <div class="listecontact">
            <p>Benjamin <span class="icons"><img class="une" src="../asset/icones/messager.png" alt="Messager"><img src="../asset/icones/icone-profil.png" alt="Profil"></span></p>
            <p>Corentin <span class="icons"><img class="une" src="../asset/icones/messager.png" alt="Messager"><img src="../asset/icones/icone-profil.png" alt="Profil"></span></p>
            <p>Justine <span class="icons"><img class="une" src="../asset/icones/messager.png" alt="Messager"><img src="../asset/icones/icone-profil.png" alt="Profil"></span></p>
            <p>Noémie <span class="icons"><img class="une" src="../asset/icones/messager.png" alt="Messager"><img src="../asset/icones/icone-profil.png" alt="Profil"></span></p>
            <p>Shirley <span class="icons"><img class="une" src="../asset/icones/messager.png" alt="Messager"><img src="../asset/icones/icone-profil.png" alt="Profil"></span></p>
        </div>
    </section>
</body>
</html>