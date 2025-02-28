<?php
    session_start();
try
    {
        $db = new PDO('mysql:host=localhost;dbname=db-hauleben' , 'usr-hauleben' , '^Rez4)Ax2y<3',[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
catch (Exception $e)
     {
         die ('Erreur : ' . $e->getMessage ( )) ;
    }
