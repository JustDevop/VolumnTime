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

// Récupérer les demandes d'organisation en attente (seulement pour les administrateurs)
$organisations_en_attente = [];
if ($role == '3') { // 3 = administrateur
    try {
        $sql = "SELECT o.id_organisation, o.nom, o.description, o.email_contact, o.telephone, 
                o.adresse, o.ville, o.code_postal, o.pays, o.site_web, o.date_creation,
                u.id_utilisateur as demandeur_id, u.nom as representant_nom, u.prenom as representant_prenom
                FROM organisation o
                LEFT JOIN organisation_representant or_rep ON o.id_organisation = or_rep.id_organisation
                LEFT JOIN utilisateur u ON or_rep.id_utilisateur = u.id_utilisateur
                WHERE o.statut = 'en_attente'
                ORDER BY o.date_creation DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $organisations_en_attente = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
    
    // Section supplémentaire pour les administrateurs : liste de toutes les organisations approuvées
    try {
        $sql = "SELECT o.*, 
                (SELECT GROUP_CONCAT(CONCAT(u.prenom, ' ', u.nom) SEPARATOR ', ') 
                 FROM organisation_representant orep 
                 JOIN utilisateur u ON orep.id_utilisateur = u.id_utilisateur 
                 WHERE orep.id_organisation = o.id_organisation) as representants
                FROM organisation o 
                WHERE o.statut = 'approuvé'
                ORDER BY o.nom";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $organisations_approuvees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Traitement des actions d'approbation/rejet si formulaire soumis
if ($role == '3' && isset($_POST['action']) && isset($_POST['id_organisation'])) {
    $action = $_POST['action'];
    $id_organisation = $_POST['id_organisation'];
    
    try {
        $db->beginTransaction();
        
        if ($action === 'approuver') {
            // Mettre à jour le statut de l'organisation
            $sql = "UPDATE organisation SET statut = 'approuvé' WHERE id_organisation = :id_organisation";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id_organisation' => $id_organisation]);
            
            $id_demandeur = isset($_POST['id_demandeur']) ? intval($_POST['id_demandeur']) : null;
            
            if ($id_demandeur) {
                // Ajouter le demandeur comme représentant de l'organisation
                $sql = "INSERT INTO organisation_representant (id_organisation, id_utilisateur) 
                        VALUES (:id_organisation, :id_utilisateur)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'id_organisation' => $id_organisation,
                    'id_utilisateur' => $id_demandeur
                ]);
                
                // Mettre à jour le rôle de l'utilisateur si nécessaire
                $sql = "UPDATE utilisateur SET role = '2' WHERE id_utilisateur = :id_utilisateur AND role = '1'";
                $stmt = $db->prepare($sql);
                $stmt->execute(['id_utilisateur' => $id_demandeur]);
            }
            
            $db->commit();
            $_SESSION['admin_message'] = "L'organisation a été approuvée avec succès et le représentant a été assigné.";
        } elseif ($action === 'rejeter') {
            $sql = "UPDATE organisation SET statut = 'rejeté' WHERE id_organisation = :id_organisation";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id_organisation' => $id_organisation]);
            $db->commit();
            $_SESSION['admin_message'] = "L'organisation a été rejetée.";
        }
        
        // Rediriger pour éviter les soumissions multiples
        header('Location: dashboard.php');
        exit();
    } catch (Exception $e) {
        $db->rollBack();
        $error_message = $e->getMessage();
    }
}

// Mode gestion des représentants
if ($role == '3' && $mode === 'representants' && $id_organisation > 0) {
    try {
        // Récupérer l'organisation
        $sql = "SELECT * FROM organisation WHERE id_organisation = :id_organisation";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_organisation' => $id_organisation]);
        $organisation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$organisation) {
            $_SESSION['message_error'] = "Organisation introuvable.";
            header('Location: dashboard.php');
            exit();
        }
        
        // Récupérer les représentants actuels
        $sql = "SELECT u.id_utilisateur, u.nom, u.prenom, u.email
                FROM organisation_representant orep
                JOIN utilisateur u ON orep.id_utilisateur = u.id_utilisateur
                WHERE orep.id_organisation = :id_organisation";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_organisation' => $id_organisation]);
        $representants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les utilisateurs qui ne sont pas déjà représentants
        $sql = "SELECT id_utilisateur, nom, prenom, email
                FROM utilisateur
                WHERE role != '3' 
                AND id_utilisateur NOT IN (
                    SELECT id_utilisateur FROM organisation_representant 
                    WHERE id_organisation = :id_organisation
                )";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_organisation' => $id_organisation]);
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Traitement de l'ajout ou suppression d'un représentant
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rep_action'])) {
            $rep_action = $_POST['rep_action'];
            
            try {
                if ($rep_action === 'ajouter' && isset($_POST['id_utilisateur'])) {
                    $id_user = intval($_POST['id_utilisateur']);
                    
                    // Vérifier si l'utilisateur existe
                    $sql = "SELECT COUNT(*) FROM utilisateur WHERE id_utilisateur = :id_utilisateur";
                    $stmt = $db->prepare($sql);
                    $stmt->execute(['id_utilisateur' => $id_user]);
                    
                    if ($stmt->fetchColumn() > 0) {
                        // Mettre à jour le rôle de l'utilisateur
                        $db->beginTransaction();
                        
                        $sql = "UPDATE utilisateur SET role = '2' WHERE id_utilisateur = :id_utilisateur AND role = '1'";
                        $stmt = $db->prepare($sql);
                        $stmt->execute(['id_utilisateur' => $id_user]);
                        
                        // Ajouter l'utilisateur comme représentant
                        $sql = "INSERT INTO organisation_representant (id_organisation, id_utilisateur) 
                                VALUES (:id_organisation, :id_utilisateur)";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([
                            'id_organisation' => $id_organisation,
                            'id_utilisateur' => $id_user
                        ]);
                        
                        $db->commit();
                        $_SESSION['message_success'] = "Le représentant a été ajouté avec succès.";
                    } else {
                        $_SESSION['message_error'] = "Utilisateur introuvable.";
                    }
                } 
                else if ($rep_action === 'supprimer' && isset($_POST['id_utilisateur'])) {
                    $id_user = intval($_POST['id_utilisateur']);
                    
                    // Supprimer le représentant
                    $sql = "DELETE FROM organisation_representant 
                           WHERE id_organisation = :id_organisation AND id_utilisateur = :id_utilisateur";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        'id_organisation' => $id_organisation,
                        'id_utilisateur' => $id_user
                    ]);
                    
                    // Vérifier s'il reste des organisations pour cet utilisateur
                    $sql = "SELECT COUNT(*) FROM organisation_representant WHERE id_utilisateur = :id_utilisateur";
                    $stmt = $db->prepare($sql);
                    $stmt->execute(['id_utilisateur' => $id_user]);
                    
                    // S'il ne représente plus aucune organisation, remettre son rôle à 1 (bénévole)
                    if ($stmt->fetchColumn() == 0) {
                        $sql = "UPDATE utilisateur SET role = '1' WHERE id_utilisateur = :id_utilisateur AND role = '2'";
                        $stmt = $db->prepare($sql);
                        $stmt->execute(['id_utilisateur' => $id_user]);
                    }
                    
                    $_SESSION['message_success'] = "Le représentant a été supprimé avec succès.";
                }
                
                header('Location: dashboard.php?mode=representants&id=' . $id_organisation);
                exit();
            } catch (Exception $e) {
                if (isset($db) && $db->inTransaction()) {
                    $db->rollBack();
                }
                $_SESSION['message_error'] = "Erreur: " . $e->getMessage();
            }
        }
    } catch (Exception $e) {
        $_SESSION['message_error'] = "Erreur: " . $e->getMessage();
        header('Location: dashboard.php');
        exit();
    }
}

// Récupérer les conversations (à ajuster selon votre modèle de données)
$conversations = [];
try {
    $sql = "SELECT c.id_conversation, c.titre as sujet, c.date_creation
            FROM conversation c
            JOIN conversation_participant cp ON c.id_conversation = cp.id_conversation
            WHERE cp.id_utilisateur = :id_utilisateur
            ORDER BY c.date_creation DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id_utilisateur' => $id_utilisateur]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Gérer l'erreur
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="../css/styleRecap.css">
</head>
<body>
    <header>
        <h1>Tableau de bord</h1>
    </header>
    <main class="main">
        <?php if (isset($_SESSION['admin_message'])): ?>
            <div class="alert success">
                <?php echo $_SESSION['admin_message']; unset($_SESSION['admin_message']); ?>
            </div>
        <?php endif; ?>
        
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
        
        <?php if ($role == '3' && $mode === 'representants' && isset($organisation)): ?>
            <!-- Mode gestion des représentants -->
            <div class="back-link">
                <a href="dashboard.php" class="btn">Retour au tableau de bord</a>
            </div>
            
            <h2>Gérer les représentants pour <?php echo htmlspecialchars($organisation['nom']); ?></h2>
            
            <section>
                <h3>Représentants actuels</h3>
                <?php if (empty($representants)): ?>
                    <p>Aucun représentant assigné à cette organisation.</p>
                <?php else: ?>
                    <table class="org-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($representants as $rep): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rep['prenom'] . ' ' . $rep['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($rep['email']); ?></td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="rep_action" value="supprimer">
                                            <input type="hidden" name="id_utilisateur" value="<?php echo $rep['id_utilisateur']; ?>">
                                            <button type="submit" class="btn">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
            
            <section>
                <h3>Ajouter un représentant</h3>
                <?php if (empty($utilisateurs)): ?>
                    <p>Aucun utilisateur disponible à ajouter comme représentant.</p>
                <?php else: ?>
                    <form method="post">
                        <input type="hidden" name="rep_action" value="ajouter">
                        <div class="form-group">
                            <label for="id_utilisateur">Sélectionner un utilisateur:</label>
                            <select name="id_utilisateur" id="id_utilisateur" required>
                                <option value="">-- Choisir un utilisateur --</option>
                                <?php foreach ($utilisateurs as $user): ?>
                                    <option value="<?php echo $user['id_utilisateur']; ?>">
                                        <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom'] . ' (' . $user['email'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn">Ajouter comme représentant</button>
                    </form>
                <?php endif; ?>
            </section>
            
        <?php else: ?>
            <!-- Dashboard normal -->
            <h2>Vos conversations</h2>
            <?php if (empty($conversations)): ?>
                <p>Vous n'avez aucune conversation active.</p>
                <a href="conversation.php" class="btn">Commencer une conversation</a>
            <?php else: ?>
                <table class="org-table">
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
                                <td><?php echo date('d/m/Y H:i', strtotime($conversation['date_creation'])); ?></td>
                                <td><a href="conversation.php?id=<?php echo $conversation['id_conversation']; ?>" class="btn">Voir</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

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
                                        <td><?php echo date('d/m/Y', strtotime($org['date_creation'])); ?></td>
                                        <td>
                                            <?php if ($org['representant_nom']): ?>
                                                <?php echo htmlspecialchars($org['representant_prenom'] . ' ' . $org['representant_nom']); ?>
                                            <?php else: ?>
                                                <em>Non spécifié</em>
                                            <?php endif; ?>
                                        </td>
                                        <td class="approval-btns">
                                            <form method="post">
                                                <input type="hidden" name="id_organisation" value="<?php echo $org['id_organisation']; ?>">
                                                <input type="hidden" name="id_demandeur" value="<?php echo $org['demandeur_id'] ?? ''; ?>">
                                                <input type="hidden" name="action" value="approuver">
                                                <button type="submit" class="btn">Approuver</button>
                                            </form>
                                            <form method="post">
                                                <input type="hidden" name="id_organisation" value="<?php echo $org['id_organisation']; ?>">
                                                <input type="hidden" name="action" value="rejeter">
                                                <button type="submit" class="btn">Rejeter</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>
                
                <!-- Liste des organisations approuvées pour les administrateurs -->
                <section class="admin-panel">
                    <h2>Administration - Organisations approuvées</h2>
                    <?php if (empty($organisations_approuvees)): ?>
                        <p>Aucune organisation approuvée.</p>
                    <?php else: ?>
                        <table class="org-table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Contact</th>
                                    <th>Représentant(s)</th>
                                    <th>Ville</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($organisations_approuvees as $org): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($org['nom']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($org['description'], 0, 100)) . '...'; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($org['email_contact']); ?><br>
                                            <?php echo htmlspecialchars($org['telephone'] ?? ''); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($org['representants'] ?? 'Aucun'); ?></td>
                                        <td><?php echo htmlspecialchars($org['ville'] . ' (' . $org['code_postal'] . ')'); ?></td>
                                        <td>
                                            <a href="organisation.php?mode=detail&id=<?php echo $org['id_organisation']; ?>" class="btn">Voir</a>
                                            <a href="dashboard.php?mode=representants&id=<?php echo $org['id_organisation']; ?>" class="btn">Gérer représentants</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <!-- Section pour faire une demande d'organisation (visible uniquement pour les bénévoles) -->
            <?php if ($role == '1'): ?>
                <h2>Vous avez une organisation ? Faites la demande pour l'ajouter</h2>
                <p>
                    <a href="inscription_organisation.php" class="btn">Inscrire mon organisation</a>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>