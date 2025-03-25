<?php
session_start();
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant']) || !isset($_SESSION['id_utilisateur'])) {
    $_SESSION['message_error'] = 'Vous devez être connecté pour créer une conversation.';
    header('Location: connexion.php');
    exit();
}

// Vérification du token CSRF
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['message_error'] = "Erreur de sécurité. Veuillez réessayer.";
    header('Location: conversation.php');
    exit();
}

// Récupération des données
$id_utilisateur = $_SESSION['id_utilisateur'];
$contact_id = isset($_POST['contact_id']) ? intval($_POST['contact_id']) : 0;

// Validation des données
if ($contact_id <= 0) {
    $_SESSION['message_error'] = 'Contact invalide.';
    header('Location: conversation.php');
    exit();
}

// Vérification que le contact existe et est bien un contact de l'utilisateur
try {
    $sql = "SELECT COUNT(*) FROM contact WHERE id_utilisateur = :id_utilisateur AND id_contact = :contact_id";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id_utilisateur' => $id_utilisateur,
        'contact_id' => $contact_id
    ]);
    
    if ($stmt->fetchColumn() == 0) {
        $_SESSION['message_error'] = "Ce contact n'existe pas ou n'est pas dans votre liste de contacts.";
        header('Location: conversation.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['message_error'] = "Erreur: " . $e->getMessage();
    header('Location: conversation.php');
    exit();
}

// Vérifier si une conversation existe déjà entre ces deux utilisateurs (conversation privée)
try {
    // Requête simplifiée pour trouver une conversation entre 2 personnes spécifiques
    $sql = "SELECT c.id_conversation
            FROM conversation c
            JOIN conversation_participant cp1 ON c.id_conversation = cp1.id_conversation AND cp1.id_utilisateur = :id_utilisateur
            JOIN conversation_participant cp2 ON c.id_conversation = cp2.id_conversation AND cp2.id_utilisateur = :contact_id
            WHERE (SELECT COUNT(*) FROM conversation_participant WHERE id_conversation = c.id_conversation) = 2";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id_utilisateur' => $id_utilisateur,
        'contact_id' => $contact_id
    ]);
    
    $existing_conversation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_conversation) {
        // Rediriger vers la conversation existante
        header('Location: conversation.php?id=' . $existing_conversation['id_conversation']);
        exit();
    }
} catch (Exception $e) {
    $_SESSION['message_error'] = "Erreur lors de la vérification des conversations existantes: " . $e->getMessage();
    header('Location: conversation.php');
    exit();
}

// Créer une nouvelle conversation
try {
    // Utilisation d'une transaction pour garantir l'intégrité des données
    $db->beginTransaction();
    
    // Récupérer les informations des participants pour le titre
    $sql = "SELECT id_utilisateur, CONCAT(prenom, ' ', nom) as nom_complet 
            FROM utilisateur 
            WHERE id_utilisateur IN (:id_utilisateur, :contact_id)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id_utilisateur' => $id_utilisateur,
        'contact_id' => $contact_id
    ]);
    $participants = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Créer un titre significatif
    $titre = "Conversation: " . implode(" & ", $participants);
    
    // Créer la conversation
    $sql = "INSERT INTO conversation (titre, date_creation) VALUES (:titre, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute(['titre' => $titre]);
    
    $id_conversation = $db->lastInsertId();
    
    // Ajouter les participants
    $sql = "INSERT INTO conversation_participant (id_conversation, id_utilisateur) VALUES (:id_conversation, :id_utilisateur)";
    $stmt = $db->prepare($sql);
    
    foreach (array_keys($participants) as $participant_id) {
        $stmt->execute([
            'id_conversation' => $id_conversation,
            'id_utilisateur' => $participant_id
        ]);
    }
    
    $db->commit();
    
    // Rediriger vers la nouvelle conversation
    header('Location: conversation.php?id=' . $id_conversation);
    exit();
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $_SESSION['message_error'] = "Erreur lors de la création de la conversation: " . $e->getMessage();
    header('Location: conversation.php');
    exit();
}
?>