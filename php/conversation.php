<?php
session_start();
include 'config.php'; // Fichier de configuration pour la connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$id_conversation = $_GET['id'];

// Récupérer les messages de la conversation
$sql = "SELECT m.id_message, m.contenu, m.date_envoi, u.nom, u.prenom
        FROM Message m
        JOIN Utilisateur u ON m.id_envoyeur = u.id_utilisateur
        WHERE m.id_conversation = ?
        ORDER BY m.date_envoi ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_conversation);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Conversation</h1>
        <nav class="burger">
            <span class="hamburger">☰</span>
            <ul>
                <li><a href="associationGlobal.php">Associations</a></li>
                <li><a href="mission.php">Missions</a></li>
                <li><a href="conversation.php">Discussions</a></li>
                <li><a href="contact.php">Contacts</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Messages</h2>
        <div class="messages">
            <?php foreach ($messages as $message): ?>
                <div class="message">
                    <p><strong><?php echo htmlspecialchars($message['nom'] . ' ' . $message['prenom']); ?>:</strong> <?php echo htmlspecialchars($message['contenu']); ?></p>
                    <p><small><?php echo htmlspecialchars($message['date_envoi']); ?></small></p>
                </div>
            <?php endforeach; ?>
        </div>
        <form action="send_message.php" method="POST">
            <input type="hidden" name="id_conversation" value="<?php echo $id_conversation; ?>">
            <textarea name="contenu" required></textarea>
            <button type="submit">Envoyer</button>
        </form>
    </main>
</body>
</html>