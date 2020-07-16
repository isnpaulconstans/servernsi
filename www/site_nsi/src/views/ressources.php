<div class="article_container">
    <div>
        <h2>
            Cours
        </h2>
        <p>
            Venez réviser vos cours avant le contrôle.
        </p>
        <a class="button" href="/ressources/courses">Cours</a>
    </div>
    <img src="/images/e-learning.png">
</div>
<div class="article_container">
    <div>
        <h2>
            Activités
        </h2>
        <p>
            Entraînez-vous à programmer en cours.
        </p>
        <a class="button" href="/ressources/activities">Activités</a>
    </div>
    <img src="/images/activity.jpg">
</div>
<div class="article_container">
    <div>
        <h2>
            Devoirs Maisons
        </h2>
        <?php if ($connected): ?>
        <p>
            Faites et rendez vos devoirs.
        </p>
        <a class="button" href="/ressources/homeworks">Devoirs Maisons</a>
        <?php else: ?>
        <p>
            Connectez-vous pour avoir accès aux devoirs maisons.
        </p>
        <?php endif ?>
    </div>
    <img src="/images/dm.png">
</div>
