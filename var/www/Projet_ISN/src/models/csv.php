<?php
/**
 * Fonctions
 *
 * Défini les fonctions.
 *
 * PHP version 7
 */

require_once CLASSES_PATH . 'User.php';

/**
 * Renvoie les informations d'élèves à partir d'un fichier CSV.
 *
 * Les informations renvoyées sont les suivantes :
 *  - Nom
 *  - Prénom
 *  - Classe
 *
 * @param string $file      Fichier CSV
 * @param string $delimiter (optional) Séparateur de champs (default: ',')
 *
 * @return array|null Informations d'élèves ou null si erreur
 */
function csv_to_user(string $file, string $delimiter = ','): ?array
{
    if ($csv_file = fopen($file, 'r')) { // Ouvre le fichier en lecture seule.
        // Récupère les champs CSV.
        while ($data = fgetcsv($csv_file, 100, $delimiter)) {
	    switch (count($data)) {
	        case 3:
		    $users[] = new User(null, null, STUDENT, $data[0], $data[1], $data[2]);
                    break;
		case 4:
		    $users[] = new User(null, null, STUDENT, $data[0], $data[1], $data[2],null,$data[3]);
		    break;
		default:
                    return null;
            }
        }

        fclose($csv_file);

        return $users;
    }
    return null;
}

/**
 * Enregistre les informations d'élèves dans d'un fichier CSV.
 *
 * Les informations renvoyées sont les suivantes :
 *  - Nom
 *  - Prénom
 *  - Classe
 *  - Mot de passe
 *
 * @param string $file      Fichier CSV
 * @param array  $data      Informations d'élèves
 * @param string $delimiter (optional) Séparateur de champs (default: ',')
 *
 * @return bool true si succès, false si erreur
 */
function user_to_csv(string $file, array $users, string $delimiter = ','
): bool
{
    if ($csv_file = fopen($file, 'w')) { // Ouvre le fichier en écriture seule.
        // Enregistre les champs CSV.
        foreach ($users as $user) {
            if (!fputcsv($csv_file, [
                $user->last_name,
                $user->first_name,
                $user->username,
                $user->password
                ], $delimiter)
            ) {
                return false;
            }
        }

        fclose($csv_file);

        return true;
    }
    return false;
}
