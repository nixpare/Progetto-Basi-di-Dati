<?php
	function get_corsi() {
		$query = 'select * from uni.corso_laurea order by tipo desc';
		$corsi = db_multi_select('get_corsi', $query, array())['result'];

		if (!$corsi) {
			return false;
		}

		for ($i = 0; $i < count($corsi); $i++) {
			$corsi[$i]['info_link'] = '/corsi.php?corso=' . $corsi[$i]['nome'];
		}
		return $corsi;
	}

	function get_informazioni_corso($nome_corso) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select * from uni.corso_laurea where lower(nome) = trim(lower($1))';
		$query_name = 'get_corso';
		$params = array($nome_corso);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$result = pg_fetch_assoc($result);

		if (!$result) {
			pg_close($db);
			return false;
		}

		$corso = array();
		$corso['nome_corso'] = $result['nome'];
		$corso['tipo'] = $result['tipo'];
		$corso['insegnamenti'] = array();

		$query = 'select * from uni.insegnamento where corso = $1 order by anno, nome';
		$query_name = 'get_insegnamenti';
		$params = array($corso['nome_corso']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		while ($row = pg_fetch_array($result)) {
			$row['nome_responsabile'] = get_nome_docente($row['responsabile']);
			$corso['insegnamenti'][] = $row;
		}
		
		pg_close($db);
		return $corso;
	}

	function get_nome_docente($email) {
		$query = 'select * from uni.docente where email = $1';
		$query_name = 'get_nome_docente_' . $email;
		$result = db_single_select($query_name, $query, array($email))['result'];

		return $result['nome'] . ' ' . $result['cognome'];
	}
?>