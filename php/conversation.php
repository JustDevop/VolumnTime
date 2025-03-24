<?php
session_start();
include '../include/connect_bdd.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['identifiant'])) {
    header('Location: connexion.php');
    exit();
}
$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les contacts de l'utilisateur
try {
    $sql = "SELECT u.id_utilisateur, u.nom, u.prenom 
            FROM contact c
            JOIN utilisateur u ON c.id_contact = u.id_utilisateur
            WHERE c.id_utilisateur = :id_utilisateur
            ORDER BY u.nom, u.prenom";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id_utilisateur' => $id_utilisateur]);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

// Récupérer les conversations de l'utilisateur
try {
    $sql = "SELECT c.id_conversation, c.titre, c.date_creation
            FROM conversation c
            JOIN conversation_participant cp ON c.id_conversation = cp.id_conversation
            WHERE cp.id_utilisateur = :id_utilisateur
            ORDER BY c.date_creation DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id_utilisateur' => $id_utilisateur]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

// Si l'ID de conversation est fourni, récupérer les messages de cette conversation
$id_conversation = isset($_GET['id']) ? intval($_GET['id']) : null;
$messages = [];

if ($id_conversation) {
    // Vérifier que l'utilisateur est bien participant à cette conversation
    $stmt = $db->prepare("SELECT COUNT(*) FROM conversation_participant WHERE id_conversation = :id_conversation AND id_utilisateur = :id_utilisateur");
    $stmt->execute(['id_conversation' => $id_conversation, 'id_utilisateur' => $id_utilisateur]);
    if ($stmt->fetchColumn() == 0) {
        header('Location: dashboard.php');
        exit();
    }

    // Récupérer les messages de la conversation
    try {
        $sql = "SELECT m.id_message, m.contenu, m.date_envoi, u.nom, u.prenom, m.id_envoyeur
                FROM message m
                JOIN utilisateur u ON m.id_envoyeur = u.id_utilisateur
                WHERE m.id_conversation = :id_conversation
                ORDER BY m.date_envoi ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id_conversation' => $id_conversation]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}

// Générer un token CSRF s'il n'existe pas déjà
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversation</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Conversation</h1>
        <a href="dashboard.php" class="back-link">Retour au tableau de bord</a>
    </header>
    
    <!-- Affichage des messages d'erreur/succès -->
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
    
    <main>
        <div class="conversation-container">
            <div class="sidebar">
                <h2>Mes conversations</h2>
                <ul class="conversation-list">
                    <?php if (empty($conversations)): ?>
                        <li>Aucune conversation active</li>
                    <?php else: ?>
                        <?php foreach ($conversations as $conv): ?>
                            <li>
                                <a href="conversation.php?id=<?php echo $conv['id_conversation']; ?>" 
                                   class="<?php echo ($id_conversation == $conv['id_conversation']) ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($conv['titre'] ?: 'Conversation du ' . date('d/m/Y', strtotime($conv['date_creation']))); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                
                <h2>Mes contacts</h2>
                <ul class="contact-list">
                    <?php if (empty($contacts)): ?>
                        <li>Aucun contact ajouté</li>
                        <li><a href="contact.php" class="btn-link">Ajouter des contacts</a></li>
                    <?php else: ?>
                        <?php foreach ($contacts as $contact): ?>
                            <li>
                                <div class="contact-info">
                                    <?php echo htmlspecialchars($contact['prenom'] . ' ' . $contact['nom']); ?>
                                    <button class="btn-new-conv" onclick="startConversation(<?php echo $contact['id_utilisateur']; ?>)">
                                        Nouvelle conversation
                                    </button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <a href="contact.php" class="btn-link">Gérer mes contacts</a>
            </div>
            
            <div class="message-area">
                <?php if ($id_conversation): ?>
                    <h2>Messages</h2>
                    <div class="messages">
                        <?php if (empty($messages)): ?>
                            <p>Aucun message dans cette conversation.</p>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="message <?php echo ($message['id_envoyeur'] == $id_utilisateur) ? 'my-message' : ''; ?>">
                                    <p><strong><?php echo htmlspecialchars($message['prenom'] . ' ' . $message['nom']); ?> :</strong> <?php echo htmlspecialchars($message['contenu']); ?></p>
                                    <p class="message-time"><small><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($message['date_envoi']))); ?></small></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <form action="send_message.php" method="POST" class="message-form">
                        <input type="hidden" name="id_conversation" value="<?php echo htmlspecialchars($id_conversation); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <textarea name="contenu" required placeholder="Écrivez votre message ici..."></textarea>
                        <button type="submit" class="btn-send">Envoyer</button>
                    </form>
                <?php else: ?>
                    <div class="no-conversation">
                        <p>Sélectionnez une conversation ou démarrez-en une nouvelle avec l'un de vos contacts.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Formulaire caché pour créer une nouvelle conversation -->
    <form id="new-conversation-form" action="create_conversation.php" method="POST" style="display: none;">
        <input type="hidden" name="contact_id" id="contact_id">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    </form>

    <script>
        function startConversation(contactId) {
            console.log("Démarrage de conversation avec ID: " + contactId);
            document.getElementById('contact_id').value = contactId;
            document.getElementById('new-conversation-form').submit();
        }
    </script>
</body>
</html>