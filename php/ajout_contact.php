<?php
session_start();
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant']) || !isset($_SESSION['id_utilisateur'])) {
    $_SESSION['message_error'] = 'Vous devez être connecté pour ajouter un contact.';
    header('Location: connexion.php');
    exit();
}

// Récupération des données
$id_utilisateur = (int)$_SESSION['id_utilisateur'];
$id_contact = isset($_POST['id_contact']) ? (int)$_POST['id_contact'] : 0;

// Validation des données
if ($id_contact <= 0) {
    $_SESSION['message_error'] = 'Veuillez sélectionner un utilisateur valide.';
    header('Location: contact.php');
    exit();
}

// Vérifier qu'on n'essaie pas de s'ajouter soi-même
if ($id_utilisateur === $id_contact) {
    $_SESSION['message_error'] = 'Vous ne pouvez pas vous ajouter vous-même comme contact.';
    header('Location: contact.php');
    exit();
}

try {
    // Vérifier si l'utilisateur à ajouter existe
    $stmt = $db->prepare("SELECT COUNT(*) FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$id_contact]);
    if ($stmt->fetchColumn() == 0) {
        $_SESSION['message_error'] = 'L\'utilisateur sélectionné n\'existe pas.';
        header('Location: contact.php');
        exit();
    }
    
    // Utiliser INSERT IGNORE pour éviter les erreurs de duplication
    // Cette méthode est plus fiable que de vérifier puis insérer
    $db->beginTransaction();
    
    // Première direction (utilisateur -> contact)
    $stmt = $db->prepare("INSERT IGNORE INTO contact (id_utilisateur, id_contact) VALUES (?, ?)");
    $success1 = $stmt->execute([$id_utilisateur, $id_contact]);
    
    // Deuxième direction (contact -> utilisateur)
    $stmt = $db->prepare("INSERT IGNORE INTO contact (id_utilisateur, id_contact) VALUES (?, ?)");
    $success2 = $stmt->execute([$id_contact, $id_utilisateur]);
    
    // Si au moins une insertion a réussi, c'est qu'il y a eu un changement
    if ($stmt->rowCount() > 0) {
        $_SESSION['message_success'] = 'Contact ajouté avec succès.';
    } else {
        // Les deux insertions ont été ignorées, donc le contact existait déjà
        $_SESSION['message_info'] = 'Ce contact existe déjà dans votre liste.';
    }
    
    $db->commit();
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $_SESSION['message_error'] = 'Erreur lors de l\'ajout du contact : ' . $e->getMessage();
}

// Redirection vers la page des contacts
header('Location: contact.php');
exit();
?>