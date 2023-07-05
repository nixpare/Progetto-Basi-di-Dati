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
		$query = 'select appello.*, insegnamento.nome as nome_insegnamento from
					uni.insegnamento
					join
					uni.appello on appello.insegnamento = insegnamento.codice and appello.corso = insegnamento.corso
				  where insegnamento.responsabile = $1
				  order by data, appello.insegnamento, corso';
		$params = array($_SESSION['email']);
		
		$result = db_multi_select('get_appelli', $query, $params)['result'];

		$appelli = array();
		foreach ($result as $row) {
			if (!filter_passed($row)) {
				continue;
			}
			$appelli[] = $row;
		}

		return $appelli;
	}

	function delete_appello($data, $insegnamento, $corso) {
		$query = 'delete from uni.appello where data = $1 and
					insegnamento = $2 and corso = $3';
		$params = array($data, $insegnamento, $corso);
		$result = db_iu('delete_appello', $query, $params)['result'];

		if (!$result) {
			return false;
		}
		return true;
	}

	function add_appello($data, $insegnamento, $corso, $tipo) {
		$query = 'insert into uni.appello values
					($1, $2, $3, $4)';
		$params = array($data, $insegnamento, $corso, $tipo);
		$result = db_iu('add_appello', $query, $params);

		if (!$result['result']) {
			return array(false, $result['error']);
		}
		return array(true);
	}
?>