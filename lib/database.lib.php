<?php

/** Connexion à la base de données
 * @param void
 * 
 * @return object Un objet PDO (connexion vers le serveur de BDD)
 */
function connect()
{
    $dbh = new PDO(DB_DSN, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $dbh;
}


function getCategoryById($dbh, $id)
{
    
    $sth = $dbh->prepare('SELECT cat_id, cat_title
                        FROM b_categorie 
                        WHERE cat_id = :idCategory');
    $sth->bindValue('idCategory',$id);
    $sth->execute();
    $category = $sth->fetch();

    return $category;
}

function getArticlesByCategoryId($dbh, $id)
{
    $sth = $dbh->prepare('SELECT *,use_firstname,use_lastname,cat_title
                        FROM b_article
                        INNER JOIN b_user ON (use_id = art_author)
                        INNER JOIN b_categorie ON (cat_id = art_categorie)
                        WHERE art_categorie = :idCategory AND art_valide = 1 AND art_date_published <= NOW()
                        ORDER BY art_date_published DESC');
    $sth->bindValue('idCategory', $id);
    $sth->execute();
    $articles = $sth->fetchAll();
    return $articles;
}