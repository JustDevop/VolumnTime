<?php
    include("connect_bdd.php");
    //Avant de faire quoi que ce soit, vérif si on est bien connecté avec le bon rôle
    if($_SESSION["role"] != 0){
        $_SESSION['message'] = "Vous n'avez pas la permission pour accéder à cette page.";
        header("location: SAE203.php?error=wrong_role");
        exit();
    }


    //Dans l'état, le code marche, il faut juste vérif pour les doublons - vérif le nom de famille ET/OU le login
    $first_name = strip_tags($_POST["first_name"]);
    $name = strip_tags($_POST["name"]);
    $login_user = strip_tags($_POST["login_user"]);
    $password_user = strip_tags(sha1($_POST["password_user"]));
    $mail = strip_tags($_POST["mail"]);
    $role = strip_tags($_POST["role"]);
    $verif = false;
    try
    {
        $sql ="INSERT INTO sae203_user (first_name,name,login_user,password_user,mail,role_user) VALUES (:first_name,:name,:login_user,:password_user,:mail,:role_user)";
        $compte = $db -> prepare($sql);
        $compte -> execute(array(
            'first_name' => "$first_name",
            'name' => "$name",
            'login_user' => "$login_user",
            'password_user' => $password_user,
            'mail'=> "$mail",
            'role_user'=> $role
        ));
        $verif = !$verif;
    }
    catch(Exception $e)
    {
        die('Erreur'. $e -> getMessage());
    }

    if($verif)    
    {
        $_SESSION['message'] = 'L\'utilisateur à bien été crée.';
        header('Location: admin.php?succes=account_created');
        exit();
    }