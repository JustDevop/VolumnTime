<?php
session_start();
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant'])) {
    header('Location: connexion.php');
    exit();
}

// Récupérer l'ID de l'utilisateur à afficher
// Si un ID est fourni en paramètre, on affiche cet utilisateur
// Sinon, on affiche l'utilisateur connecté
$id_profil = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id_utilisateur'];

// Récupérer les informations de l'utilisateur
try {
    $sql = "SELECT id_utilisateur, nom, prenom, email, identifiant, role, 
                  tagUsers, telephone, adresse, ville, code_postal, date_inscription, 
                  handicap, description_handicap 
           FROM utilisateur 
           WHERE id_utilisateur = :id_utilisateur";
    
    $stmt = $db->prepare($sql);
    $stmt->execute(['id_utilisateur' => $id_profil]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$utilisateur) {
        // Si l'utilisateur n'existe pas, rediriger vers la page de profil personnelle
        $_SESSION['message_error'] = "L'utilisateur demandé n'existe pas.";
        header('Location: profil.php');
        exit();
    }
    
    // Récupérer les compétences de l'utilisateur
    $sql_competences = "SELECT c.id_competence, c.nom
                        FROM competence c
                        JOIN utilisateur_competence uc ON c.id_competence = uc.id_competence
                        WHERE uc.id_utilisateur = :id_utilisateur";
    
    $stmt_comp = $db->prepare($sql_competences);
    $stmt_comp->execute(['id_utilisateur' => $id_profil]);
    $competences = $stmt_comp->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $_SESSION['message_error'] = "Erreur lors de la récupération des données: " . $e->getMessage();
    header('Location: dashboard.php');
    exit();
}

// Vérifier si l'utilisateur courant consulte son propre profil
$profil_personnel = ($id_profil == $_SESSION['id_utilisateur']);

// Pour le titre de la page
$titre_page = $profil_personnel ? "Mon profil" : "Profil de " . $utilisateur['prenom'] . " " . $utilisateur['nom'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolunTime - <?php echo $titre_page; ?></title>
    <link rel="stylesheet" href="../css/styleProfil.css">
</head>
<body>
    <header>
        <h1><?php echo $titre_page; ?></h1>
    </header>
    
    <main>
        <div class="back-link">
            <a href="dashboard.php" class="btn">Retour au tableau de bord</a>
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
        
        <section class="profil-container">
            <div class="profil-header">
                <div class="profil-info-header">
                    <h2><?php echo htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?></h2>
                    <p class="username">@<?php echo htmlspecialchars($utilisateur['identifiant']); ?></p>
                    
                    <?php if ($utilisateur['tagUsers']): ?>
                        <p class="tag">#<?php echo htmlspecialchars($utilisateur['tagUsers']); ?></p>
                    <?php endif; ?>
                    
                    <p class="membre-depuis">Membre depuis : <?php echo date('d/m/Y', strtotime($utilisateur['date_inscription'])); ?></p>
                    
                    <?php if (!$profil_personnel): ?>
                        <div class="actions-profil">
                            <a href="conversation.php?contact_id=<?php echo $utilisateur['id_utilisateur']; ?>" class="btn">Envoyer un message</a>
                            <?php 
                            // Vérifier si l'utilisateur est déjà dans les contacts
                            $stmt_contact = $db->prepare("SELECT COUNT(*) FROM contact WHERE id_utilisateur = :id_utilisateur AND id_contact = :id_contact");
                            $stmt_contact->execute([
                                'id_utilisateur' => $_SESSION['id_utilisateur'],
                                'id_contact' => $utilisateur['id_utilisateur']
                            ]);
                            $est_contact = $stmt_contact->fetchColumn() > 0;
                            ?>
                            
                            <?php if (!$est_contact): ?>
                                <form action="ajout_contact.php" method="POST">
                                    <input type="hidden" name="id_contact" value="<?php echo $utilisateur['id_utilisateur']; ?>">
                                    <button type="submit" class="btn">Ajouter aux contacts</button>
                                </form>
                            <?php else: ?>
                                <span class="contact-info">Déjà dans vos contacts</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="profil-details">
                <div class="profil-section">
                    <h3>Informations personnelles</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Email :</span>
                            <span class="value"><?php echo htmlspecialchars($utilisateur['email']); ?></span>
                        </div>
                        
                        <?php if ($utilisateur['telephone']): ?>
                            <div class="info-item">
                                <span class="label">Téléphone :</span>
                                <span class="value"><?php echo htmlspecialchars($utilisateur['telephone']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($utilisateur['ville'] && $utilisateur['code_postal']): ?>
                            <div class="info-item">
                                <span class="label">Localisation :</span>
                                <span class="value"><?php echo htmlspecialchars($utilisateur['ville'] . ' (' . $utilisateur['code_postal'] . ')'); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($utilisateur['handicap'] == 1): ?>
                            <div class="info-item">
                                <span class="label">Handicap :</span>
                                <span class="value"><?php echo htmlspecialchars($utilisateur['description_handicap'] ?: 'Non précisé'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="profil-section">
                    <h3>Compétences</h3>
                    <?php if (empty($competences)): ?>
                        <p>Aucune compétence renseignée</p>
                    <?php else: ?>
                        <div class="competences-list">
                            <?php foreach ($competences as $competence): ?>
                                <span class="competence-tag"><?php echo htmlspecialchars($competence['nom']); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($profil_personnel): ?>
                    <div class="profil-actions">
                        <a href="modifier_profil.php" class="btn">Modifier mon profil</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>


