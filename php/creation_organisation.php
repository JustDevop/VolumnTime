<?php
session_start();

include("../include/connect_bdd.php");

// Récupération des données du formulaire
$nom = strip_tags($_POST["nom"]);
$description = strip_tags($_POST["description"]);
$email_contact = strip_tags($_POST["email_contact"]);
$telephone = strip_tags($_POST["telephone"]);
$adresse = strip_tags($_POST["adresse"]);
$ville = strip_tags($_POST["ville"]);
$code_postal = strip_tags($_POST["code_postal"]);
$pays = strip_tags($_POST["pays"]);
$site_web = strip_tags($_POST["site_web"]);



// Vérification des doublons
$verif = false;
try {
    $sql = "SELECT COUNT(*) FROM organisation WHERE nom = :nom OR email_contact = :email_contact";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(
        'nom' => $nom,
        'email_contact' => $email_contact
    ));
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Insertion des données dans la table organisation avec statut "en attente"
        $sql = "INSERT INTO organisation (nom, description, email_contact, telephone, adresse, ville, code_postal, pays, site_web, date_creation, statut)
                VALUES (:nom, :description, :email_contact, :telephone, :adresse, :ville, :code_postal, :pays, :site_web, NOW(), 'en_attente')";
        $compte = $db->prepare($sql);
        $compte->execute(array(
            'nom' => $nom,
            'description' => $description,
            'email_contact' => $email_contact,
            'telephone' => $telephone,
            'adresse' => $adresse,
            'ville' => $ville,
            'code_postal' => $code_postal,
            'pays' => $pays,
            'site_web' => $site_web
        ));
        
        // Récupérer l'ID de l'organisation nouvellement créée
        $id_organisation = $db->lastInsertId();
        
        // Récupérer l'ID numérique de l'utilisateur
        $sql_user = "SELECT id_utilisateur FROM utilisateur WHERE identifiant = :identifiant";
        $stmt_user = $db->prepare($sql_user);
        $stmt_user->execute(['identifiant' => $identifiant]);
        $user_data = $stmt_user->fetch();
        
        if ($user_data) {
            $id_utilisateur = $user_data['id_utilisateur'];
            
            // Maintenant, on peut insérer l'ID numérique dans la table organisation_representant
            $sql = "INSERT INTO organisation_representant (id_organisation, id_utilisateur) VALUES (:id_organisation, :id_utilisateur)";
            $stmt = $db->prepare($sql);
            $stmt->execute(array(
                'id_organisation' => $id_organisation,
                'id_utilisateur' => $id_utilisateur
            ));
        }
        
        $verif = true;
    } else {
        $_SESSION['message'] = 'Une organisation avec ce nom ou cette adresse email existe déjà.';
        header('Location: inscription_organisation.php?error=duplicate');
        exit();
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

if ($verif) {
    $_SESSION['message'] = 'Votre demande d\'inscription a bien été envoyée. Un administrateur l\'examinera prochainement.';
    header('Location: attente_approbation.php');
    exit();
}
?>