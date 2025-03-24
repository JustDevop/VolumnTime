<?php
session_start();
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant'])) {
    header('Location: connexion.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_organisation = isset($_GET['id_org']) ? intval($_GET['id_org']) : 0;

// Déterminer si l'utilisateur est admin
$est_admin = ($_SESSION['role'] == '3');

// Récupérer les organisations dont l'utilisateur est représentant
try {
    if ($est_admin) {
        // Les admins peuvent créer des missions pour n'importe quelle organisation approuvée
        $sql = "SELECT id_organisation, nom FROM organisation WHERE statut = 'approuvé' ORDER BY nom";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    } else {
        // Utilisateurs normaux - seulement leurs organisations
        $sql = "SELECT o.id_organisation, o.nom 
                FROM organisation o
                JOIN organisation_representant orep ON o.id_organisation = orep.id_organisation
                WHERE orep.id_utilisateur = :id_utilisateur AND o.statut = 'approuvé'
                ORDER BY o.nom";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_utilisateur' => $id_utilisateur]);
    }
    
    $organisations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($organisations)) {
        $_SESSION['message_error'] = "Vous devez être représentant d'une organisation pour créer une mission.";
        header('Location: dashboard.php');
        exit();
    }
    
    // Vérifier si l'ID org passé en GET est valide pour cet utilisateur
    if ($id_organisation > 0) {
        $org_ids = array_column($organisations, 'id_organisation');
        if (!in_array($id_organisation, $org_ids) && !$est_admin) {
            $id_organisation = 0; // Réinitialiser si non autorisé
        }
    }
    
} catch (Exception $e) {
    $_SESSION['message_error'] = "Erreur: " . $e->getMessage();
    header('Location: dashboard.php');
    exit();
}

?>

<main class="main">
    <div class="back-link">
        <a href="organisation.php" class="btn">Retour aux organisations</a>
    </div>
    
    <?php if (isset($_SESSION['message_error'])): ?>
        <div class="alert error">
            <?php echo $_SESSION['message_error']; unset($_SESSION['message_error']); ?>
        </div>
    <?php endif; ?>
    
    <section class="form-container">
        <h2>Créer une nouvelle mission</h2>
        
        <form action="ajouter_mission.php" method="post">
            <!-- Organisation -->
            <div class="form-group">
                <label for="id_organisation">Organisation *</label>
                <select name="id_organisation" id="id_organisation" required>
                    <option value="">-- Sélectionner une organisation --</option>
                    <?php foreach ($organisations as $org): ?>
                        <option value="<?php echo $org['id_organisation']; ?>" <?php echo ($id_organisation == $org['id_organisation']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($org['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Titre de la mission -->
            <div class="form-group">
                <label for="titre">Titre de la mission *</label>
                <input type="text" id="titre" name="titre" required maxlength="100">
            </div>
            
            <!-- Description -->
            <div class="form-group">
                <label for="description">Description de la mission *</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            
            <!-- Dates -->
            <div class="form-row">
                <div class="form-group">
                    <label for="date_debut">Date de début *</label>
                    <input type="date" id="date_debut" name="date_debut" required>
                </div>
                <div class="form-group">
                    <label for="date_fin">Date de fin *</label>
                    <input type="date" id="date_fin" name="date_fin" required>
                </div>
            </div>
            
            <!-- Horaires -->
            <div class="form-group">
                <label for="horaires">Horaires (optionnel)</label>
                <input type="text" id="horaires" name="horaires" placeholder="Ex: 9h-17h, le weekend...">
            </div>
            
            <!-- Lieu -->
            <div class="form-group">
                <label for="lieu">Lieu *</label>
                <input type="text" id="lieu" name="lieu" required>
            </div>
            
            <!-- Nombre de places -->
            <div class="form-group">
                <label for="nb_places">Nombre de places disponibles *</label>
                <input type="number" id="nb_places" name="nb_places" min="1" required>
            </div>
            
            <!-- Compétences requises -->
            <div class="form-group">
                <label for="competences">Compétences requises (optionnel)</label>
                <textarea id="competences" name="competences" rows="3"></textarea>
            </div>
            
            <!-- Handicap -->
            <div class="form-group checkbox-group">
                <input type="checkbox" id="handicap" name="handicap" value="1">
                <label for="handicap">Accessible aux personnes en situation de handicap</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Créer la mission</button>
        </form>
    </section>
</main>

</body>
</html>