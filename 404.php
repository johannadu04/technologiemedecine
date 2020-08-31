<?php
$titlePage = '404';
$subTitlePage = 'Erreur 404 - Page introuvable !';
$picturePage = 'img/404.jpg';
$view = '404';

header("HTTP/1.0 404 Not Found");

include('tpl/layout.phtml');