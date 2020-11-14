<?php
/**
 * Routeur
 *
 * Redirige la requête vers le controlleur associé selon les permitions
 * accordés au différents utilisateurs.
 *
 * PHP version 7
 */

// Définition des chemins vers les dossiers.
define('WEB_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

const STYLES_PATH = '/styles/';
const VIEWS_PATH = WEB_ROOT . 'src' . DIRECTORY_SEPARATOR . 'views' .
    DIRECTORY_SEPARATOR;
const TEMPLATES_PATH = WEB_ROOT . 'src' . DIRECTORY_SEPARATOR . 'templates' .
    DIRECTORY_SEPARATOR;
const CONTROLLERS_PATH = WEB_ROOT . 'src' . DIRECTORY_SEPARATOR .
    'controllers' . DIRECTORY_SEPARATOR;
const MODELS_PATH = WEB_ROOT . 'src' . DIRECTORY_SEPARATOR . 'models' .
    DIRECTORY_SEPARATOR;
const FUNCTIONS_PATH = WEB_ROOT . 'src' . DIRECTORY_SEPARATOR . 'functions' .
    DIRECTORY_SEPARATOR;
const CLASSES_PATH = WEB_ROOT . 'src' . DIRECTORY_SEPARATOR . 'classes' .
    DIRECTORY_SEPARATOR;
const JS_PATH = WEB_ROOT . 'src' . DIRECTORY_SEPARATOR . 'js' .
    DIRECTORY_SEPARATOR;
const DATA_PATH = WEB_ROOT . 'data' . DIRECTORY_SEPARATOR;

// Lecture du fichier de configuration.
define('CONFIG', parse_ini_file(
        WEB_ROOT . 'config' . DIRECTORY_SEPARATOR . 'config.ini',
        true
    )
);

// Constantes décrivant les roles des utilisateurs.
const STUDENT   = 0;
const PROFESSOR = 1;
const ADMIN     = 2;

const ERROR404 = '<h1>Erreur 404 : Page introuvable</h1>';
const ERROR401 = '<h1>Veuillez vous connecter pour accéder à cette page.</h1>';

require_once FUNCTIONS_PATH . 'global.php';

// Séparation de l'URI (sans les paramètres GET).
$uri = explode('/', explode('?', $_SERVER['REQUEST_URI'])[0]);
$uri_size = count($uri);

$admin = false;
$prof  = false;
if ($connected = is_connected()) {
    $student = is_student();
    $prof = is_prof();
    $admin = is_admin();
}

ob_start();

switch ($uri[1]) {
//case 'test': phpinfo(); break;
    case '':
    case 'home':
        if ($uri_size === 2) {
            $page_title = '';
            require VIEWS_PATH . 'home.php';
            break;
        }
    case 'news':
        if ($uri_size === 2) {
            $page_title = 'actualités';
            require VIEWS_PATH . 'news.php';
            break;
        }
    case 'ressources':
        if ($uri_size === 2) {
            $page_title = 'ressources';
            require VIEWS_PATH . 'ressources.php';
            break;
        }
        switch ($uri[2]) {
            case 'courses':
                $page_title = 'cours';
                $ressource_type = 'course';
                require CONTROLLERS_PATH . 'ressources' .
                    DIRECTORY_SEPARATOR . 'ressources.php';
                require VIEWS_PATH . 'ressources' .
                    DIRECTORY_SEPARATOR . 'ressources.php';
                break;
            case 'activities':
                $page_title = 'activités';
                $ressource_type = 'activity';
                require CONTROLLERS_PATH . 'ressources' .
                    DIRECTORY_SEPARATOR . 'ressources.php';
                require VIEWS_PATH . 'ressources' .
                    DIRECTORY_SEPARATOR . 'ressources.php';
                break;
            case 'homeworks':
                if (!$connected) {
                    echo ERROR401;
                    break;
                }
                if ($uri_size === 3) {
                    $page_title = 'devoirs maison';
                    require CONTROLLERS_PATH . 'ressources' .
                        DIRECTORY_SEPARATOR . 'homeworks.php';
                    require VIEWS_PATH . 'ressources' .
                        DIRECTORY_SEPARATOR . 'homeworks.php';
                    break;
                }
                if ($uri_size != 4) {
                    echo ERROR404;
                    break;
                }
                switch ($uri[3]) {
                    case 'edit':
                        if ($student) {
                            echo ERROR401;
                            break;
                        }
                        $page_title = 'édition d\'un devoir maison';
                        require CONTROLLERS_PATH . 'ressources' .
                            DIRECTORY_SEPARATOR . 'homeworks' .
                            DIRECTORY_SEPARATOR . 'edit.php';
                        require VIEWS_PATH . 'ressources' .
                            DIRECTORY_SEPARATOR . 'homeworks' .
                            DIRECTORY_SEPARATOR . 'edit.php';
                        break;
                    case 'view':
                        if ($student) {
                            echo ERROR401;
                            break;
                        }
                        $page_title = 'productions du devoir maison';
                        require CONTROLLERS_PATH . 'ressources' .
                            DIRECTORY_SEPARATOR . 'homeworks' .
                            DIRECTORY_SEPARATOR . 'view.php';
                        require VIEWS_PATH . 'ressources' .
                            DIRECTORY_SEPARATOR . 'homeworks' .
                            DIRECTORY_SEPARATOR . 'view.php';
                        break;
                    case 'return':
                        if (!$student) {
                            echo ERROR401;
                            break;
                        }
                        $page_title = 'rendre un devoir maison';
                        require CONTROLLERS_PATH . 'ressources' .
                            DIRECTORY_SEPARATOR . 'homeworks' .
                            DIRECTORY_SEPARATOR . 'return.php';
                        require VIEWS_PATH . 'ressources' .
                            DIRECTORY_SEPARATOR . 'homeworks' .
                            DIRECTORY_SEPARATOR . 'return.php';
                        break;
                    default:
                        echo ERROR404;
                }
                break;
            case 'edit':
                if ($student) {
                    echo ERROR401;
                    break;
                }
                $page_title = 'édition d\'une ressource';
                require CONTROLLERS_PATH . 'ressources' .
                    DIRECTORY_SEPARATOR . 'edit.php';
                require VIEWS_PATH . 'ressources' .
                    DIRECTORY_SEPARATOR . 'edit.php';
                break;
            default:
                echo ERROR404;
        }
        break;
    case 'services':
        if ($uri_size === 2) {
            $page_title = 'services';
            require VIEWS_PATH . 'services.php';
            break;
        }
    case 'login':
        if ($connected) {
            echo 'Vous êtes déjà connecté';
            break;
        }
        $page_title = 'connexion';
        require CONTROLLERS_PATH . 'login.php';
        require VIEWS_PATH . 'login.php';
        break;
    case 'contact':
        if ($uri_size === 2) {
            $page_title = 'contact';
            require VIEWS_PATH . 'contact.php';
            break;
        }
    case 'file':
        if ($uri_size === 2) {
            require CONTROLLERS_PATH . 'file.php';
            break;
        }
    default:
        if (!$connected) {
            echo ERROR401;
            break;
        }
        switch ($uri[1]) {
            case 'logout':
                if ($uri_size === 2) {
                    require CONTROLLERS_PATH . 'logout.php';
                    break;
                }
            case 'profil':
                if ($uri_size === 2) {
                    $page_title = 'profil';
                    require CONTROLLERS_PATH . 'profil.php';
                    require VIEWS_PATH . 'profil.php';
                    break;
                }
            case 'communication':
                if ($uri_size === 2) {
                    $page_title = 'communications';
                    require CONTROLLERS_PATH . 'communication.php';
                    require VIEWS_PATH . 'communication.php';
                    break;
                }
            case 'edit_pass':
                if ($uri_size === 2) {
                    $page_title = 'modifier le mot de passe';
                    require CONTROLLERS_PATH . 'edit_pass.php';
                    require VIEWS_PATH . 'edit_pass.php';
                    break;
                }
            case 'admin':
                if (!$admin) {
                    echo ERROR401;
                    break;
                }
                if ($uri_size === 2) {
                    $page_title = 'administration';
                    require CONTROLLERS_PATH . 'admin.php';
                    require VIEWS_PATH . 'admin.php';
                    break;
                }
                if ($uri_size != 3) {
                    echo ERROR404;
                    break;
                }
                switch ($uri[2]) {
                    case 'edit':
                        $page_title = 'édition d\'utilisateur';
                        require CONTROLLERS_PATH . 'admin' .
                            DIRECTORY_SEPARATOR . 'edit.php';
                        require VIEWS_PATH . 'admin' .
                            DIRECTORY_SEPARATOR . 'edit.php';
                        break;
                    default:
                        echo ERROR404;
                }
                break;
            default:
                echo ERROR404;
        }
}

$page_content = ob_get_clean();

require TEMPLATES_PATH . 'template_controller.php';
require TEMPLATES_PATH . 'template_view.php';
