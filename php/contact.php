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

    $id_utilisateur = $_SESSION['id_utilisateur'];

    // Récupérer la liste des contacts de l'utilisateur
    try {
        $sql = "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.telephone
                FROM contact c
                JOIN utilisateur u ON c.id_contact = u.id_utilisateur
                WHERE c.id_utilisateur = :id_utilisateur
                ORDER BY u.nom, u.prenom";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_utilisateur' => $id_utilisateur]);
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    // Récupérer les utilisateurs qui ne sont pas encore contacts (pour l'ajout de contacts)
    try {
        $sql = "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, 
                (SELECT GROUP_CONCAT(o.nom SEPARATOR ', ') 
                 FROM organisation_representant orep 
                 JOIN organisation o ON orep.id_organisation = o.id_organisation 
                 WHERE orep.id_utilisateur = u.id_utilisateur AND o.statut = 'approuvé') as organisations
                FROM utilisateur u
                WHERE u.id_utilisateur != :id_utilisateur
                AND u.id_utilisateur NOT IN (
                    SELECT id_contact FROM contact WHERE id_utilisateur = :id_utilisateur
                )
                AND u.role != '3' -- Exclure les administrateurs
                ORDER BY u.nom, u.prenom
                LIMIT 50"; // Limite à 50 utilisateurs pour éviter de surcharger la liste - Modifiable 
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_utilisateur' => $id_utilisateur]);
        $autres_utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
?>
    <script src="../Js/menuhamburger.js"></script>

    <div class="fav">
        <h1>Contacts</h1>
        <button class="add-contact" id="show-add-contact"><img src="../asset/icones/icone-plus.png" alt="Add Contact"></button>
    </div>

    <section>
        <div class="listecontact">
            <?php echo $_SESSION['message_error']; unset($_SESSION['message_error']); ?>
            <?php if (empty($contacts)): ?>
                <p>Vous n'avez aucun contact pour le moment.</p>
            <?php else: ?>
                <?php foreach ($contacts as $contact): ?>
                    <p>
                        <?php echo htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']); ?>
                        <span class="icons">
                            <a href="conversation.php?contact=<?php echo $contact['id_utilisateur']; ?>"><img class="une" src="../asset/icones/messager.png" alt="Messager"></a>
                            <a href="profil.php?id=<?php echo $contact['id_utilisateur']; ?>"><img src="../asset/icones/icone-profil.png" alt="Profil"></a>
                        </span>
                    </p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Ajouter un contact -->
    <div id="add-contact-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Ajouter un contact</h2>
            <?php if (empty($autres_utilisateurs)): ?>
                <p>Aucun autre utilisateur disponible à ajouter.</p>
            <?php else: ?>
                <form action="ajout_contact.php" method="POST">
                <select name="id_contact" required>
                    <option value="">Sélectionner un utilisateur</option>
                    <?php foreach ($autres_utilisateurs as $user): ?>
                        <option value="<?php echo $user['id_utilisateur']; ?>">
                            <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']);
                                echo !empty($user['organisations']) ? ' (Représentant: ' . htmlspecialchars($user['organisations']) . ')' : ''; 
                                echo ' - ' . $user['email']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                    <button type="submit" class="btn-add">Ajouter</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        //Modal d'ajout de contact
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('add-contact-modal');
            var btn = document.getElementById('show-add-contact');
            var span = document.getElementsByClassName('close')[0];
            
            btn.onclick = function() {
                modal.style.display = 'block';
            }
            
            span.onclick = function() {
                modal.style.display = 'none';
            }
            
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>