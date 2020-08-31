<?php
session_start();
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

$titlePage = 'Se Connecter';
$subTitlePage = 'Bienvenue !';
$picturePage = 'img/home-bg.jpg';
$view = 'login';

try {
    if (array_key_exists('email', $_POST) && array_key_exists('passwd', $_POST)) {
        // Filtrer
        $email = filter_input(INPUT_POST, 'email');
        $passwd = filter_input(INPUT_POST, 'passwd');
        // Login
        $dbh = connect();
        $sth = $dbh->prepare('SELECT use_id, use_email, use_password, use_role
                        FROM b_user 
                        WHERE use_email = :email');
        $sth->bindValue('email', $email);
        $sth->execute();

        $result = $sth->fetch();

        // Si le mot de passe reçu en POST correpsond avec celui de la db :
        if (password_verify($passwd, $result['use_password'])) {
            $_SESSION['id'] = $result['use_id'];

            $view = 'connected';
        } else {
            $view = 'login_failed';
        }
    }
} catch (PDOException $e) {
    echo 'Erreur ' . $e->getMessage();
}

require('tpl/layout.phtml');
