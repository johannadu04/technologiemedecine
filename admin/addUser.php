<?php
session_start();

require('../config/config.php');

require('../lib/app.lib.php');
require('../lib/database.lib.php');

userIsConnected();

$view = 'addUser';
$pageTitle = 'Ajouter un utilisateur';

$errors = [];

$emailUser = '';
$firstnameUser = '';
$lastnameUser = '';
//$bioUser = '';
$valideUser = true;
$roleUser = 'ROLE_AUTHOR';
 
try
{
    // Je reçois le form
    if(array_key_exists('email',$_POST))
    {
        $emailUser = trim($_POST['email']);
        $firstnameUser = trim($_POST['firstname']);
        $lastnameUser = trim($_POST['lastname']);
        $passwordUser = $_POST['password']; //pas de trim ici !
        //$bioUser = trim($_POST['bio']);
        $valideUser = ($_POST['valide'])?true:false;
        $roleUser = $_POST['role'];

        $dbh = connect();

        if ($passwordUser != $passwordConfUser || $passwordUser == '')
            $errorForm[] = 'Le mot de passe ou sa confimation ne sont pas corrects !';

        if (strlen($passwordUser) < 8)
            $errorForm[] = 'Le mot de passe doit comporter 8 caractères minimum !';


        if(trim($emailUser) == '' || filter_var($emailUser, FILTER_VALIDATE_EMAIL) === false){
            $errors[] =  'Erreur email';
        } else {
            $sth = $dbh->prepare('SELECT use_email FROM b_user WHERE use_email = :email');
            $sth->bindValue('email',$emailUser);
            $sth->execute();
            $user = $sth->fetch();

            if ($user !== false)
                $errors[] =  'Cet email est déjà utilisé dans la base';
        }

        if(trim($firstnameUser) == '')
            $errors[] =  'Erreur firstname ne peut être vide !';

        if (trim($lastnameUser) == '')
            $errors[] =  'Erreur lastname ne peut être vide !';

        if (count($errors)==0) {

            $passwordUser = password_hash($passwordUser,PASSWORD_BCRYPT);

           
            $sth = $dbh->prepare('INSERT INTO b_user (use_id, use_firstname, use_lastname, use_email, use_password, use_valide, use_role) VALUES (NULL, :firstname, :lastname, :email, :password, :valide, :role)');

            $sth->bindValue('firstname',$firstnameUser);
            $sth->bindValue('lastname', $lastnameUser);
            $sth->bindValue('email', $emailUser);
            $sth->bindValue('password', $passwordUser);
            $sth->bindValue('valide', $valideUser);
            $sth->bindValue('role', $roleUser);

            $sth->execute();

            header('Location:listeUser.php');
            exit();
        }
    }

}
catch (PDOException $e)
{
    echo 'Erreur PDO : '.$e->getMessage();

    //var_dump($e->getTrace());
}



require('tpl/layout.phtml');