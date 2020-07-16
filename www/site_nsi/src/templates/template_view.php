<!DOCTYPE html>

<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <link href="/styles/<?= $theme ?>.css" rel="stylesheet" type="text/css">
<?php if ($page_title === 'Édition d\'utilisateur' || $page_title === 'Administration'): ?>
        <link href="/styles/octicon.css" rel="stylesheet" type="text/css">
<?php endif ?>
        <title>NSI <?= $page_title ?></title>
        <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.png" />
    </head>

    <body>
        <header>
            <nav>
                <ul id="menu">
                    <li><a<?php if ($page_title === ''): ?> class="active"<?php endif ?> href="/">Accueil</a></li>
                    <li><a<?php if ($page_title === 'Actualités'): ?> class="active"<?php endif ?> href="/news">Actualités</a></li>
                    <li><a<?php if ($page_title === 'Ressources' || $page_title === 'Cours' || $page_title === 'Activités' || $page_title === 'Devoirs Maisons'): ?> class="active"<?php endif ?> href="/ressources">Ressources</a>
                        <ul class="sous-menus">
                            <li><a<?php if ($page_title === 'Cours'): ?> class="active"<?php endif ?> href="/ressources/courses">Cours</a></li>
                            <li><a href="/ressources/activities">Activités</a></li>
                            <?php if ($connected): ?>
                            <li><a href="/ressources/homeworks">Devoirs Maisons</a></li>
                            <?php endif ?>
                        </ul>
                    </li>
                    <li><a<?php if ($page_title === 'Services'): ?> class="active"<?php endif ?> href="/services">Services</a>
                        <ul class="sous-menus">
                            <li><a href="/jirafeau">Jirafeau</a></li>
                            <li><a href="/gitea">Gitea</a></li>
                            <li><a href="/jupyter">Jupyter</a></li>
                            <!--<li><a href="/redmine">Redmine</a></li>-->
                        </ul>
                    </li>
                    <li><a id="connexion"<?php if ($page_title === 'Connexion' || $page_title === 'Profil' || $page_title === 'Administration' || $page_title === 'Communications' ): ?> class="active"<?php endif ?> href="<?= $connected ? '/profil' : '/login' ?>"><?= $connected ? $_SESSION['user']->username : 'Connexion' ?></a>
                        <ul class='sous-menus'>
                            <li><a<?php if ($page_title === 'Contact'): ?> class="active"<?php endif ?> href='/contact'>Contact</a></li>
                        </ul>
                        <?php if ($connected): ?>
                        <ul class="sous-menus">
                            <?php if ($admin): ?>
                            <li><a<?php if ($page_title === 'Administration'): ?> class="active"<?php endif ?> href="/admin">Administration</a></li>
                            <?php endif ?>
                            <li><a<?php if ($page_title === 'Communication'): ?> class="active"<?php endif ?> href="/communication">Communication</a></li>
                            <li><a href="/logout">Déconnexion</a></li>
                        </ul>
                        <?php endif ?>
                    </li>
                </ul>
            </nav>
        </header>
        <?php if (!empty($page_title)): ?>
        <h1><?= $page_title ?></h1>
        <?php endif ?>
        <main>
            <?= $page_content ?>
        </main>
    </body>
</html>
