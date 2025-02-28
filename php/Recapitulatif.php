<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif Inscription VolunTime</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Récapitulatif de l'inscription</h1>
    </header>
    <main>
        <div class="recap-container">
            <h2>Vérifiez et modifiez vos informations si nécessaire :</h2>
            <form action="confirmation.php" method="POST">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($_POST['prenom']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="identifiant">Identifiant</label>
                    <input type="text" id="identifiant" name="identifiant" value="<?php echo htmlspecialchars($_POST['identifiant']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Sexe</label>
                    <div>
                        <input type="radio" id="homme" name="sexe" value="homme" <?php if ($_POST['sexe'] == 'homme') echo 'checked'; ?> required>
                        <label for="homme">Homme</label>
                    </div>
                    <div>
                        <input type="radio" id="femme" name="sexe" value="femme" <?php if ($_POST['sexe'] == 'femme') echo 'checked'; ?> required>
                        <label for="femme">Femme</label>
                    </div>
                    <div>
                        <input type="radio" id="neutre" name="sexe" value="neutre" <?php if ($_POST['sexe'] == 'neutre') echo 'checked'; ?> required>
                        <label for="neutre">Neutre</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="interets">Centres d'intérêt</label>
                    <select id="interets" name="interets[]" multiple required>
                        <option value="sport" <?php if (in_array('sport', $_POST['interets'])) echo 'selected'; ?>>Sport</option>
                        <option value="musique" <?php if (in_array('musique', $_POST['interets'])) echo 'selected'; ?>>Musique</option>
                        <option value="lecture" <?php if (in_array('lecture', $_POST['interets'])) echo 'selected'; ?>>Lecture</option>
                        <option value="voyage" <?php if (in_array('voyage', $_POST['interets'])) echo 'selected'; ?>>Voyage</option>
                        <option value="technologie" <?php if (in_array('technologie', $_POST['interets'])) echo 'selected'; ?>>Technologie</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="localisation">Localisation</label>
                    <input type="text" id="localisation" name="localisation" value="<?php echo htmlspecialchars($_POST['localisation']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="disponibilite">Disponibilité</label>
                    <select id="disponibilite" name="disponibilite" required>
                        <option value="semaine" <?php if ($_POST['disponibilite'] == 'semaine') echo 'selected'; ?>>Semaine</option>
                        <option value="weekend" <?php if ($_POST['disponibilite'] == 'weekend') echo 'selected'; ?>>Week-end</option>
                    </select>
                </div>
                <button type="submit" class="btn">Confirmer</button>
            </form>
        </div>
    </main>
</body>
</html>