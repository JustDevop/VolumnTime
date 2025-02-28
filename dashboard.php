<?php
session_start();
include 'config.php'; // Fichier de configuration pour la connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les conversations de l'utilisateur
$sql = "SELECT c.id_conversation, c.sujet, c.date_creation
        FROM Conversation c
        JOIN Message m ON c.id_conversation = m.id_conversation
        WHERE m.id_envoyeur = ? OR m.id_destinataire = ?
        GROUP BY c.id_conversation, c.sujet, c.date_creation
        ORDER BY c.date_creation DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $id_utilisateur, $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();
$conversations = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Tableau de bord</h1>
    </header>
    <main>
        <h2>Vos conversations</h2>
        <table>
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
                        <td><?php echo htmlspecialchars($conversation['date_creation']); ?></td>
                        <td><a href="conversation.php?id=<?php echo $conversation['id_conversation']; ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>