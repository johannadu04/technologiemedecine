<?php
session_start();

require('../config/config.php');
require('../lib/database.lib.php');
require('../lib/app.lib.php');

userIsConnected();

$view = 'addArticle';
$pageTitle = 'Ajouter un article';

$errors = [];

$titleArticle = '';
$contentArticle = '';

$tmpDate = new DateTime('now', new DateTimeZone('Europe/Paris'));
$dateArticle = $tmpDate->format('Y-m-d');
$timeArticle = $tmpDate->format('H:i');

$pictureNameArticle = null; /*Le nom de l'image sui sera stocké en base de donnée*/

$valideArticle = true;
$categoryArticle = null;


try {

    /** Chercher toutes les catégories dans la base de données pour les afficher dans le formulaire*/
    $dbh = connect();
    $sth = $dbh->prepare('SELECT cat_id, cat_title FROM b_categorie');
    $sth->execute();
    $categories = $sth->fetchAll();

    /* Si le formulaire est reçu par la page */
    if (array_key_exists('title', $_POST)) {
        $titleArticle = trim($_POST['title']);
        $contentArticle = trim($_POST['content']);
        $dateArticle = $_POST['date'];
        $timeArticle = $_POST['time'];
        $categoryArticle = $_POST['category'];
        $valideArticle = (isset($_POST['valide'])) ? true : false;

        if ($titleArticle == '')
            $errors[] =  'Le titre ne peut être vide !';

        if ($dateArticle == '')
            $errors[] =  'La date ne peut être vide !';

        if ($timeArticle == '')
            $errors[] =  'L\'heure ne peut être vide !';

        /* Si une image a été uploadée avec le contenu de l'article */
        if (array_key_exists('picture', $_FILES) && $_FILES['picture']['name'] != '') {

            /** On vérifie qu'il n'y ai pas d'erreur d'upload */
            switch ($_FILES['picture']['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errors[] = 'Pas de fichier ou erreur sur le fichier';
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = 'Fichier trop grand';
                    break;
                default:
                    $errors[] = 'Erreur inconnu lors du chargement de l\'image !';
            }

            /** Si on a pas d'erreur d'upload on va déplacer l'image dans notre dossier uploads */
            if (count($errors) == 0) {

                $info = new SplFileInfo($_FILES['picture']['name']);
                $extension = $info->getExtension();

                /** Je Bascule ça dans le config.php dans une CONSTANTE VALIDE_EXTENSION_PICTURE
                 * J'en aurait besoin ailleurs .. avatar utilisateur ;)
                 * $valideExtension = ['jpg','gif','png','webp'];
                 */

                if (in_array($extension, VALIDE_EXTENSION_PICTURE)) {
                    /** Pour renommer l'image plusieurs possibiltés 
                     * 1. Renommer avec le nom orginal précédé d'un texte unique
                     * 2. Renommer avec le hash du fichier (evite les doublons, mais pas bon pour le référencement - En commentaire)
                     */
                    $pictureNameArticle = uniqid() . basename($_FILES['picture']['name']);
                    //$pictureNameArticle = hash_file('md5', $_FILES['picture']['tmp_name']).'.'.$info->getExtension();

                    /** On déplace le fichier temporaire vers sa nouvelle destination */
                    move_uploaded_file($_FILES['picture']['tmp_name'], UPLOADS_DIR . '/articles/' . $pictureNameArticle);
                } else {
                    $errors[] = 'Ce type de fichier n\'est pas autorisé. Seules les images sont acceptées';
                }
            }
        }


        if (count($errors) == 0) {
            $sth = $dbh->prepare('INSERT INTO b_article (art_id, art_title, art_content, art_date_published, art_date_created, art_picture, art_categorie, art_author, art_valide) 
            VALUES (NULL, :title, :content, :datePublished, NOW(), :picture, :categorie, :author, :valide)');

            $sth->bindValue('title', $titleArticle);
            $sth->bindValue('content', $contentArticle);
            $sth->bindValue('datePublished', $dateArticle . ' ' . $timeArticle);
            $sth->bindValue('picture', $pictureNameArticle);
            $sth->bindValue('categorie', $categoryArticle);
            $sth->bindValue('author', $_SESSION['user']['id']);
            $sth->bindValue('valide', $valideArticle, PDO::PARAM_BOOL);

            $sth->execute();

            addFlashBag('Article bien ajouté', 'success');

            header('Location:listeArticle.php');
            exit();
        }
    }
} catch (PDOException $e) {
    $view = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :' . $e->getMessage();
}

require('tpl/layout.phtml');
