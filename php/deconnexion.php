<?php
    session_start();
    require '../include/connect_bdd.php';
    
    session_unset();
    session_destroy();

    header('Location: connexion.php');
