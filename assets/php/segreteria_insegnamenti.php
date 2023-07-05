<?php
	function filter_passed($ins) {
		if (!isset($_GET['filter']) || $_GET['filter'] === '') {
			return true;
		}

		if (str_contains(strtolower($ins['codice']), strtolower(urldecode($_GET['filter'])))) {
			return true;
		}

		if (str_contains(strtolower($ins['nome']), strtolower(urldecode($_GET['filter'])))) {
			return true;
		}

		if (str_contains(strtolower($ins['corso']), strtolower(urldecode($_GET['filter'])))) {
			return true;
		}

		return false;
	}

	function get_insegnamenti() {
		$query = 'select insegnamento.*, corso_laurea.tipo from
					uni.insegnamento
					join
					uni.corso_laurea on insegnamento.corso = corso_laurea.nome
				order by corso_laurea.tipo desc, insegnamento.anno, insegnamento.codice, insegnamento.nome';
		$result = db_multi_select('get_insegnamenti', $query, array())['result'];
		
		$insegnamenti = array();
		foreach ($result as $ins) {
			if (!filter_passed($ins)) {
				continue;
			}
			$insegnamenti[] = $ins;
		}
		
		return $insegnamenti;
	}

	function get_corsi() {
		$query = 'select * from uni.corso_laurea order by tipo desc, nome asc';
		return db_multi_select('get_corsi', $query, array())['result'];
	}

	function get_suitable_docenti() {
		$query = 'select docente.* from
					uni.docente
					left join
					uni.insegnamento on docente.email = insegnamento.responsabile
				group by docente.email
				having count(insegnamento.*) < 3
				order by docente.cognome, docente.nome';
		return db_multi_select('get_suitable_docenti', $query, array())['result'];
	}

	function create_insegnamento($codice, $corso, $anno, $nome, $descr, $resp) {
		$query = 'insert into uni.insegnamento values ($1, $2, $3, $4, $5, $6)';
		$params = array($codice, $corso, $anno, $nome, $descr, $resp);

		return db_iu('create_insegnamento', $query, $params);
	}

	function change_field($codice, $corso, $field, $value) {
		$query = 'update uni.insegnamento set ' . $field .' = $1 where codice = $2 and corso = $3';
		$params = array($value, $codice, $corso);
		
		return db_iu('change_' . $field, $query, $params);
	}

	function get_insegnamento($codice, $corso) {
		$query = 'select * from uni.insegnamento where codice = $1 and corso = $2';
		return db_single_select('get_insegnamento', $query, array($codice, $corso));
	}
?>