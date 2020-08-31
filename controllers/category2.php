<?php

$titlePage = 'Page catégories';
$subTitlePage = 'Les articles de la catégorie : ';
$picturePage = 'img/home-bg.jpg';
$view = 'category';


/** Si on a pas d'id fourni on lance une exception DomainException 
 * Du coup la suite du code dans le try ne sera pas executé !
 */
if (!array_key_exists('id', $_GET))
    throw new DomainException('Accès à la page non autorisé !');

/** On récupère la catégorie dans la base pour voir si elle existe */
$category = getCategoryById($dbh, $_GET['id']);
/** Si la catégorie n'est pas trouvé on lance une exception DomainException
 * Le reste du code dans le try ne sera pas executé !
 */
if ($category == false)
    throw new DomainException('Cet catégorie n\'existe pas !');

/** On modifie le sous titre de la page pour y mettre le nom de la catégorie */
$subTitlePage .= $category['cat_title'];

/** Si tout est bon on récupère tous les articles de cette catégorie */
$articles = getArticlesByCategoryId($dbh, $_GET['id']);

foreach ($articles as $index => $article) {
    $articles[$index]['art_content'] = mb_strimwidth(str_replace(['&eacute;', '&egrave;', '&rsquo;'], ['é', 'è', "'"], strip_tags($article['art_content'])), 0, RESUME_LENGTH, '...');
    $articles[$index]['art_date_published'] = (new DateTime($article['art_date_published']))->format('d/m/Y');
}
