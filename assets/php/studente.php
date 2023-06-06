<?php
	function init_studente() {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select tel, indirizzo, corso from uni.studente
					where matricola = $1';
		$query_name = 'init_studente';
		$params = array($_SESSION['matricola']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$result = pg_fetch_assoc($result);

		$_SESSION['tel'] = $result['tel'];
		$_SESSION['indirizzo'] = $result['indirizzo'];
		$_SESSION['corso'] = $result['corso'];
	}

	function get_info_corso($corso) {
		$info = array();

		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select tipo from uni.corso_laurea
					where nome = $1';
		$query_name = 'get_corso';
		$params = array($corso);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$result = pg_fetch_assoc($result);

		$info['tipo_corso'] = $result['tipo'];

		$query = 'select codice, anno, nome, responsabile from uni.insegnamento
					where corso = $1
					order by anno, nome';
		$query_name = 'get_insegnamenti';
		$params = array($corso);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$info['insegnamenti'] = array();
		while ($row = pg_fetch_array($result)) {
			$ins = array('anno' => $row['anno'], 'nome' => $row['nome']);

			$query = 'select nome, cognome from uni.docente
					where email = $1';
			$query_name = 'get_resp_' . $row['codice'];
			$params = array($row['responsabile']);
			
			$inner_result = pg_prepare($db, $query_name, $query);
			$inner_result = pg_execute($db, $query_name, $params);

			$inner_result = pg_fetch_assoc($inner_result);
			$ins['responsabile'] = $inner_result['nome'] . ' ' . $inner_result['cognome'];
			$ins['link_appelli'] = '/studente/appelli.php?codice=' . $row['codice'];

			$info['insegnamenti'][$row['codice']] = $ins;
		}

		return $info;
	}

	function change_password($password) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'update uni.studente set password = $1 where matricola = $2';
		$query_name = 'change_password';
		$params = array($password, $_SESSION['matricola']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			return false;
		}
		return true;
	}

	function change_tel($tel) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'update uni.studente set tel = $1 where matricola = $2';
		$query_name = 'change_tel';
		$params = array($tel, $_SESSION['matricola']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			return false;
		}
		return true;
	}

	function change_indirizzo($indirizzo) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'update uni.studente set indirizzo = $1 where matricola = $2';
		$query_name = 'change_indirizzo';
		$params = array($indirizzo, $_SESSION['matricola']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			return false;
		}
		return true;
	}
?>