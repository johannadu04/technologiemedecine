<?php
session_start();


/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
require('../lib/database.lib.php');

$_SESSION['connected'] = false;
unset($_SESSION['connected']);
unset($_SESSION['user']);

header('Location:login.php');
exit();
