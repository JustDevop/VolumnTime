<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande en attente - VolunTime</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>VolunTime</h1>
    </header>
    <main>
        <div class="waiting-container">
            <h2 class="waiting-title">Demande d'inscription en attente d'approbation</h2>
            
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert success">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php else: ?>
                <p class="waiting-text">
                    Votre demande d'inscription pour l'organisation a bien été reçue. 
                    Elle est actuellement en cours d'examen par nos administrateurs.
                </p>
            <?php endif; ?>
            
            <p class="waiting-text">
                Ce processus peut prendre jusqu'à 48 heures ouvrables. 
                Vous recevrez une notification par email à l'adresse fournie lors de l'inscription 
                dès que votre demande sera traitée.
            </p>
            
            <p class="waiting-text">
                En attendant, vous pouvez continuer à explorer la plateforme VolunTime 
                et découvrir les opportunités de bénévolat disponibles.
            </p>
            
            <a href="../index.php" class="btn">Retour à l'accueil</a>
        </div>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> VolunTime - Tous droits réservés</p>
    </footer>
</body>
</html>