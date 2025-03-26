<?php
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant'])) {
    header('Location: connexion.php');
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$id_utilisateur = $_SESSION['identifiant'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '1'; // Par défaut, rôle bénévole

// Récupérer les demandes d'organisation en attente (seulement pour les administrateurs)
$organisations_en_attente = [];
if ($role == '3') { // 3 = administrateur
    try {
        $sql = "SELECT o.id_organisation, o.nom, o.description, o.email_contact, o.telephone, 
                o.adresse, o.ville, o.code_postal, o.pays, o.site_web, o.date_creation,
                u.nom as representant_nom, u.prenom as representant_prenom
                FROM organisation o
                LEFT JOIN organisation_representant or_rep ON o.id_organisation = or_rep.id_organisation
                LEFT JOIN utilisateur u ON or_rep.id_utilisateur = u.id_utilisateur
                WHERE o.statut = 'en_attente'
                ORDER BY o.date_creation DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $organisations_en_attente = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Gérer l'erreur
        $error_message = $e->getMessage();
    }
}

// Traitement des actions d'approbation/rejet si formulaire soumis
if ($role == '3' && isset($_POST['action']) && isset($_POST['id_organisation'])) {
    $action = $_POST['action'];
    $id_organisation = $_POST['id_organisation'];
    
    try {
        if ($action === 'approuver') {
            $sql = "UPDATE organisation SET statut = 'approuve' WHERE id_organisation = :id_organisation";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id_organisation' => $id_organisation]);
            $_SESSION['admin_message'] = "L'organisation a été approuvée avec succès.";
        } elseif ($action === 'rejeter') {
            $sql = "UPDATE organisation SET statut = 'rejete' WHERE id_organisation = :id_organisation";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id_organisation' => $id_organisation]);
            $_SESSION['admin_message'] = "L'organisation a été rejetée.";
        }
        
        // Rediriger pour éviter les soumissions multiples
        header('Location: dashboard.php');
        exit();
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Tableau de bord</h1>
    </header>
    <main>
        <h2>Vos conversations</h2>
        <table>
            <thead>
                <tr>
                    <th>Sujet</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conversations as $conversation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($conversation['sujet']); ?></td>
                        <td><?php echo htmlspecialchars($conversation['date_creation']); ?></td>
                        <td><a href="conversation.php?id=<?php echo $conversation['id_conversation']; ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

         <!-- Panel d'administration (visible uniquement pour les administrateurs) -->
         <?php if ($role == '3'): ?>
            <section class="admin-panel">
                <h2>Administration - Demandes d'approbation d'organisations</h2>
                
                <?php if (empty($organisations_en_attente)): ?>
                    <div class="no-requests">
                        <p>Aucune demande d'approbation en attente.</p>
                    </div>
                <?php else: ?>
                    <table class="org-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Contact</th>
                                <th>Adresse</th>
                                <th>Date de demande</th>
                                <th>Représentant</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($organisations_en_attente as $org): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($org['nom']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($org['description'], 0, 100)) . '...'; ?></td>
                                    <td>
                                        Email: <?php echo htmlspecialchars($org['email_contact']); ?><br>
                                        Tél: <?php echo htmlspecialchars($org['telephone']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($org['adresse']); ?><br>
                                        <?php echo htmlspecialchars($org['code_postal']) . ' ' . htmlspecialchars($org['ville']); ?><br>
                                        <?php echo htmlspecialchars($org['pays']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($org['date_creation']); ?></td>
                                    <td>
                                        <?php if ($org['representant_nom']): ?>
                                            <?php echo htmlspecialchars($org['representant_prenom'] . ' ' . $org['representant_nom']); ?>
                                        <?php else: ?>
                                            <em>Non spécifié</em>
                                        <?php endif; ?>
                                    </td>
                                    <td class="approval-btns">
                                        <form method="post" action="dashboard.php">
                                            <input type="hidden" name="id_organisation" value="<?php echo $org['id_organisation']; ?>">
                                            <input type="hidden" name="action" value="approuver">
                                            <button type="submit" class="btn-approve">Approuver</button>
                                        </form>
                                        <form method="post" action="dashboard.php">
                                            <input type="hidden" name="id_organisation" value="<?php echo $org['id_organisation']; ?>">
                                            <input type="hidden" name="action" value="rejeter">
                                            <button type="submit" class="btn-reject">Rejeter</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        <?php endif; ?>


         <!-- Section pour faire une demande d'organisation (visible uniquement pour les non-administrateurs) -->
        <?php if ($role != '3'): ?>
            <h2>Vous avez une organisation  ? Faites la demande pour l'ajouter</h2>
            <p>
                <a href="inscription_organisation.php" class="btn">Inscrire mon organisation</a>
            </p>
        <?php endif; ?>
    </main>

</body>
</html>