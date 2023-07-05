<?php
	function init_studente() {
		$query = 'select tel, indirizzo, corso from uni.studente
					where matricola = $1';
		$result = db_single_select('init_studente', $query, array($_SESSION['matricola']))['result'];

		$_SESSION['tel'] = $result['tel'];
		$_SESSION['indirizzo'] = $result['indirizzo'];
		$_SESSION['corso'] = $result['corso'];
	}

	function get_info_corso($corso) {
		$info = array();

		$db = db_connect();

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

		pg_close($db);
		return $info;
	}

	function change_field($matricola, $field, $value) {
		$query = 'update uni.studente set ' . $field .' = $1 where matricola = $2';
		$params = array($value, $matricola);
		
		return db_iu('change_' . $field, $query, $params);
	}

	function get_studente($matricola) {
		$query = 'select * from uni.studente where matricola = $1';
		$params = array($matricola);
		
		return db_single_select('get_studente', $query, $params);
	}
?>