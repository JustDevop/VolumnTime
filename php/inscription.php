<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription VolunTime</title>
    <link rel="stylesheet" href="../css/styleIns.css">
</head>
<body>
    <header>
        <h1>Inscription</h1>
    </header>
    <main>
        <div class="formIns">
            <form action="Recapitulatif.php" method="POST">
                <div class="nom">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Sylvain" required>
                </div>
                <div class="prenom">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Jeanker" required>
                </div>
                <div class="tel">
                    <label for="telephone">Numéro de téléphone</label>
                    <input type="tel" id="telephone" name="telephone" pattern="\d{10}" maxlength="10" placeholder="Ex:0612345678" required>
                </div>
                <div class="mail">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="sylvain.jeanker@gmail.com" required>
                </div>
                <div class="id">
                    <label for="identifiant">Identifiant</label>
                    <input type="text" id="identifiant" name="identifiant" placeholder="SylvainJeanker" required>
                </div>
                <div class="mdp">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="sexe">
                    <label for="sexe">Sexe</label>
                    <select id="sexe" name="sexe" required>
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option>
                        <option value="neutre">Neutre</option>
                    </select>
                </div>
                <div class="centre">
                    <label for="interets">Centres d'intérêt</label>
                    <div>
                        <div class="centre1">
                            <p>
                                <input type="checkbox" id="sport" name="interets[]" value="sport">
                                Sport
                            </p>
                            <p>
                                <input type="checkbox" id="musique" name="interets[]" value="musique">
                                Musique
                            </p>
                            <p>
                                <input type="checkbox" id="lecture" name="interets[]" value="lecture">
                                Lecture
                            </p>
                            <p>
                                <input type="checkbox" id="voyage" name="interets[]" value="voyage">
                                Voyage
                            </p>
                            <p>
                                <input type="checkbox" id="technologie" name="interets[]" value="technologie">
                                Technologie
                            </p>
                        </div>
                    </div>
                </div>
                <div class="ville">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" placeholder="Lille" required>
                </div>
                <div class="adresse">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" placeholder="Pl Augustin Laurent" required>
                </div>
                <div class="postal">
                    <label for="postal">Code Postal</label>
                    <input type="number" id="postal" name="postal" placeholder="59000" required>
                </div>
                <div class="dispo">
                    <label for="disponibilite">Disponibilité</label>
                    <select id="disponibilite" name="disponibilite" required>
                        <option value="" disabled selected>Choisissez votre disponibilité</option>
                        <option value="semaine">Semaine</option>
                        <option value="weekend">Week-end</option>
                        <option value="semaine_weekend">Semaine et Week-end</option>
                    </select>
                </div>
                <div class="handicap">
                    <label for="handicap_checkbox">Êtes-vous handicapé ?</label>
                    <input type="checkbox" id="handicap_checkbox" name="handicap_checkbox" onclick="toggleHandicapField()">
                </div>
                <div class="desciptionHandi" id="handicap_field" style="display: none;">
                    <label for="description_handicap">Veuillez préciser votre handicap</label>
                    <textarea id="description_handicap" name="description_handicap" placeholder="Sensoriels, Moteurs, Mentaux, Psychiques et Cognitifs" rows="4" cols="35"></textarea>
                </div>
                <button type="submit" class="btn">S'inscrire</button>
            </form>
        </div>
        <p>Déjà un compte ? <a href="connexion.php"> Connectez vous</a></p>
    </main>
    <script src="../Js/inscription.js"></script>
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