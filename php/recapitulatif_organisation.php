<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif Organisation - VolunTime</title>
    <link rel="stylesheet" href="../css/styleRecap.css">
</head>
<body>
    <header>
        <h1>Récapitulatif de l'inscription de l'organisation</h1>
    </header>
    <main>
        <div class="recap-container">
            <h2>Vérifiez et modifiez les informations de votre organisation si nécessaire :</h2>
            <form action="creation_organisation.php" method="POST">
                <div class="form-group">
                    <label for="nom">Nom de l'organisation</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($_POST['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="email">Email de contact</label>
                    <input type="email" id="email" name="email_contact" value="<?php echo htmlspecialchars($_POST['email_contact']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Numéro de téléphone</label>
                    <input type="tel" id="telephone" name="telephone" pattern="\d{10}" maxlength="10" value="<?php echo htmlspecialchars($_POST['telephone']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($_POST['adresse']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($_POST['ville']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="code_postal">Code Postal</label>
                    <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($_POST['code_postal']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="pays">Pays</label>
                    <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($_POST['pays']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="site_web">Site Web</label>
                    <input type="url" id="site_web" name="site_web" value="<?php echo htmlspecialchars($_POST['site_web']); ?>">
                </div>
                <button type="submit" class="btn">Confirmer l'inscription</button>
            </form>
        </div>
    </main>
</body>
</html>