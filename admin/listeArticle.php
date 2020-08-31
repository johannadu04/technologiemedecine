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
$view = 'listeArticle';      //vue qui sera affichée dans le layout
$pageTitle = 'Tous les articles';  //titre de la page qui sera mis dans title et h1 dans le layout

try
{
    $flashbag = getFlashBag();

    $bdd = connect();
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article 
                        INNER JOIN '.DB_PREFIXE.'user ON (art_author=use_id) 
                        LEFT JOIN '.DB_PREFIXE. 'categorie ON (art_categorie=cat_id)
                        ORDER BY art_date_created DESC');
    $sth->execute();
   
    $articles = $sth->fetchAll(PDO::FETCH_ASSOC);


}
catch(PDOException $e)
{
    
    $view = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
