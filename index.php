<?php
session_start();
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

/** Les variables qui servent au layout et à la vue ! */
$titlePage = 'Technologie & Médecine';
$subTitlePage = 'Les soins à venir';
$picturePage = 'img/home-bg.jpg';
$view = 'index';

$startArticle = 0;

try {
    $dbh = connect();

    /** GESTION DE LA PAGINATION */
    // On compte combien on a d'articles dans la base pour la pagination
    $sth = $dbh->prepare('SELECT COUNT(art_id) as nb
                        FROM b_article
                        WHERE art_valide = 1 AND art_date_published <= NOW()');
    $sth->execute();
    $nbArticles = $sth->fetch();
    // On détermine le nombre max que peut prendre la valeur start dans l'url
    $tmpMaxNumberStart = intval($nbArticles['nb'] / MAX_ARTICLES_BY_PAGE) * MAX_ARTICLES_BY_PAGE;

    // On défini la nouvelle valeur de start si le paramètre dans l'url est existe et est cohérent (> 0 et <$tmpMaxNumberStart)
    if (array_key_exists('start', $_GET) && intval($_GET['start']) > 0 && intval($_GET['start']) <= $tmpMaxNumberStart)
        $startArticle = intval($_GET['start'], 10);


    /** PREPARATION DES ARTICLES
     * On sélectionne tous les articles avec les données auteur et catégories
     * On sélectionne les articles en fonction de MAX_ARTICLES_BY_PAGE et $startArticle
     */
    $sth = $dbh->prepare('SELECT b_article.*,use_firstname,use_lastname,cat_title
                        FROM b_article
                        INNER JOIN b_user ON (use_id = art_author)
                        INNER JOIN b_categorie ON (cat_id = art_categorie)
                        WHERE art_valide = 1 AND art_date_published <= NOW()
                        ORDER BY art_date_published DESC
                        LIMIT :start,:end');
    $sth->bindValue('start', $startArticle, PDO::PARAM_INT);
    $sth->bindValue('end', MAX_ARTICLES_BY_PAGE, PDO::PARAM_INT);
    $sth->execute();
    $articles = $sth->fetchAll();

    // On modifie les données du jeu d'enregistrement pour le résumé de l'article et la date
    foreach ($articles as $index => $article) {
        $articles[$index]['art_content'] = mb_strimwidth(str_replace(['&eacute;', '&egrave;', '&rsquo;'], ['é', 'è', "'"], strip_tags($article['art_content'])), 0, RESUME_LENGTH, '...');
        $articles[$index]['art_date_published'] = (new DateTime($article['art_date_published']))->format('d/m/Y');
    }
} catch (PDOException $e) {
    /** ERREUR base de données 
     * On affiche simplement une page d'erreur simple pour l'internaute
     */
    $view = 'erreurBdd';
    /** On peut ici envoyer un email à l'admin du site pour qu'il ai connaissance de l'erreur avec la base de données ;) */
}


require('tpl/layout.phtml');
