<?php
	function filter_insegnamento_passed($appello) {
		if ((!isset($_GET['insegnamento'])  || $_GET['insegnamento'] === '') && (!isset($_GET['codice']) || $_GET['codice'] === '')) {
			return true;
		}

		if (isset($_GET['insegnamento'])) {
			if (str_contains(
				strtolower($appello['nome_insegnamento']),
				strtolower(urldecode($_GET['insegnamento']))
			) or str_contains(
				strtolower($appello['insegnamento']),
				strtolower(urldecode($_GET['insegnamento']))
			)) {
				return true;
			}
		}
		if (isset($_GET['codice'])) {
			if (strtolower($appello['insegnamento']) === strtolower(urldecode($_GET['codice']))) {
				return true;
			}
		}

		return false;
	}

	function filter_corso_passed($appello) {
		if (!isset($_GET['corso']) || $_GET['corso'] === '') {
			return true;
		}

		return strtolower($appello['corso']) === strtolower(urldecode($_GET['corso']));
	}

	function filter_passed($appello) {
		return filter_insegnamento_passed($appello) && filter_corso_passed($appello);
	}

	function get_appelli() {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'select appello.*, insegnamento.nome as nome_insegnamento from
					uni.insegnamento
					join
					uni.appello on appello.insegnamento = insegnamento.codice and appello.corso = insegnamento.corso
				  where insegnamento.responsabile = $1
				  order by data, appello.insegnamento, corso';
		$query_name = 'get_appelli';
		$params = array($_SESSION['email']);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		$appelli = array();
		while ($row = pg_fetch_array($result)) {
			if (!filter_passed($row)) {
				continue;
			}
			$appelli[] = $row;
		}

		return $appelli;
	}

	function delete_appello($data, $insegnamento, $corso) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'delete from uni.appello where data = $1 and
					insegnamento = $2 and corso = $3';
		$query_name = 'delete_appello';
		$params = array($data, $insegnamento, $corso);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			return false;
		}
		return true;
	}

	function add_appello($data, $insegnamento, $corso, $tipo) {
		$db = pg_connect('host=localhost user=bdlab password=bdlab dbname=project');

		$query = 'insert into uni.appello values
					($1, $2, $3, $4)';
		$query_name = 'add_appello';
		$params = array($data, $insegnamento, $corso, $tipo);
		
		$result = pg_prepare($db, $query_name, $query);
		$result = pg_execute($db, $query_name, $params);

		if (!$result) {
			$query_error = explode('CONTEXT', pg_last_error($db), 2)[0];
			$query_error = str_replace('ERRORE:', '', $query_error);
			$query_error = trim($query_error);

			return array(false, $query_error);
		}
		return array(true);
	}
?>