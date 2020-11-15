<?php
/**
 * Template
 *
 * Crée les ressources nécessaire à l'affiche et appelle la vue principale.
 *
 * PHP version 7
 */

/*
header("Strict-Transport-Security: max-age=63072000");
header("Content-Security-Policy: " .
       "default-src https:; img-src 'self'; script-src 'self';" .
       "style-src 'self'; object-src 'none';" .
       "frame-ancestors 'none'; X-Frame-Options DENY;" .
       "base-uri 'self'; form-action 'self'");
header("X-Content-Type-Options: nosniff");
 */


if (!isset($page_title)) {
    $page_title = null;
}

if (!isset($tab)) {
    $tab = null;
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
