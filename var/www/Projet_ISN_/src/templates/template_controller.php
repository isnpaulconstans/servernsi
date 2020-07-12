<?php
/**
 * Template
 *
 * Crée les ressources nécessaire à l'affiche et appelle la vue principale.
 *
 * PHP version 7
 */

if (!isset($page_title)) {
    $page_title = null;
}

$theme = 'light';
if ($connected) {
    switch ($_SESSION['user']->theme) {
        case 1:
            $theme = 'dark';
            break;
        case 2:
            $theme = 'hacker';
            break;
        default:
            $theme = 'light';
    }
}