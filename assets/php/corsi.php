<?php
	function get_corsi() {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select * from uni.corso_laurea order by tipo desc';
		$query_name = 'get_insegnamenti';
		$params = array();
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			return false;
		}

		$corsi = array();
		while ($row = pg_fetch_array($result)) {
			$row['info_link'] = '/corsi.php?corso=' . $row['nome'];
			$corsi[] = $row;
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
		return $corso;
	}

	function get_nome_docente($email) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select * from uni.docente where email = $1';
		$query_name = 'get_nome_docente';
		$params = array($email);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$result = pg_fetch_assoc($result);

		return $result['nome'] . ' ' . $result['cognome'];
	}
?>