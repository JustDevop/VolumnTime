<?php
    include("connect_bdd.php");

    $user_id = strip_tags($_POST["ID"]);
    $user_pwd = strip_tags(sha1($_POST["password"]));

    $sql = "SELECT id_utilisateur, mot_de_passe, role ,email FROM utilisateur";
    $requete=$db->prepare($sql);
    $requete->execute();
    $identification = $requete->fetchAll();
    $connexion = false;

    foreach($identification as $row)
    {
        if ($row["id_utilisateur"] == $user_id && $row["mot_de_passe"] == $user_pwd)
        {
            $_SESSION["login"] = $row["id_utilisateur"];
            $_SESSION["password"] = $row["mot_de_passe"];
            $_SESSION["id"] = $row["email"];
            $_SESSION["role"] = $row["role"];
            $connexion = true;
            break;
        }

    }
    if ($connexion)
    {

        header("Location: index.php");
        /*
        $sql = "SELECT role_user FROM sae203_user WHERE login_user = :login_user";
        $requete = $db->prepare($sql);
        $requete->execute(['login_user' => $_SESSION["login"]]);
        $donnees = $requete->fetch(PDO::FETCH_ASSOC);
        
        if ($donnees) { // Vérifier que $donnees n'est pas vide
            switch ($donnees["role_user"]) {
                case "0":
                    header("Location: admin.php"); // admin
                    break;
                case "1":
                    header("Location: enseignant.php"); // prof
                    break;
                case "2":
                    header("Location: apprenant.php"); // apprenant
                    break;
                default:
                    header("Location: SAE203.php"); // Redirection par défaut si le rôle n'est pas reconnu
                    break;
            }
            exit;
        }
            */
        
    }    
    else{
        //Redirection sur l'ancienne page avec un texte d'erreur
        $_SESSION['message'] = 'Nom d\'utilisateur ou mot de passe incorrect.';
        header('Location: connexion.php');
        exit();
    }
