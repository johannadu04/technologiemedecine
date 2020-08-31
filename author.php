<?php
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

$titlePage = 'Page auteur';
$subTitlePage = 'Les article de : ';
$picturePage = 'img/home-bg.jpg';
$view ='author';

try
{
    if (!array_key_exists('id', $_GET))
        throw new DomainException('Accès à la page non autorisé !');

    $dbh = connect();

    $sth = $dbh->prepare('SELECT use_id
                        FROM b_user 
                        WHERE use_id = :idAuthor');
    $sth->bindValue('idAuthor', $_GET['id']);
    $sth->execute();
    $articles = $sth->fetch();

    if($articles == false)
        throw new DomainException('Cet auteur n\'existe pas !');

    $sth = $dbh->prepare('SELECT b_article.*,use_firstname,use_lastname,cat_title
                        FROM b_article
                        INNER JOIN b_user ON (use_id = art_author)
                        INNER JOIN b_categorie ON (cat_id = art_categorie)
                        WHERE art_author = :idAuthor AND art_valide = 1 AND art_date_published <= NOW()
                        ORDER BY art_date_published DESC');
    $sth->bindValue('idAuthor', $_GET['id']);
    $sth->execute();
    $articles = $sth->fetchAll();

    $subTitlePage.= $articles[0]['use_firstname'].' '. $articles[0]['use_lastname'];

    foreach ($articles as $index=>$article) {
        $articles[$index]['art_content'] = mb_strimwidth(str_replace(['&eacute;','&egrave;', '&rsquo;'], ['é','è',"'"], strip_tags($article['art_content'])), 0, RESUME_LENGTH, '...');
        $articles[$index]['art_date_published'] = (new DateTime($article['art_date_published']))->format('d/m/Y');
    }  
}
catch(PDOException $e)
{
    echo 'Erreur '.$e->getMessage();
}
catch(DomainException $e)
{
    header("HTTP/1.0 404 Not Found");
    $titlePage = '404 Pas trouvé !';
    $subTitlePage = 'Tu es perdu ?';
    $picturePage = 'img/404.jpg';
    $view = '404';
    $displayMessage = $e->getMessage();
}


require('tpl/layout.phtml');