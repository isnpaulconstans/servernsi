<!DOCTYPE html>

<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <link href="/styles/<?= $theme ?>.css" rel="stylesheet" type="text/css">
    <?php if ($page_title === 'édition d\'utilisateur'
              || $page_title === 'administration'): ?>
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
          <li><a<?php if ($page_title === 'actualités'): ?> class="active"<?php endif ?> href="/news">Actualités</a></li>
          <li><a<?php if ($page_title === 'ressources'
                         || $page_title === 'cours'
                         || $page_title === 'activités'
                         || $page_title === 'devoirs maison'
                         || $page_title === 'édition d\'un devoir maison'
                         || $page_title === 'productions du devoir maison'
                         || $page_title === 'rendre un devoir maison'
                         || $page_title === 'édition d\'une ressource'
                         ): ?> class="active"<?php endif ?> href="/ressources">Ressources</a>
            <ul class="sous-menus">
              <li><a<?php if ($page_title === 'cours'): ?> class="active"<?php endif ?> href="/ressources/courses">Cours</a></li>
              <li><a href="/ressources/activities">Activités</a></li>
              <?php if ($connected): ?>
              <li><a href="/ressources/homeworks">Devoirs maison</a></li>
              <?php endif ?>
            </ul>
          </li>
          <li><a<?php if ($page_title === 'services'): ?> class="active"<?php endif ?> href="/services">Services</a>
            <ul class="sous-menus">
                <li><a href="/jirafeau">Jirafeau</a></li>
                <li><a href="/gitea">Gitea</a></li>
                <li><a href="/jupyter">Jupyter</a></li>
            </ul>
          </li>
          <li><a id="connexion"<?php if ($page_title === 'connexion'
                                        || $page_title === 'profil'
                                        || $page_title === 'administration'
                                        || $page_title === 'communications'
                                        || $page_title === 'contact'
                                        ): ?> class="active"<?php endif ?> href="<?= $connected ? '/profil' : '/login' ?>"><?= $connected ? $_SESSION['user']->username : 'Connexion' ?></a>
            <ul class='sous-menus'>
                <li><a<?php if ($page_title === 'contact'): ?> class="active"<?php endif ?> href='/contact'>Contact</a></li>
            </ul>
            <?php if ($connected): ?>
            <ul class="sous-menus">
                <?php if ($admin): ?>
                <li><a<?php if ($page_title === 'administration'): ?> class="active"<?php endif ?> href="/admin">Administration</a></li>
                <?php endif ?>
                <li><a<?php if ($page_title === 'communication'): ?> class="active"<?php endif ?> href="/communication">Communication</a></li>
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
