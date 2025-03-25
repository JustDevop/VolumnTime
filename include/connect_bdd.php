<?php
    session_start();
    try{$db=new PDO('mysql:host=localhost;dbname=db-hauleben','root','');}
    catch(Exception $e){ die('Erreur:' .$e->getMessage() );}
?>