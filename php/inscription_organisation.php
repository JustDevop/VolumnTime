<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Organisation - VolunTime</title>
    <link rel="stylesheet" href="../css/styleIns.css">
</head>
<body>
    <header>
        <h1>Inscription d'une Organisation</h1>
    </header>
    <main>
        <div class="signup-container">
            <form action="recapitulatif_organisation.php" method="POST">
                <div class="form-group">
                    <label for="nom">Nom de l'organisation</label>
                    <input type="text" id="nom" name="nom" placeholder="Nom de votre organisation" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Décrivez votre organisation" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="email">Email de contact</label>
                    <input type="email" id="email" name="email_contact" placeholder="contact@organisation.com" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Numéro de téléphone</label>
                    <input type="tel" id="telephone" name="telephone" pattern="\d{10}" maxlength="10" placeholder="Ex: 0612345678" required>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" placeholder="123 rue du Bénévolat" required>
                </div>
                <div class="form-group">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" placeholder="Paris" required>
                </div>
                <div class="form-group">
                    <label for="code_postal">Code Postal</label>
                    <input type="text" id="code_postal" name="code_postal" placeholder="75000" required>
                </div>
                <div class="form-group">
                    <label for="pays">Pays</label>
                    <input type="text" id="pays" name="pays" placeholder="France" required>
                </div>
                <div class="form-group">
                    <label for="site_web">Site Web</label>
                    <input type="url" id="site_web" name="site_web" placeholder="https://www.organisation.com">
                </div>
                <button type="submit" class="btn">Inscrire l'organisation</button>
            </form>
        </div>
    </main>
</body>
</html>