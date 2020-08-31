<?php
session_start();

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
require('../lib/database.lib.php');

$email = '';

/** On test si on réceptionne les données de login */
try {
    //$flashbag = getFlashBag();

    if (array_key_exists('email', $_POST)) {
        $errorForm = []; //Pas d'erreur pour le moment sur les données

        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password == '')
            $errorForm[] = 'Merci de vérifier vos identifiants !';


        if (count($errorForm) == 0) {
            $bdd = connect();
            // use_role = 1 correspond au role admin.
            $sth = $bdd->prepare('SELECT use_id,use_lastname,use_firstname,use_email,use_role,use_valid,use_password
                                FROM b_user 
                                WHERE use_email = :email AND use_valid=1');
            $sth->bindValue('email', $email, PDO::PARAM_STR);
            $sth->execute();
            $user =  $sth->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['use_password'])) {
                //Connexion de l'utilisateur
                $_SESSION['connected'] = true;
                $_SESSION['user'] = ['id' => $user['use_id'], 'name' => $user['use_firstname'] . ' ' . $user['use_lastname'], 'role' => $user['use_role']];

                header('Location:index.php');
                exit();
            } else {
                $errorForm[] = 'Merci de vérifier vos identifiants !';
            }
        }
    }
} catch (PDOException $e) {
    $view = 'erreur';
    $messageErreur = 'Une erreur de connexion a eu lieu :' . $e->getMessage();
}

/** On inclu directement la vue login qui est un layout complet ! 
 * Spécifique pour le login... pas de menu... 
 *
 */
include('tpl/login.phtml');
