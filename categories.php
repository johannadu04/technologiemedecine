<?php
session_start();
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

$titlePage = 'Nos catégories';
$subTitlePage = 'Les catégories du blog';
$picturePage = 'img/home-bg.jpg';
$view = 'categories';

try {

  /** PREPARATION DES CATEGORIES
   * On sélectionne toutes les catégories
   */
  $bdd = connect();
  $sth = $bdd->prepare('SELECT c1.*, c2.cat_title as parent, COUNT(a.art_id) as articles, art_valide, art_date_published  
                        FROM ' . DB_PREFIXE . 'categorie c1 
                        LEFT JOIN ' . DB_PREFIXE . 'categorie c2 ON c1.cat_parent=c2.cat_id 
                        LEFT JOIN ' . DB_PREFIXE . 'article a ON c1.cat_id = a.art_categorie 
                        GROUP BY c1.cat_id,c2.cat_id 
                        ORDER BY c1.cat_title, c1.cat_parent');
  /* tri sur le fait qu'un article soit en ligne à valider
      WHERE art_valide = 1 AND art_date_published <= NOW()
      HAVING articles = 0 OR (art_valide = 1 AND art_date_published <= NOW())
    */
  $sth->execute();

  $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

  /**  On va créer un tableau des catégorie hiérarchisée pour afficher des ul>li hiérarchiques (arbre des catégories parent/enfants)
   * Utilisation d'une fonction récursive (pour l'exemple algorithmique !).
   */
  $orderedCategories = orderCategoriesLevel($categories);
} catch (PDOException $e) {
  echo 'Erreur ' . $e->getMessage();
}



require('tpl/layout.phtml');
