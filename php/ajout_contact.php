<?php
    session_start();
    include("include/connect_bdd.php");
    
    // Vérifiez si l'utilisateur est connecté
    if (!isset($_SESSION['id_utilisateur'])) {
        die('Vous devez être connecté pour ajouter un contact.');
    }

    // Récupération des données
    $id_utilisateur = $_SESSION['id_utilisateur'];
    $id_contact = strip_tags($_POST['id_contact']); // Vérifier POST / GET / La ligne est suspectible de changer 

    // Vérifiez si l'utilisateur à ajouter existe
    $stmt = $db->prepare("SELECT COUNT(*) FROM utilisateur WHERE id_utilisateur = :id_contact");
    $stmt->execute(array('id_contact' => $id_contact));
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        die('L\'utilisateur à ajouter n\'existe pas.');
    }

    // Ajout du contact dans la table
    try {
        $sql = "INSERT INTO contact (id_utilisateur, id_contact) VALUES (:id_utilisateur, :id_contact), (:id_contact, :id_utilisateur)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(
            'id_utilisateur' => $id_utilisateur,
            'id_contact' => $id_contact
        ));
        echo 'Contact ajouté avec succès.';
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
?>