<?php
	function check_doc($insegnamento, $corso) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select * from uni.insegnamento
				  where codice = $1 and corso = $2 and responsabile = $3';
		$query_name = 'check_doc';
		$params = array($insegnamento, $corso, $_SESSION['email']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$num_rows = pg_num_rows($result);

		if ($num_rows == 0) {
			return false;
		}
		return true;
	}

	function get_iscritti($data, $insegnamento, $corso) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select studente.matricola, studente.nome, studente.cognome, sostiene.voto from
					uni.studente
					join
					uni.sostiene on studente.matricola = sostiene.studente
				  where sostiene.data = $1 and sostiene.insegnamento = $2 and sostiene.corso = $3
				  order by voto, matricola, studente.cognome, studente.nome';
		$query_name = 'get_iscritti';
		$params = array($data, $insegnamento, $corso);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$iscritti = array();
		while ($row = pg_fetch_array($result)) {
			$iscritti[] = $row;
		}

		return $iscritti;
	}

	function set_voto($data, $insegnamento, $corso, $matricola, $voto) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'update uni.sostiene set voto = $5
					where studente = $1 and data = $2 and insegnamento = $3 and corso = $4';
		$query_name = 'set_voto';
		$params = array($matricola, $data, $insegnamento, $corso, $voto);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$num_rows = pg_affected_rows($result);

		if ($num_rows == 0) {
			return false;
		}
		return true;
	}
?>