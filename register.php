<?php
require('config/config.php');
require('lib/database.lib.php');
require('lib/app.lib.php');

$titlePage = 'Enregistrement';
$subTitlePage = 'Vous pouvez créer votre superbe compte gratuitement !';
$picturePage = 'img/home-bg.jpg';
$view = 'register';

try {
    if (array_key_exists('email', $_POST) && array_key_exists('passwd', $_POST)) {
        // Filtrer
        $email = filter_input(INPUT_POST, 'email');
        $passwd = filter_input(INPUT_POST, 'passwd');
        $passwd_repeat = filter_input(INPUT_POST, 'passwd_repeat');

        // Si on passe la validation email et que les mots de passe sont identiques :
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && $passwd === $passwd_repeat) {
            // Enregistrement
            $dbh = connect();

            $sth = $dbh->prepare('INSERT INTO b_user (use_lastname, use_firstname, use_email, use_password) 
            VALUES (:lastname, :firstname, :email, :passwd)');

            $sth->bindValue('lastname', "Nom");
            $sth->bindValue('firstname', "Prenom");
            $sth->bindValue('email', $email);
            $sth->bindValue('passwd', password_hash($passwd, PASSWORD_DEFAULT));

            $sth->execute();

            $view = 'register_success';
        }
    }
} catch (PDOException $e) {
    echo 'Erreur ' . $e->getMessage();
}

require('tpl/layout.phtml');
