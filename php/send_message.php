<?php
session_start();
include 'config.php'; // Fichier de configuration pour la connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_conversation = $_POST['id_conversation'];
$contenu = $_POST['contenu'];

// Insérer le nouveau message
$sql = "INSERT INTO Message (id_conversation, id_envoyeur, id_destinataire, contenu, date_envoi)
        VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiis', $id_conversation, $id_utilisateur, $id_destinataire, $contenu);
$stmt->execute();

header('Location: conversation.php?id=' . $id_conversation);
exit();
?>
