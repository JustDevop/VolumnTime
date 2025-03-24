<?php
session_start();
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant']) || !isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_mission = isset($_POST['id_mission']) ? intval($_POST['id_mission']) : 0;

// Validation
if ($id_mission <= 0) {
    $_SESSION['message_error'] = "Mission invalide.";
    header('Location: dashboard.php');
    exit();
}

try {
    // Vérifier si la mission existe et est ouverte
    $sql = "SELECT m.*, o.nom as nom_organisation 
            FROM mission m
            JOIN organisation o ON m.id_organisation = o.id_organisation
            WHERE m.id_mission = :id_mission AND m.statut = 'ouverte'";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id_mission' => $id_mission]);
    $mission = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$mission) {
        $_SESSION['message_error'] = "La mission n'existe pas ou n'est plus disponible.";
        header('Location: dashboard.php');
        exit();
    }
    
    // Vérifier si l'utilisateur est déjà inscrit
    $sql = "SELECT COUNT(*) FROM inscription 
            WHERE id_mission = :id_mission AND id_utilisateur = :id_utilisateur";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id_mission' => $id_mission,
        'id_utilisateur' => $id_utilisateur
    ]);
    
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['message_error'] = "Vous êtes déjà inscrit à cette mission.";
        header('Location: mission_detail.php?id=' . $id_mission);
        exit();
    }
    
    // Vérifier s'il reste des places
    $sql = "SELECT m.nb_places, COUNT(i.id_inscription) as inscrits
            FROM mission m
            LEFT JOIN inscription i ON m.id_mission = i.id_mission AND i.statut != 'annulée'
            WHERE m.id_mission = :id_mission
            GROUP BY m.id_mission";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id_mission' => $id_mission]);
    $places = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($places['inscrits'] >= $places['nb_places']) {
        $_SESSION['message_error'] = "Désolé, toutes les places pour cette mission sont déjà prises.";
        header('Location: mission_detail.php?id=' . $id_mission);
        exit();
    }
    
    // Tout est bon, procéder à l'inscription
    $sql = "INSERT INTO inscription (id_utilisateur, id_mission, statut) 
            VALUES (:id_utilisateur, :id_mission, 'en attente')";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id_utilisateur' => $id_utilisateur,
        'id_mission' => $id_mission
    ]);
    
    $_SESSION['message_success'] = "Votre demande d'inscription à la mission \"" . $mission['titre'] . "\" a été enregistrée. L'organisation " . $mission['nom_organisation'] . " examinera votre candidature.";
    header('Location: mission_detail.php?id=' . $id_mission);
    exit();
    
} catch (Exception $e) {
    $_SESSION['message_error'] = "Erreur lors de l'inscription : " . $e->getMessage();
    header('Location: dashboard.php');
    exit();
}
?>