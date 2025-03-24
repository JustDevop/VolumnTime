<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleProfil.css">
    <title>VolumnTime - Profil</title>
</head>
<body>
    <header>
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
            <img class="iconeProfil" src="../asset/icones/icone-profil.png">
        </div>
        <div class="loupe">
            <img class="iconeLoupe" src="../asset/icones/icone-loupe.png">
        </div>
    </header>
    <?php
        include '../include/connect_bdd.php';

        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['identifiant'])) {
            header('Location: connexion.php');
            exit();
        }

        // Récupérer les informations de l'utilisateur connecté
        $identifiantUser = $_SESSION['identifiant'];
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '1'; // Par défaut, rôle bénévole
    ?>

    <section>
        <div class="profil">

        </div>
    </section>

    <?php
        // REQUETE SQL AFFICHE TOUT PROFIL
        $sql = "SELECT utilisateur.*, competence.* FROM utilisateur_competence JOIN utilisateur ON utilisateur_competence.id_utilisateur=utilisateur.id_utilisateur JOIN competence ON utilisateur_competence.id_competence=competence.id_competence WHERE utilisateur.identifiant = :identifiant";
        $requete = $db->prepare($sql);
        $requete->execute([
            "identifiant" => $identifiantUser
        ]);

        // $profil = $requete->fetchAll();
        // foreach($profil as $user){
        //     echo "<li>".$user['nomCours']. " - mis en ligne le : ". $user['dateCreation']. "</li><form action='voirCours.php' method='post'><input type='hidden' name='idCours' value='".$user['idCours']."'><button type='submit'>Voir le cours</button></form><form action='editCours.php' method='post'><input type='hidden' name='idCours' value='".$user['idCours']."'><button type='submit'>Modifier</button></form><form action='suppCours.php' method='post' onsubmit='return confirmDeletion();'><input type='hidden' name='idCours' value='".$user['idCours']."'><input type='hidden' name='confirmDelete' value='yes'><input type='hidden' name='idCours' value='".$user['idCours']."'><button type='submit'>Supprimer</button></form><br/>";
        // }

    ?>
</body>
</html>


