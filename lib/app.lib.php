<?php

/** Function pour vérifier si l'utilisateur est connecté 
 * 
 */
function userIsConnected()
{
    if(session_status() == PHP_SESSION_DISABLED)
        session_start();

    if ($_SESSION['connected'] !== true) {
        header('Location:login.php');
        exit();
    }
}


/** function addFlashBag
 * Ajoute une valeur au flashbag
 * @param string $texte le message a afficher
 * @param string $level le niveau du message (correspond au type d'info bulle boostrap : success - warning - danger ...)
 * @return void
 */
function addFlashBag($texte, $level = 'success')
{
    if (!isset($_SESSION['flashbag']) || !is_array($_SESSION['flashbag']))
        $_SESSION['flashbag'] = array();

    $_SESSION['flashbag'][] = ['message' => $texte, 'level' => $level];
}

/** function getFlashBag
 * Ajoute une valeur au flashbag
 * @param void
 * @return array flashbag le tableau contenant tous les messages a afficher
 */
function getFlashBag()
{
    if (isset($_SESSION['flashbag']) && is_array($_SESSION['flashbag'])) {
        $flashbag = $_SESSION['flashbag'];
        unset($_SESSION['flashbag']);
        return $flashbag;
    }
    return false;
}


/** Function récursive (qui s'appelle elle même) permettant de trier le tableau des catégories
 * Cette fonction de créée pas de sous tableau mais donne un niveau de hérarchie et ordonne le tableau
 * @param array $categories le tableau (jeu d'enregistrement) des catégories
 * @param mixed $parent l'id du parent s'il existe ou null
 * @param mixed $level le niveau de hiérarchie
 */
function orderCategoriesLevel($categories, $parent = null, $level = 0)
{
    $tree = array();
    foreach ($categories as $index => $categorie) {
        if ($categorie['cat_parent'] == $parent) {
            $categorie['level'] = $level;
            $tree[] = $categorie;
            $childrens = orderCategoriesLevel($categories, $categorie['cat_id'], $level + 1);
            if (count($childrens) > 0)
                $tree = array_merge($tree, $childrens);
        }
    }
    return $tree;
}