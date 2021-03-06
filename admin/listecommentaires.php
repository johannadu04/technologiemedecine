<?php
session_start();
/** On veut lister les articles. On doit soit :
 * 1. Récupérer tous les articles en bdd
 * 2. Afficher les articles ! 
 */

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');
include('../lib/database.lib.php');

userIsConnected();

/** On définie nos variables nécessaire pour la vue et le layout */
$view = 'listecommentaires';      //vue qui sera affichée dans le layout
$pageTitle = 'Tous les commentaires';  //titre de la page qui sera mis dans title et h1 dans le layout

try {
    $flashbag = getFlashBag();

    $bdd = connect();
    $sth = $bdd->prepare('SELECT * FROM ' . DB_PREFIXE . 'comment a INNER JOIN ' . DB_PREFIXE . 'user ON (a.com_email  = use_email) ORDER BY a.com_created_date DESC;');
    $sth->execute();

    $comments = $sth->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {

    $view = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :' . $e->getMessage();
}

include('tpl/layout.phtml');
