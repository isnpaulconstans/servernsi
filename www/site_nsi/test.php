<?php
require_once '/var/www/Projet_ISN/src/models/Database.php';
require_once '/var/www/Projet_ISN/src/classes/Homework.php';
class HomeworkDatabase extends Database
{
	public function test() {
		$query = $this->pdo->prepare('SELECT * FROM homework WHERE id = ?');
		print_r($query->execute([1]));
		print_r($query->fetchAll

?>

