<?php
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

/** Les variables qui servent au layout et à la vue ! */
$titlePage = '';
$subTitlePage = '';
$picturePage = '';
$metaPage = true;
$view = 'comment';


$nameComment = '';
$emailComment = '';
$contentComment = '';


try {
    /** Si on a pas d'id fourni on lance une exception DomainException 
     * Du coup la suite du code dans le try ne sera pas executé !
     */
    if (!array_key_exists('id', $_GET))
        throw new DomainException('Accès à la page non autorisé !');

    /** On récupère l'article dans la base */
    $dbh = connect();
    $sth = $dbh->prepare('SELECT b_article.*,use_firstname,use_lastname,cat_title
                        FROM b_article
                        INNER JOIN b_user ON (use_id = art_author)
                        INNER JOIN b_categorie ON (cat_id = art_categorie)
                        WHERE art_id = :idArticle AND art_valide = 1 AND art_date_published <= NOW()
                        ORDER BY art_date_published DESC');
    $sth->bindValue('idArticle', $_GET['id']);
    $sth->execute();
    $article = $sth->fetch();

    /** Si l'article n'est pas trouvé on lance une exception DomainException
     * Le reste du code dans le try ne sera pas executé !
     */
    if ($article === false)
        throw new DomainException('L\'article demandé n\'existe pas !');

    // On modifie les données du jeu d'enregistrement pour le résumé de l'article et la date
    $article['art_date_published'] = (new DateTime($article['art_date_published']))->format('d/m/Y');

    // On modifie les variables de templates en fonction de l'article
    $titlePage = $article['art_title'];
    $picturePage = UPLOADS_URL . 'articles/' . $article['art_picture'];
    $metaPage = 'Posté par <a href="author.php?id=' . $article['art_author'] . '">' . $article['use_firstname'] . ' ' . $article['use_lastname'] . '</a>
                    le ' . $article['art_date_published'] . '- Dans <a href="category.php?id=' . $article['art_categorie'] . '">' . $article['cat_title'] . '</a>';
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
