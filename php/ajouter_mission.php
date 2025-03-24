<?php
session_start();
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté et a le droit d'ajouter une mission
if (!isset($_SESSION['identifiant']) || ($_SESSION['role'] != '2' && $_SESSION['role'] != '3')) {
    header('Location: connexion.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_organisation = isset($_GET['id_org']) ? intval($_GET['id_org']) : 0;

// Vérifier si l'organisation existe et si l'utilisateur en est bien un représentant
try {
    if ($_SESSION['role'] == '3') {
        // Pour un administrateur, vérifier simplement si l'organisation existe
        $sql = "SELECT id_organisation, nom FROM organisation WHERE id_organisation = :id_organisation";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_organisation' => $id_organisation]);
    } else {
        // Pour un représentant, vérifier qu'il a les droits sur cette organisation
        $sql = "SELECT o.id_organisation, o.nom 
                FROM organisation o
                JOIN organisation_representant orep ON o.id_organisation = orep.id_organisation
                WHERE o.id_organisation = :id_organisation 
                AND orep.id_utilisateur = :id_utilisateur";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'id_organisation' => $id_organisation,
            'id_utilisateur' => $id_utilisateur
        ]);
    }
    
    $organisation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$organisation) {
        $_SESSION['message_error'] = "Vous n'avez pas le droit d'ajouter une mission à cette organisation.";
        header('Location: organisation.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['message_error'] = "Erreur: " . $e->getMessage();
    header('Location: organisation.php');
    exit();
}

// Récupérer toutes les compétences disponibles
try {
    $sql = "SELECT id_competence, nom FROM competence ORDER BY nom";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $competences = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $competences = [];
}

// Traitement du formulaire d'ajout de mission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $date_debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : '';
    $date_fin = isset($_POST['date_fin']) ? $_POST['date_fin'] : '';
    $lieu = isset($_POST['lieu']) ? trim($_POST['lieu']) : '';
    $nb_places = isset($_POST['nb_places']) ? intval($_POST['nb_places']) : 0;
    $handicap = isset($_POST['handicap']) ? 1 : 0;
    $competences_mission = isset($_POST['competences']) ? $_POST['competences'] : [];
    
    // Validation simple
    $errors = [];
    if (empty($titre)) $errors[] = "Le titre est obligatoire.";
    if (empty($description)) $errors[] = "La description est obligatoire.";
    if (empty($date_debut)) $errors[] = "La date de début est obligatoire.";
    if (empty($date_fin)) $errors[] = "La date de fin est obligatoire.";
    if ($nb_places <= 0) $errors[] = "Le nombre de places doit être positif.";
    
    if (empty($errors)) {
        try {
            $db->beginTransaction();
            
            // Insérer la mission
            $sql = "INSERT INTO mission (id_organisation, titre, description, date_debut, date_fin, lieu, nb_places, statut, handicap) 
                    VALUES (:id_organisation, :titre, :description, :date_debut, :date_fin, :lieu, :nb_places, 'ouverte', :handicap)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id_organisation' => $id_organisation,
                'titre' => $titre,
                'description' => $description,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'lieu' => $lieu,
                'nb_places' => $nb_places,
                'handicap' => $handicap
            ]);
            
            $id_mission = $db->lastInsertId();
            
            // Ajouter les compétences à la mission
            if (!empty($competences_mission)) {
                $sql = "INSERT INTO mission_competence (id_mission, id_competence) VALUES (:id_mission, :id_competence)";
                $stmt = $db->prepare($sql);
                
                foreach ($competences_mission as $id_competence) {
                    $stmt->execute([
                        'id_mission' => $id_mission,
                        'id_competence' => $id_competence
                    ]);
                }
            }
            
            $db->commit();
            $_SESSION['message_success'] = "La mission a été ajoutée avec succès !";
            header('Location: organisation.php');
            exit();
        } catch (Exception $e) {
            $db->rollBack();
            $error_message = "Erreur lors de l'ajout de la mission : " . $e->getMessage();
        }
    } else {
        $error_message = "Veuillez corriger les erreurs suivantes : " . implode(" ", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une mission</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Ajouter une mission</h1>
        <a href="organisation.php" class="back-link">Retour aux organisations</a>
    </header>
    
    <main>
        <section>
            <h2>Nouvelle mission pour : <?php echo htmlspecialchars($organisation['nom']); ?></h2>
            
            <?php if (isset($error_message)): ?>
                <div class="alert error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="form-mission">
                <div class="form-group">
                    <label for="titre">Titre de la mission *</label>
                    <input type="text" id="titre" name="titre" required value="<?php echo isset($titre) ? htmlspecialchars($titre) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="5" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group half">
                        <label for="date_debut">Date de début *</label>
                        <input type="date" id="date_debut" name="date_debut" required value="<?php echo isset($date_debut) ? $date_debut : ''; ?>">
                    </div>
                    
                    <div class="form-group half">
                        <label for="date_fin">Date de fin *</label>
                        <input type="date" id="date_fin" name="date_fin" required value="<?php echo isset($date_fin) ? $date_fin : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="lieu">Lieu</label>
                    <input type="text" id="lieu" name="lieu" value="<?php echo isset($lieu) ? htmlspecialchars($lieu) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="nb_places">Nombre de places *</label>
                    <input type="number" id="nb_places" name="nb_places" min="1" required value="<?php echo isset($nb_places) ? $nb_places : 1; ?>">
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="handicap" name="handicap" <?php echo isset($handicap) && $handicap ? 'checked' : ''; ?>>
                    <label for="handicap">Accessible aux personnes en situation de handicap</label>
                </div>
                
                <div class="form-group">
                    <label>Compétences requises</label>
                    <div class="competences-checkboxes">
                        <?php foreach ($competences as $competence): ?>
                            <div class="competence-item">
                                <input type="checkbox" id="comp_<?php echo $competence['id_competence']; ?>" name="competences[]" value="<?php echo $competence['id_competence']; ?>"
                                <?php echo (isset($competences_mission) && in_array($competence['id_competence'], $competences_mission)) ? 'checked' : ''; ?>>
                                <label for="comp_<?php echo $competence['id_competence']; ?>"><?php echo htmlspecialchars($competence['nom']); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Ajouter la mission</button>
                    <a href="organisation.php" class="btn-secondary">Annuler</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>