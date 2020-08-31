<?php
session_start();

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');
include('../lib/database.lib.php');


/** On vérie que l'utilisateur est connecté et qu'il a le rôle admin 'ROLE_ADMIN'*/
userIsConnected();


/** On définie nos variables nécessaire pour la vue et le layout */
$view = 'listeUser';      //vue qui sera affichée dans le layout
$pageTitle = 'Tous les utilisateurs';  //titre de la page qui sera mis dans title et h1 dans le layout


try {
    $bdd = connect();
    $sth = $bdd->prepare('SELECT use_id,use_lastname,use_firstname,use_email, use_role, use_valid, COUNT(art_title) as articles 
                        FROM ' . DB_PREFIXE . 'user 
                        LEFT JOIN ' . DB_PREFIXE . 'article ON use_id=art_author 
                        GROUP BY use_id');
    $sth->execute();


    $flashbag = getFlashBag();

    $users = $sth->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :' . $e->getMessage();
}

include('tpl/layout.phtml');
