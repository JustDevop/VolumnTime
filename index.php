<?php
    include 'include/connect_bdd.php';

    // $sql = "SELECT * FROM organisation WHERE statut = 'approuvé'";
    // $stmt = $db->prepare($sql);
    // $stmt->execute();
    // $organisations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <title>VolumnTime</title>
</head>
<body>
    <header>
        <img class="logo" src="asset/logo/logo-voluntime_version-finale.png" alt="logo volunTime">
        <nav class="burger">
            <span class="hamburger">☰</span>
            <ul>
                <li><a href="php/dashboard.php">Associations</a></li>
                <li><a href="php/conversation.php">Discussions</a></li>
                <li><a href="php/favoris.php">Favoris</a></li>
                <li><a href="php/dashboard.php">Contacts</a></li>
            </ul>
        </nav>
        <div class="profil">
            <img class="iconeProfil" src="asset/icones/icone-profil.png">
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

    <section class="about">
        <div class="titre">
            <h1><b>L'engagement</b> à portée de main.</h1>
        </div>

        <div class="texte">
            <p>VolunTime est une application qui connecte les 
            bénévoles aux associations en leur permettant 
            de trouver facilement des missions adaptées à 
            leurs compétences et leur disponibilité. 
            En facilitant la mise en relation directe, 
            elle transforme chaque minute en une opportunité 
            de faire la différence.</p>
        </div>
    </section>

    <section class="illustration">
        <div class="une">
            <img src="asset/images/ecologie.png" alt="Main portant une plante">
        </div>
        <div class="deux">
            <img src="asset/images/crafts.png" alt="réalisation d'affiche fait main">
        </div>
        <div class="trois">
            <img src="asset/images/confi.png" alt="réalisation de confiture">
        </div>
    </section>

    <section class="organisations">
        <div class="titre">
            <h2>Les organisations présentes sur notre plateforme</h2>
        </div>

        <div class="liste">
            <?php foreach ($organisations as $organisation): ?>
                <div class="organisation">
                    <img src="asset/images/<?= $organisation['logo'] ?>" alt="<?= $organisation['nom'] ?>">
                    <h3><?= $organisation['nom'] ?></h3>
                    <p><?= $organisation['description'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    
    <script src="Js/menuhamburger.js"></script>
    <script src="Js/recherche.js"></script>
</body>
</html>