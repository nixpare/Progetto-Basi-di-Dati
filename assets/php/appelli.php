<?php
	function filter_passed($ins) {
		if (empty($_GET)) {
			return true;
		}

		if (isset($_GET['insegnamento'])) {
			if (str_contains(
				strtolower($ins['nome']),
				strtolower(urldecode($_GET['insegnamento']))
			) or str_contains(
				strtolower($ins['insegnamento']),
				strtolower(urldecode($_GET['insegnamento']))
			)) {
				return true;
			}
		}
		if (isset($_GET['codice'])) {
			if ($ins['insegnamento'] === urldecode($_GET['codice'])) {
				return true;
			}
		}

		return false;
	}

	function get_iscrizioni() {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select sostiene.*, insegnamento.nome, appello.tipo from
						uni.sostiene join uni.insegnamento
							on sostiene.insegnamento = insegnamento.codice and sostiene.corso = insegnamento.corso
						join uni.appello
							on sostiene.data = appello.data and sostiene.insegnamento = appello.insegnamento and sostiene.corso = appello.corso
					where studente = $1 and voto is null
					order by data, insegnamento.nome';
		$query_name = 'get_iscrizioni';
		$params = array($_SESSION['matricola']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$iscrizioni = array();
		while ($row = pg_fetch_array($result)) {
			if (!filter_passed($row)) {
				continue;
			}
			$iscrizioni[] = $row;
		}
		return $iscrizioni;
	}

	function delete_iscrizione($data, $insegnamento) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'delete from uni.sostiene where studente = $1 and
					data = $2 and insegnamento = $3 and corso = $4';
		$query_name = 'delete_iscrizione';
		$params = array($_SESSION['matricola'], $data, $insegnamento, $_SESSION['corso']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			return false;
		}
		return true;
	}

	function get_disponibili() {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select a.*, insegnamento.nome, insegnamento.anno from
						uni.appello as a
						join
						uni.insegnamento on insegnamento.codice = a.insegnamento and insegnamento.corso = a.corso
					where insegnamento.corso = $1 and a.data > (select CURRENT_DATE) and not exists (
						select * from uni.sostiene
						where sostiene.studente = $2 and sostiene.data = a.data and sostiene.insegnamento = a.insegnamento
							and sostiene.corso = a.corso
					)
					order by data, insegnamento.nome';
		$query_name = 'get_disponibili';
		$params = array($_SESSION['corso'], $_SESSION['matricola']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$disponibili = array();
		while ($row = pg_fetch_array($result)) {
			if (!filter_passed($row)) {
				continue;
			}
			$disponibili[] = $row;
		}
		return $disponibili;
	}

	function add_iscrizione($data, $insegnamento) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'insert into uni.sostiene values
					($1, $2, $3, $4, NULL)';
		$query_name = 'delete_iscrizione';
		$params = array($_SESSION['matricola'], $data, $insegnamento, $_SESSION['corso']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			return false;
		}
		return true;
	}
?>