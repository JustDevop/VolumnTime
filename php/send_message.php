<?php
session_start();
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant']) || !isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// Récupération et validation des données
$id_utilisateur = $_SESSION['id_utilisateur'];
$id_conversation = isset($_POST['id_conversation']) ? (int)$_POST['id_conversation'] : 0;
$contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
$token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

// Validation des données
$errors = [];

// Vérifier le token CSRF
if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
    $errors[] = "Erreur de sécurité, veuillez réessayer.";
}

// Vérifier que l'ID de conversation est valide
if ($id_conversation <= 0) {
    $errors[] = "Conversation invalide.";
}

// Vérifier que le contenu n'est pas vide
if (empty($contenu)) {
    $errors[] = "Le message ne peut pas être vide.";
}

// Vérifier que l'utilisateur participe à cette conversation
try {
    $sql = "SELECT COUNT(*) FROM conversation_participant 
            WHERE id_conversation = :id_conversation 
            AND id_utilisateur = :id_utilisateur";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id_conversation' => $id_conversation,
        'id_utilisateur' => $id_utilisateur
    ]);
    
    if ($stmt->fetchColumn() == 0) {
        $errors[] = "Vous n'êtes pas autorisé à accéder à cette conversation.";
    }
} catch (Exception $e) {
    $errors[] = "Erreur de vérification : " . $e->getMessage();
}

// S'il y a des erreurs, rediriger avec un message d'erreur
if (!empty($errors)) {
    $_SESSION['message_error'] = implode('<br>', $errors);
    header('Location: conversation.php?id=' . $id_conversation);
    exit();
}

// Insérer le nouveau message
try {
    $sql = "INSERT INTO message (id_conversation, id_envoyeur, contenu, date_envoi)
            VALUES (:id_conversation, :id_envoyeur, :contenu, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id_conversation' => $id_conversation,
        'id_envoyeur' => $id_utilisateur,
        'contenu' => $contenu
    ]);
    
    $_SESSION['message_success'] = "Message envoyé avec succès.";
} catch (Exception $e) {
    $_SESSION['message_error'] = "Erreur d'envoi : " . $e->getMessage();
}

// Générer un nouveau token CSRF pour la prochaine requête
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

header('Location: conversation.php?id=' . $id_conversation);
exit();
?>