<?php
	function filter_passed($doc) {
		if (!isset($_GET['doc']) || $_GET['doc'] === '') {
			return true;
		}

		if (str_contains(strtolower($doc['cognome']), strtolower(urldecode($_GET['doc'])))) {
			return true;
		}

		return false;
	}

	function get_docenti() {
		$query = 'select * from uni.docente order by cognome, nome';
		$result = db_multi_select('get_docenti', $query, array())['result'];
		
		$docenti = array();
		foreach ($result as $doc) {
			if (!filter_passed($doc)) {
				continue;
			}
			$docenti[] = $doc;
		}
		
		return $docenti;
	}

	function create_docente($cognome, $nome, $email, $password) {
		$query = 'insert into uni.docente values ($1, $2, $3, $4)';
		$params = array($email, $password, $nome, $cognome);

		return db_iu('create_docente', $query, $params);
	}
?>