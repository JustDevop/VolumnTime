<?php
session_start();
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant'])) {
    header('Location: connexion.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'liste';
$id_organisation = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Déterminer si l'utilisateur est admin
$est_admin = ($_SESSION['role'] == '3');

// Vérifier si l'utilisateur est représentant d'au moins une organisation
try {
    $sql_rep_check = "SELECT COUNT(*) FROM organisation_representant WHERE id_utilisateur = :id_utilisateur";
    $stmt_rep_check = $db->prepare($sql_rep_check);
    $stmt_rep_check->execute(['id_utilisateur' => $id_utilisateur]);
    $est_representant_quelque_part = ($stmt_rep_check->fetchColumn() > 0);
    
    // Combiner la vérification du rôle et de l'existence dans la table organisation_representant
    $est_representant = ($est_representant_quelque_part || $_SESSION['role'] == '2' || $est_admin);
} catch (Exception $e) {
    // En cas d'erreur, par sécurité, supposer que l'utilisateur n'est pas représentant
    $est_representant = ($_SESSION['role'] == '2' || $est_admin);
}

// MODE DÉTAIL D'UNE ORGANISATION
if ($mode === 'detail' && $id_organisation > 0) {
    try {
        // Récupérer les informations de l'organisation
        $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM organisation_representant WHERE id_organisation = o.id_organisation AND id_utilisateur = :id_utilisateur) as est_representant
                FROM organisation o 
                WHERE o.id_organisation = :id_organisation AND o.statut = 'approuvé'";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'id_organisation' => $id_organisation,
            'id_utilisateur' => $id_utilisateur
        ]);
        
        $organisation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$organisation) {
            $_SESSION['message_error'] = "Organisation introuvable ou non approuvée.";
            header('Location: dashboard.php');
            exit();
        }
        
        // Redéfinir si l'utilisateur est représentant de cette organisation spécifique
        $est_representant_org = ($organisation['est_representant'] > 0 || $est_admin);
        
        // Récupérer le représentant de l'organisation pour contact
        $sql_rep = "SELECT u.id_utilisateur, u.nom, u.prenom
                    FROM organisation_representant orep
                    JOIN utilisateur u ON orep.id_utilisateur = u.id_utilisateur
                    WHERE orep.id_organisation = :id_organisation
                    LIMIT 1";
        
        $stmt_rep = $db->prepare($sql_rep);
        $stmt_rep->execute(['id_organisation' => $id_organisation]);
        $representant = $stmt_rep->fetch(PDO::FETCH_ASSOC);
        
        // Récupérer les missions de l'organisation
        $sql_mission = "SELECT m.*, 
                        (SELECT COUNT(*) FROM inscription WHERE id_mission = m.id_mission AND id_utilisateur = :id_utilisateur) as est_inscrit
                        FROM mission m
                        WHERE m.id_organisation = :id_organisation 
                        AND m.statut = 'ouverte'
                        ORDER BY m.date_debut ASC";
        
        $stmt_mission = $db->prepare($sql_mission);
        $stmt_mission->execute([
            'id_organisation' => $id_organisation,
            'id_utilisateur' => $id_utilisateur
        ]);
        
        $missions = $stmt_mission->fetchAll(PDO::FETCH_ASSOC);
        
        // Vérifier si l'organisation est dans les favoris
        $sql_fav = "SELECT COUNT(*) FROM favoris_organisation 
                    WHERE id_utilisateur = :id_utilisateur AND id_organisation = :id_organisation";
        $stmt_fav = $db->prepare($sql_fav);
        $stmt_fav->execute([
            'id_utilisateur' => $id_utilisateur,
            'id_organisation' => $id_organisation
        ]);
        $est_favori = ($stmt_fav->fetchColumn() > 0);
        
    } catch (Exception $e) {
        $_SESSION['message_error'] = "Erreur: " . $e->getMessage();
        header('Location: dashboard.php');
        exit();
    }
    
    // Affichage du détail de l'organisation
    include '../include/header.php';
    ?>
    
    <main class="main">
        <div class="back-link">
            <a href="<?php echo $est_representant ? 'organisation.php' : 'dashboard.php'; ?>" class="btn">Retour</a>
        </div>
        
        <?php if (isset($_SESSION['message_error'])): ?>
            <div class="alert error">
                <?php echo $_SESSION['message_error']; unset($_SESSION['message_error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['message_success'])): ?>
            <div class="alert success">
                <?php echo $_SESSION['message_success']; unset($_SESSION['message_success']); ?>
            </div>
        <?php endif; ?>
        
        <section class="org-details">
            <!-- Contenu existant du détail de l'organisation -->
            <!-- ... -->
        </section>
    </main>
    <?php
    
// MODE LISTE DES ORGANISATIONS
} else {
    // Vérifier les droits d'accès pour la liste d'organisations
    if (!$est_representant) {
        header('Location: dashboard.php');
        exit();
    }
    
    // Récupérer uniquement les organisations dont l'utilisateur est représentant
    try {
        $sql = "SELECT o.* 
                FROM organisation o
                JOIN organisation_representant orep ON o.id_organisation = orep.id_organisation
                WHERE orep.id_utilisateur = :id_utilisateur AND o.statut = 'approuvé'
                ORDER BY o.nom";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_utilisateur' => $id_utilisateur]);
        $associations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        $associations = [];
    }

    // Récupérer les missions des organisations
    $missions = [];
    if (!empty($associations)) {
        try {
            $ids_orgs = array_column($associations, 'id_organisation');
            $placeholders = implode(',', array_fill(0, count($ids_orgs), '?'));
            
            $sql = "SELECT m.*, o.nom as nom_organisation 
                    FROM mission m
                    JOIN organisation o ON m.id_organisation = o.id_organisation
                    WHERE m.id_organisation IN ($placeholders)
                    ORDER BY m.date_debut DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($ids_orgs);
            $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Pas d'interruption si erreur sur missions
            $error_mission = $e->getMessage();
        }
    }
    
    // Affichage de la liste des organisations
    include '../include/header.php';
    ?>
    
    <main class="main">
        <div class="back-link">
            <a href="dashboard.php" class="btn">Retour au tableau de bord</a>
        </div>
        
        <?php if (isset($_SESSION['message_success'])): ?>
            <div class="alert success">
                <?php echo $_SESSION['message_success']; unset($_SESSION['message_success']); ?>
            </div>
        <?php endif; ?>
        
        <section>
            <h2>Vos organisations</h2>
            <?php if (empty($associations)): ?>
                <p>Vous ne gérez actuellement aucune organisation.</p>
                <a href="inscription_organisation.php" class="btn">Inscrire une nouvelle organisation</a>
            <?php else: ?>
                <a href="inscription_organisation.php" class="btn">Ajouter une organisation</a>
                <table class="organisation-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Contact</th>
                            <th>Localisation</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($associations as $association): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($association['nom']); ?>
                                    <?php if ($association['logo']): ?>
                                        <img src="../asset/images/<?php echo htmlspecialchars($association['logo']); ?>" alt="Logo" class="small-logo">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars(substr($association['description'], 0, 100)) . (strlen($association['description']) > 100 ? '...' : ''); ?></td>
                                <td>
                                    <strong>Email:</strong> <?php echo htmlspecialchars($association['email_contact']); ?><br>
                                    <strong>Tél:</strong> <?php echo htmlspecialchars($association['telephone'] ?? 'Non renseigné'); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($association['ville'] . ' (' . $association['code_postal'] . ')'); ?><br>
                                    <?php echo htmlspecialchars($association['pays']); ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($association['date_creation'])); ?></td>
                                <td>
                                    <a href="organisation.php?mode=detail&id=<?php echo $association['id_organisation']; ?>" class="btn">Détails</a>
                                    <a href="creer_mission.php?id_org=<?php echo $association['id_organisation']; ?>" class="btn">Ajouter mission</a>
                                    <a href="modifier_organisation.php?id=<?php echo $association['id_organisation']; ?>" class="btn">Modifier</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
        
        <?php if (!empty($missions)): ?>
        <section>
            <h2>Missions de vos organisations</h2>
            <table class="missions-table">
                <thead>
                    <tr>
                        <th>Organisation</th>
                        <th>Titre</th>
                        <th>Dates</th>
                        <th>Lieu</th>
                        <th>Places</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missions as $mission): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mission['nom_organisation']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($mission['titre']); ?>
                                <?php if ($mission['handicap']): ?>
                                    <span class="badge-accessible" title="Accessible aux personnes en situation de handicap">♿</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong>Début:</strong> <?php echo date('d/m/Y', strtotime($mission['date_debut'])); ?><br>
                                <strong>Fin:</strong> <?php echo date('d/m/Y', strtotime($mission['date_fin'])); ?>
                            </td>
                            <td><?php echo htmlspecialchars($mission['lieu'] ?? 'Non précisé'); ?></td>
                            <td>
                                <?php 
                                try {
                                    // Compter les inscriptions validées pour cette mission
                                    $sql_count = "SELECT COUNT(*) FROM inscription WHERE id_mission = :id_mission AND statut != 'annulée'";
                                    $stmt_count = $db->prepare($sql_count);
                                    $stmt_count->execute(['id_mission' => $mission['id_mission']]);
                                    $places_prises = $stmt_count->fetchColumn();
                                    
                                    echo $places_prises . ' / ' . $mission['nb_places'];
                                } catch (Exception $e) {
                                    echo $mission['nb_places'] . ' au total';
                                }
                                ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $mission['statut']; ?>">
                                    <?php 
                                    switch($mission['statut']) {
                                        case 'ouverte': echo 'Ouverte'; break;
                                        case 'fermée': echo 'Fermée'; break;
                                        case 'en attente': echo 'En attente'; break;
                                        default: echo $mission['statut']; 
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <a href="mission_detail.php?id=<?php echo $mission['id_mission']; ?>" class="btn">Détails</a>
                                <a href="modifier_mission.php?id=<?php echo $mission['id_mission']; ?>" class="btn">Modifier</a>
                                <a href="inscrits_mission.php?id=<?php echo $mission['id_mission']; ?>" class="btn">Voir inscrits</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php endif; ?>
    </main>
    <?php
}
?>
</body>
</html>