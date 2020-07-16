<?php if ($error): ?>
<div class="error"><?= $error ?></div>
<?php endif ?>
<?php if ($success): ?>
<div class="success"><?= $success ?></div>
<?php endif ?>

<?php if (!empty($communications)): ?>
<?php foreach ($communications as $communication): ?>
<div class="comms_comm">
    <div class="comms_head">
        <?php if ($communication->sender === $user->id): ?>
        <div class="comms_contact">Pour : <?= $communication->receiver ?></div>
        <?php else: ?>
        <div class="comms_contact">De : <?= $communication->sender ?></div>
        <?php endif ?>
        <div class="comms_timestamp"><?= $week[date('N', $communication->timestamp)] . ' ' . date('j', $communication->timestamp) . ' ' . $month[date('n', $communication->timestamp)] . ' ' . date('Y', $communication->timestamp) . ' à ' . date('H:i:s', $communication->timestamp)?></div>
    </div>
    <p class="comms_message"><?= $communication->message ?></p>
</div>
<?php endforeach ?>
<?php endif ?>

<div class="comms_side">
    <ul>
        <li><a href="/communication">Tout</a></li>
        <?php if ($student_list): ?>
        <li>Élèves (<?= $student_count ?>)
            <ul>
                <?php foreach ($student_list as $student): ?>
                <li><a href="/communication?id=<?= $student->id ?>"><?= $student->last_name ?> <?= $student->first_name ?> (<?= $student->class ?>)</a></li>
                <?php endforeach ?>
            </ul>
        </li>
        <?php endif ?>
        <?php if ($professor_list): ?>
        <li>Professeurs (<?= $professor_count ?>)
            <ul>
                <?php foreach ($professor_list as $professor): ?>
                <li><a href="/communication?id=<?= $professor->id ?>"><?= $professor->last_name ?> <?= $professor->first_name ?></a></li>
                <?php endforeach ?>
            <ul>
        </li>
        <?php endif ?>
    </ul>
</div>

<?php if ($contact): ?>
<div class="comms_input">
    <div class="comms_for">Pour <?= $contact->last_name . ' ' . $contact->first_name ?></div>
    <form method="post">
        <textarea class= "comms_text" name="message" rows="5" cols="33" placeholder="Votre message..." maxlength="<?= CONFIG['message']['max_length'] ?>" required></textarea>
        <button class="comms_button">Envoyer</button>
    </form>
</div>
<?php else: ?>
<h2>Sélectionnez un contact pour communiquer.</h2>
<?php endif ?>