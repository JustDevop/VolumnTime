<?php
    session_start();
    try{$db=new PDO('mysql:host=localhost;dbname=voluntime','root','');}
    catch(Exception $e){ die('Erreur:' .$e->getMessage() );}
?>