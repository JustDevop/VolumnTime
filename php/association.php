<?php
session_start();
include '../include/connect_bdd.php'; 

// Vérifier si l'utilisateur est connecté et qu'il est le représentant d'une association (rôle 2)
if (!isset($_SESSION['identifiant']) && ($_SESSION['role'] == 2)) {
    header('Location: connexion.php');
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les associations de l'utilisateur
$sql = "SELECT o.id_organisation, o.nom, o.description, o.date_creation
        FROM organisation o
        JOIN organisation_representant orp ON o.id_organisation = orp.id_organisation
        WHERE orp.id_utilisateur = ?
        ORDER BY o.date_creation DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();
$associations = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord des associations</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Tableau de bord des associations</h1>
    </header>
    <main>
        <h2>Vos associations</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($associations as $association): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($association['nom']); ?></td>
                        <td><?php echo htmlspecialchars($association['description']); ?></td>
                        <td><?php echo htmlspecialchars($association['date_creation']); ?></td>
                        <td><a href="association_detail.php?id=<?php echo $association['id_organisation']; ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>