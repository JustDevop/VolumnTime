<?php
    include("connect_bdd.php");

    // Récupération des données du formulaire
    $nom = strip_tags($_POST["nom"]);
    $prenom = strip_tags($_POST["prenom"]);
    $mail = strip_tags($_POST["email"]);
    $identifiant = strip_tags($_POST["identifiant"]);
    $password = strip_tags(sha1($_POST["password"]));
    $sexe = strip_tags($_POST["sexe"]);
    $telephone = strip_tags($_POST["telephone"]);
    $interets = implode(",", $_POST["interets"]);
    $ville = strip_tags($_POST["ville"]);
    $adresse = strip_tags($_POST["adresse"]);
    $code_postal = strip_tags($_POST["code_postal"]);
    $disponibilite = strip_tags($_POST["disponibilite"]);
    $role = "1"; // Role par défaut
    $disponibilite = strip_tags($_POST["disponibilite"]);
    $handicap = strip_tags($_POST["handicap"]);
    $description_handicap = strip_tags($_POST["description_handicap"]);

    // Vérification des doublons
    $verif = false;
    try {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE nom = :nom OR email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(
            'nom' => $nom,
            'email' => $mail
        ));
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Insertion des données dans la base de données
            $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, tagUsers, telephone, adresse, ville, code_postal, pays, date_inscription, handicap) 
                    VALUES (:nom, :prenom, :email, :mot_de_passe, :role, :tagUsers, :telephone,:adresse, :ville, :code_postal, :pays, NOW(), :handicap)";
            $compte = $db->prepare($sql);
            $compte->execute(array(
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $mail,
                'mot_de_passe' => $password,
                'role' => $role,
                'telephone' => $telephone,
                'tagUsers' => $interets,
                'adresse' => $localisation,
                'ville' => $ville,	
                'code_postal' => $code_postal,
                'handicap' => $handicap
            ));
            $verif = true;
        } else {
            $_SESSION['message'] = 'Un utilisateur avec ce nom ou email existe déjà.';
            header('Location: inscription.php?error=duplicate');
            exit();
        }
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    if ($verif) {
        $_SESSION['message'] = 'L\'utilisateur a bien été créé.';
        header('Location: contact.php');
        exit();
    }
