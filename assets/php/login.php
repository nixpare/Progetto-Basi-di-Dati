<?php
	function already_logged_in() {
		http_response_code(301);
		switch ($_SESSION['tipo_utente']) {
			case 'stud':
				header('Location: /studente.php');
			case 'doc':
				header('Location: /docente.php');
			case 'segr':
				header('Location: /segreteria.php');
		}
	}

	function login_user($userType, $email, $password) {
		$selection = '';
		$table = '';
		switch ($userType) {
			case 'stud':
				$selection = 'matricola, nome, cognome, corso';
				$table = 'studente';
				break;
			case 'doc':
				$selection = 'nome, cognome';
				$table = 'docente';
				break;
			case 'segr':
				$selection = 'nome, cognome';
				$table = 'segretario';
				break;
			default:
				echo 'Bella - ';
				return false;
		}

		$query = 'select ' . $selection . ' from uni.' . $table . ' 
					where email = $1 and password = $2';
		return db_single_select('login', $query, array($email, $password));
	}
?>