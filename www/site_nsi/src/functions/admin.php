<?php
require_once CLASSES_PATH . 'User.php';
require_once MODELS_PATH . 'UserDatabase.php';
require_once MODELS_PATH . 'csv.php';


/**
 * Créer un nom d'utilisateur à partir d'un nom et d'un prénom.
 *
 * @param string $last_name  Nom
 * @param string $first_name Prénom
 *
 * @return string Nom d'utilisateur
 */
function create_username(string $last_name, string $first_name): string
{
    return strtolower($last_name) . '.' . strtolower($first_name)[0];
}


/**
 * Vérifie les données et ajoute un utilisateur à la base de données.
 *
 * @param UserDatabase $user_db    Base de données des utilisateurs
 * @param string       $username   Nom d'utilisateur
 * @param string       $last_name  Nom de l'utilisateur
 * @param string       $first_name Prénom de l'utilisateur
 * @param string       $role       Role de l'utilisateur
 * @param string|null  $class      Classe de l'utilisateur
 *
 * @return array Tableau indiquant le succès ou non et le message associé
 */
function user_add(UserDatabase $user_db, string $username, string $last_name,
    string $first_name, string $role, $class)
{
    $username   = htmlentities(trim($username));
    $last_name  = htmlentities(trim($last_name));
    $first_name = htmlentities(trim($first_name));
    $role       = (int)$role;
    
    if ($role != STUDENT && $role != PROFESSOR && $role != ADMIN) {
        return [
            'success' => false,
            'message' => 'Rôle invalide.'
        ];
    }

    if (empty($username) || empty($last_name) || empty($first_name)) {
        return [
            'success' => false,
            'message' => 'Tous les champs doivent être remplis'
        ];
    }

    // L'utilisateur existe.
    if ($user_db->exist($username)) {
        return [
            'success' => false,
            'message' => 'L\'utilisateur <strong>' . $username . '</strong> ' .
                'existe déjà.'
        ];
    }

    // Génération d'un nouveau mot de passe.
    $new_password = rand_string(CONFIG['password']['generated_length']);
    if (!$password = password_hash($new_password, PASSWORD_DEFAULT)) {
        return [
            'success' => false,
            'message' => 'Une erreur est survenue lors de la génération du ' .
                'nouveau mot de passe'
        ];
    }

    $user = new User(
        null,        // ID
        $username,   // Nom d'utilisateur
        $role,       // Rôle
        $last_name,  // Nom
        $first_name, // Prénom
        $class,      // Classe
        null,        // Thème
        null,        // Mot de passe
        $password    // Mot de passe hashé
    );

    // Ajout de l'utilisateur à la base de données.
    if (!$user_db->add($user)) {
        return [
            'success' => false,
            'message' => 'Une erreur est survenue lors de l\'ajout de ' .
                'l\'utilisateur'
        ];
    }

    return [
        'success' => true,
        'message' => '<strong>' . $user->last_name . ' ' . $user->first_name .
            '</strong> (' . $user->username . ') a été ajouté. Le mot de ' .
	    'passe associé est : <strong>' . $new_password . '</strong>',
	'new_password' => $new_password
    ];
}


function csv_add(UserDatabase $user_db, $file)
{
    // Le fichier n'est pas trop lourd.
    if ($_FILES['csv_file']['size'] > 1000000) {
        return [
            'success' => false,
            'message' => 'Le fichier envoyé est trop volumineux.'
        ];
    }

    $file_info = pathinfo($file['name']);
    // Le fichier est au format csv.
    if ($file_info['extension'] != 'csv') {
        return [
            'success' => false,
            'message' => 'Le fichier envoyé n\'est pas au format CSV.'
        ];
    }

    // Création de la liste des élèves à ajouter.
    if (!$new_users = csv_to_user($file['tmp_name'], CONFIG['csv']['delimiter'])
    ) {
        return [
            'success' => false,
            'message' => 'Une erreur est survenue lors de la lecture du ' .
                'fichier CSV. Vérifiez la syntaxe.'
        ];
    }

    $user_db->pdo->beginTransaction();
    foreach ($new_users as &$user) {
	// Génération d'un mot de passe.
	if ($user->password === null) {
	    $user->password = rand_string(
                CONFIG['password']['generated_length']
            );
	}
        // Génération du nom d'utilisateur.
        $user->username = create_username($user->last_name,
            $user->first_name
        );
        if ($user_db->exist($user->username)) { // L'utilisateur existe déjà.
            $i = 0;
            do {
                $username_tmp = $user->username . ++$i; // Ajoute un nombre.
            } while ($user_db->exist($username_tmp));
            $user->username = $username_tmp;
        }

        // Création d'un hash du mot de passe.
        if (!$user->password_hash = password_hash($user->password, PASSWORD_DEFAULT)
        ) {
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création ' .
                    'mots de passe.'
            ];
        }

        // Ajout de l'utilisateur à la base de données.
        if (!$user_db->add($user)
        ) {
            // Annule toutes les modifications en cas d'erreur.
            $user_db->rollBack();
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'ajout de ' .
                    "l'utilisateur ({$student->username} : " .
                    "{$student->last_name} {$student->first_name} en " .
                    "{$student->class})."
            ];
        }
    }
    $user_db->pdo->commit();

    // Génération du fichier CSV avec les mots de passe associé au élèves.
    if (!user_to_csv(
            DATA_PATH . 'admin' . DIRECTORY_SEPARATOR . 'students.csv',
            $new_users,
            CONFIG['csv']['delimiter']
        )
    ) {
        return [
            'success' => false,
            'message' => 'Une erreur est survenue lors de la génération du ' .
                'fichier CSV.'
        ];
    }

    $new_users_count = count($new_users);
    return [
        'success' => true,
        'message' =>  '<strong>' . $new_users_count . '</strong> élèves ont ' .
            'été ajoutés. <a href="/file?t=admin&f=students.csv">' .
            'Télécharger le fichier CSV</a>'
    ];
}
