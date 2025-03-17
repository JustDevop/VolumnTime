<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif Inscription VolunTime</title>
    <link rel="stylesheet" href="styleIns.css">
</head>
<body>
    <header>
        <h1>Récapitulatif de l'inscription</h1>
    </header>
    <main>
        <div class="recap-container">
            <h2>Vérifiez et modifiez vos informations si nécessaire :</h2>
            <form action="creation_benevole.php" method="POST">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($_POST['prenom']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Numéro de téléphone</label>
                    <input type="tel" id="telephone" name="telephone" pattern="\d{10}" maxlength="10" value="<?php echo htmlspecialchars($_POST['telephone']); ?>" required>
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
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($_POST['ville']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($_POST['adresse']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="postal">Code Postal</label>
                    <input type="number" id="postal" name="postal" value="<?php echo htmlspecialchars($_POST['postal']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="disponibilite">Disponibilité</label>
                    <select id="disponibilite" name="disponibilite" required>
                        <option value="semaine" <?php if ($_POST['disponibilite'] == 'semaine') echo 'selected'; ?>>Semaine</option>
                        <option value="weekend" <?php if ($_POST['disponibilite'] == 'weekend') echo 'selected'; ?>>Week-end</option>
                        <option value="semaine_weekend" <?php if ($_POST['disponibilite'] == 'semaine_weekend') echo 'selected'; ?>>Semaine et Week-end</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="handicap_checkbox">Êtes-vous handicapé ?</label>
                    <input type="checkbox" id="handicap_checkbox" name="handicap_checkbox" <?php if (isset($_POST['handicap_checkbox'])) echo 'checked'; ?> onclick="toggleHandicapField()">
                </div>
                <div class="form-group" id="handicap_field" style="<?php if (!isset($_POST['handicap_checkbox'])) echo 'display: none;'; ?>">
                    <label for="description_handicap">Veuillez préciser votre handicap</label>
                    <textarea id="description_handicap" name="description_handicap" placeholder="Sensoriels, Moteurs, Mentaux, Psychiques et Cognitifs" rows="4" cols="35" ><?php echo htmlspecialchars($_POST['description_handicap']); ?></textarea>
                </div>
                <button type="submit" class="btn">Confirmer</button>
            </form>
        </div>
    </main>
    <script>
        function toggleHandicapField() {
            var handicapField = document.getElementById('handicap_field');
            if (document.getElementById('handicap_checkbox').checked) {
                handicapField.style.display = 'block';
            } else {
                handicapField.style.display = 'none';
            }
        }
    </script>
</body>
</html>