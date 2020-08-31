<?php
session_start();
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

$titlePage = 'A propos';
$subTitlePage = 'Tout savoir sur nous !';
$picturePage = 'img/home-bg.jpg';
$view = 'about';

require('tpl/layout.phtml');
