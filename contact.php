<?php
session_start();
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

$titlePage = 'Contactez-nous';
$subTitlePage = 'Laissez nous un petit message gentil !';
$picturePage = 'img/home-bg.jpg';
$view = 'contact';

try {
    if (array_key_exists('email', $_POST)) {
        /** On va récupérer les données du formulaire et envoyer un email à l'admin du blog ;) */
    }
} catch (PDOException $e) {
    echo 'Erreur ' . $e->getMessage();
}

require('tpl/layout.phtml');
