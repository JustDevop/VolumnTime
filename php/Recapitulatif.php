<?php
// Ajouter ces lignes au tout début du fichier recapitulatif.php
session_start();

// Stocker le mot de passe haché en session s'il vient du formulaire d'inscription
if (isset($_POST['password'])) {
    $_SESSION["password"] = sha1($_POST['password']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif Inscription VolunTime</title>
    <link rel="stylesheet" href="../css/styleRecap.css">
    <style>
        .edit-btn {
            cursor: pointer;
            margin-left: 10px;
        }
        .readonly {
            background-color: #e9ecef;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <header>
        <h1>Récapitulatif de l'inscription</h1>
    </header>
    <main>
        <div class="formRecap">
            <h2>Vérifiez et modifiez vos informations si nécessaire :</h2>
            <form action="creation_benevole.php" method="POST">
                <div class="nom">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom']); ?>" required readonly class="readonly">
                    <span class="edit-btn" onclick="enableEdit('nom')">✏️</span>
                </div>
                <div class="prenom">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($_POST['prenom']); ?>" required readonly class="readonly">
                    <span class="edit-btn" onclick="enableEdit('prenom')">✏️</span>
                </div>
                <div class="tel">
                    <label for="telephone">Numéro de téléphone</label>
                    <input type="tel" id="telephone" name="telephone" pattern="\d{10}" maxlength="10" value="<?php echo htmlspecialchars($_POST['telephone']); ?>" required readonly class="readonly">
                    <span class="edit-btn" onclick="enableEdit('telephone')">✏️</span>
                </div>
                <div class="mail">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>" required readonly class="readonly">
                    <span class="edit-btn" onclick="enableEdit('email')">✏️</span>
                </div>
                <div class="id">
                    <label for="identifiant">Identifiant</label>
                    <input type="text" id="identifiant" name="identifiant" value="<?php echo htmlspecialchars($_POST['identifiant']); ?>" required readonly class="readonly">
                    <span class="edit-btn" onclick="enableEdit('identifiant')">✏️</span>
                </div>
                <div class="sexe">
                    <label for="sexe">Sexe</label>
                    <select id="sexe" name="sexe" required disabled class="readonly">
                        <option value="homme" <?php if ($_POST['sexe'] == 'homme') echo 'selected'; ?>>Homme</option>
                        <option value="femme" <?php if ($_POST['sexe'] == 'femme') echo 'selected'; ?>>Femme</option>
                        <option value="neutre" <?php if ($_POST['sexe'] == 'neutre') echo 'selected'; ?>>Neutre</option>
                    </select>
                    <span class="edit-btn" onclick="enableEdit('sexe')">✏️</span>
                </div>
                <div class="centre">
                    <label for="interets">Centres d'intérêt</label>
                    <span class="edit-btn" onclick="enableEdit('interets')">✏️</span>
                    <div class="centre1">
                        <p>
                            <input type="checkbox" id="sport" name="interets[]" value="sport" <?php if (in_array('sport', $_POST['interets'])) echo 'checked'; ?> disabled class="readonly">
                            Sport
                        </p>
                        <p>
                            <input type="checkbox" id="musique" name="interets[]" value="musique" <?php if (in_array('musique', $_POST['interets'])) echo 'checked'; ?> disabled class="readonly">
                            Musique
                        </p>
                        <p>
                            <input type="checkbox" id="lecture" name="interets[]" value="lecture" <?php if (in_array('lecture', $_POST['interets'])) echo 'checked'; ?> disabled class="readonly">
                            Lecture
                        </p>
                        <p>
                            <input type="checkbox" id="voyage" name="interets[]" value="voyage" <?php if (in_array('voyage', $_POST['interets'])) echo 'checked'; ?> disabled class="readonly">
                            Voyage
                        </p>
                        <p>
                            <input type="checkbox" id="technologie" name="interets[]" value="technologie" <?php if (in_array('technologie', $_POST['interets'])) echo 'checked'; ?> disabled class="readonly">
                            Technologie
                        </p>
                    </div>
                </div>
                <div class="ville">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($_POST['ville']); ?>" required readonly class="readonly">
                    <span class="edit-btn" onclick="enableEdit('ville')">✏️</span>
                </div>
                <div class="adresse">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($_POST['adresse']); ?>" required readonly class="readonly">
                    <span class="edit-btn" onclick="enableEdit('adresse')">✏️</span>
                </div>
                <div class="postal">
                    <label for="postal">Code Postal</label>
                    <input type="number" id="postal" name="postal" value="<?php echo htmlspecialchars($_POST['postal']); ?>" required readonly class="readonly">
                    <span class="edit-btn" onclick="enableEdit('postal')">✏️</span>
                </div>
                <div class="dispo">
                    <label for="disponibilite">Disponibilité</label>
                    <select id="disponibilite" name="disponibilite" required disabled class="readonly">
                        <option value="semaine" <?php if ($_POST['disponibilite'] == 'semaine') echo 'selected'; ?>>Semaine</option>
                        <option value="weekend" <?php if ($_POST['disponibilite'] == 'weekend') echo 'selected'; ?>>Week-end</option>
                        <option value="semaine_weekend" <?php if ($_POST['disponibilite'] == 'semaine_weekend') echo 'selected'; ?>>Semaine et Week-end</option>
                    </select>
                    <span class="edit-btn" onclick="enableEdit('disponibilite')">✏️</span>
                </div>
                <?php if (isset($_POST['handicap_checkbox'])): ?>
                <div class="handicap">
                    <label for="handicap_checkbox">Êtes-vous handicapé ?</label>
                    <input type="checkbox" id="handicap_checkbox" name="handicap_checkbox" <?php if (isset($_POST['handicap_checkbox'])) echo 'checked'; ?> onclick="toggleHandicapField()">
                </div>
                <div class="descriptionHandi" id="handicap_field">
                    <label for="description_handicap">Veuillez préciser votre handicap</label>
                    <textarea id="description_handicap" name="description_handicap" placeholder="Sensoriels, Moteurs, Mentaux, Psychiques et Cognitifs" rows="4" cols="35"><?php echo htmlspecialchars($_POST['description_handicap']); ?></textarea>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <button type="submit" class="btn">Confirmer</button>
                </div>
            </form>
        </div>
    </main>
    <script>
        function enableEdit(fieldId) {
            if (fieldId === 'interets') {
                var checkboxes = document.querySelectorAll('.centre1 input[type="checkbox"]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.disabled = false;
                    checkbox.classList.remove('readonly');
                });
            } else {
                var field = document.getElementById(fieldId);
                if (field.tagName === 'SELECT' || field.type === 'checkbox') {
                    field.disabled = false;
                    field.classList.remove('readonly');
                } else {
                    field.readOnly = false;
                    field.classList.remove('readonly');
                }
            }
        }

        function toggleHandicapField() {
            var handicapField = document.getElementById('handicap_field');
            var descriptionHandicap = document.getElementById('description_handicap');
            if (document.getElementById('handicap_checkbox').checked) {
                handicapField.style.display = 'block';
                descriptionHandicap.readOnly = false;
                descriptionHandicap.classList.remove('readonly');
            } else {
                handicapField.style.display = 'none';
                descriptionHandicap.readOnly = true;
                descriptionHandicap.classList.add('readonly');
            }
        }
    </script>
</body>
</html>