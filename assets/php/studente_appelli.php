<?php
	function filter_passed($ins) {
		if ((!isset($_GET['insegnamento'])  || $_GET['insegnamento'] === '') && (!isset($_GET['codice']) || $_GET['codice'] === '')) {
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
			if (strtolower($ins['insegnamento']) === strtolower(urldecode($_GET['codice']))) {
				return true;
			}
		}

		return false;
	}

	function get_iscrizioni() {
		$query = 'select sostiene.*, insegnamento.nome, appello.tipo from
						uni.sostiene join uni.insegnamento
							on sostiene.insegnamento = insegnamento.codice and sostiene.corso = insegnamento.corso
						join uni.appello
							on sostiene.data = appello.data and sostiene.insegnamento = appello.insegnamento and sostiene.corso = appello.corso
					where studente = $1 and voto is null
					order by data, insegnamento.nome';
		$result = db_multi_select('get_iscrizioni', $query, array($_SESSION['matricola']))['result'];

		$iscrizioni = array();
		foreach ($result as $iscr) {
			if (!filter_passed($iscr)) {
				continue;
			}
			$iscrizioni[] = $iscr;
		}
		return $iscrizioni;
	}

	function delete_iscrizione($data, $insegnamento) {
		$query = 'delete from uni.sostiene where studente = $1 and
					data = $2 and insegnamento = $3 and corso = $4';
		$params = array($_SESSION['matricola'], $data, $insegnamento, $_SESSION['corso']);
		$result = db_iu('delete_iscrizione', $query, $params)['result'];

		if (!$result) {
			return false;
		}
		return true;
	}

	function get_disponibili() {
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
		$params = array($_SESSION['corso'], $_SESSION['matricola']);
		$result = db_multi_select('get_disponibili', $query, $params)['result'];

		$disponibili = array();
		foreach ($result as $ins) {
			if (!filter_passed($ins)) {
				continue;
			}
			$disponibili[] = $ins;
		}
		return $disponibili;
	}

	function add_iscrizione($data, $insegnamento) {
		$query = 'insert into uni.sostiene values
					($1, $2, $3, $4, NULL)';
		$params = array($_SESSION['matricola'], $data, $insegnamento, $_SESSION['corso']);
		$result = db_iu('add_iscrizione', $query, $params)['result'];

		if (!$result) {
			return false;
		}
		return true;
	}
?>