<?php

require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

$titlePage = '';
$subTitlePage = '';
$picturePage = '';
$view = 'index';


try {

    if(!array_key_exists('controller',$_GET))
        throw new DomainException('Accès à la page non autorisé !');

    $dbh = connect();
    
    $controller = 'controllers/' . $_GET['controller'] . '.php'; //post ///category
    if(file_exists($controller))
        include($controller);
    else
        include('controllers/index.php');

} catch (PDOException $e) {
    /** ERREUR base de données 
     * On affiche simplement une page d'erreur simple pour l'internaute
     */
    $view = 'erreurBdd';
    /** On peut ici envoyer un email à l'admin du site pour qu'il ai connaissance de l'erreur avec la base de données ;) */
} catch (DomainException $e) {
    /** Si une exception est levée car l'id n'est pas transmis ou l'article introuvable
     * On renvoi une page avec un code 404 dans l'entête / Page non trouvée
     * Cela sert au référencement et éventuellement si un utilisateur arrive ici alors qu'un article a été supprimer
     * Le mieux dans cette page c'est de lui permettre de naviguer ou de rechercher dans le contenu du blog
     * 
     */
    header("HTTP/1.0 404 Not Found");
    $titlePage = '404 Pas trouvé !';
    $subTitlePage = 'Tu es perdu ?';
    $picturePage = 'img/404.jpg';
    $metaPage = false;
    $view = '404';
    $displayMessage = $e->getMessage();
}


require('tpl/layout.phtml');