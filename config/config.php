<?php

/* const DB_ENGINE     = 'mysql';
const DB_HOST       = 'localhost';
const DB_DATABASE   = 'classicmodels';
const DB_CHARSET    = 'utf8'; */

const DB_DSN        = 'mysql:host=localhost;dbname=blogtechandmed;charset=utf8';
const DB_USER       = 'root';
const DB_PASS       = '';

const DB_PREFIXE    = 'b_';


/* Les extensions valide pour les images */
const VALIDE_EXTENSION_PICTURE = ['jpg', 'gif', 'png', 'webp'];



/** Répertoire de base de l'application sur le serveur */
define('BASE_DIR', realpath(dirname(__FILE__) . "/../"));

//const BASE_DIR = realpath(dirname(__FILE__) . "/../");n

const URL = 'http://localhost/blogtechandmed/';

//Répertoire chemin complet vers le blog (pour l'upload)
const UPLOADS_DIR = BASE_DIR . '/uploads/';

const TPL_ADMIN_DIR = BASE_DIR . '/admin/tpl/';

//URL complète vers le répertoire upload (pour l'affichage des images dans l'HTML)
const UPLOADS_URL = URL . 'uploads/';

// LES ROLES dans l'application
const ROLES = ['ROLE_ADMIN' => 'Administrateur', 'ROLE_AUTHOR' => 'Auteur'];


/** Le titre principale */
const SITE_TITLE = 'Technologie & Medecine';

/** Le nombre d'article par page */
const MAX_ARTICLES_BY_PAGE = 5;

/** La longueur du résumé des article sur le front */
const RESUME_LENGTH = 150;
