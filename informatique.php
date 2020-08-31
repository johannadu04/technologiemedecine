<?php
session_start();
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

$titlePage = 'Informatique';
$subTitlePage = 'Cette catégorie concerne...';
$picturePage = 'img/home-bg.jpg';
$view = 'cat/informatique';

// Controlleur de la page informatique du menu catégories.

require('tpl/layout.phtml');
