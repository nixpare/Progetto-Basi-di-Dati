<?php
	function filter_passed($stud) {
		if (!isset($_GET['stud']) || $_GET['stud'] === '') {
			return true;
		}

		if (str_contains(strtolower($stud['cognome']), strtolower(urldecode($_GET['stud'])))) {
			return true;
		}

		if (str_contains(strtolower($stud['matricola']), strtolower(urldecode($_GET['stud'])))) {
			return true;
		}

		return false;
	}

	function get_studenti() {
		$query = 'select * from uni.studente order by matricola';
		$result = db_multi_select('get_studenti', $query, array())['result'];
		
		$studenti = array();
		foreach ($result as $stud) {
			if (!filter_passed($stud)) {
				continue;
			}
			$studenti[] = $stud;
		}
		
		return $studenti;
	}

	function get_corsi() {
		$query = 'select * from uni.corso_laurea';
		return db_multi_select('get_corsi', $query, array())['result'];
	}

	function create_studente($matricola, $cognome, $nome, $email, $password, $corso) {
		$query = 'insert into uni.studente
					(matricola, cognome, nome, email, password, corso)
					values ($1, $2, $3, $4, $5, $6)';
		$params = array($matricola, $cognome, $nome, $email, $password, $corso);
		return db_iu('create_studente', $query, $params);
	}
?>