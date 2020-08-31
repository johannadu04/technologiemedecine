<?php
session_start();
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

$titlePage = 'Page catégories';
$subTitlePage = 'Les article de la catégorie : ';
$picturePage = 'img/home-bg.jpg';
$view = 'category';

try {
    /** Si on a pas d'id fourni on lance une exception DomainException 
     * Du coup la suite du code dans le try ne sera pas executé !
     */
    if (!array_key_exists('id', $_GET))
        throw new DomainException('Accès à la page non autorisé !');

    /** On récupère la catégorie dans la base pour voir si elle existe */
    $dbh = connect();
    $sth = $dbh->prepare('SELECT cat_id, cat_title
                        FROM b_categorie 
                        WHERE cat_id = :idCategory');
    $sth->bindValue('idCategory', $_GET['id']);
    $sth->execute();
    $category = $sth->fetch();



    /** Si la catégorie n'est pas trouvé on lance une exception DomainException
     * Le reste du code dans le try ne sera pas executé !
     */
    if ($category == false)
        throw new DomainException('Cet catégorie n\'existe pas !');

    /** On modifie le sous titre de la page pour y mettre le nom de la catégorie */
    $subTitlePage .= $category['cat_title'];

    /** Si tout est bon on récupère tous les articles de cette catégorie */
    $sth = $dbh->prepare('SELECT *,use_firstname,use_lastname,cat_title
                        FROM b_article
                        INNER JOIN b_user ON (use_id = art_author)
                        INNER JOIN b_categorie ON (cat_id = art_categorie)
                        WHERE art_categorie = :idCategory AND art_valide = 1 AND art_date_published <= NOW()
                        ORDER BY art_date_published DESC');
    $sth->bindValue('idCategory', $_GET['id']);
    $sth->execute();
    $articles = $sth->fetchAll();



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
} catch (DomainException $e) {
    /** Si une exception est levée car l'id n'est pas transmis ou l'article introuvable
     * On renvoi une page avec un code 404 dans l'entête / Page non trouvée
     * Cela sert au référencement et éventuellement si un utilisateur arrive ici alors qu'un article a été supprimer
     * Le mieux dans cette page c'est de lui permettre de naviguer ou de rechercher dans le contenu du blog
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
