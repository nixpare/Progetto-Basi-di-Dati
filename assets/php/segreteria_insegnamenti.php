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

	function get_propedeuticità($codice, $corso) {
		$query = 'select insegnamento.* from
					uni.propedeuticità
					join
					uni.insegnamento on propedeuticità.codice_propedeutico = insegnamento.codice and
						propedeuticità.corso_propedeutico = insegnamento.corso
				where propedeuticità.codice_insegnamento = $1 and propedeuticità.corso_insegnamento = $2
				order by insegnamento.anno, insegnamento.codice';
		return db_multi_select('get_propedeuticità', $query, array($codice, $corso))['result'];
	}

	function remove_propedeuticità($codice, $corso, $codice_prop) {
		$query = 'delete from uni.propedeuticità where codice_insegnamento = $1 and
					corso_insegnamento = $2 and codice_propedeutico = $3 and corso_propedeutico = $2';
		return db_iu('remove_propedeuticità', $query, array($codice, $corso, $codice_prop));
	}

	function add_propedeuticità($codice, $corso, $codice_prop) {
		$query = 'insert into uni.propedeuticità values ($1, $2, $3, $2)';
		return db_iu('add_propedeuticità', $query, array($codice, $corso, $codice_prop));
	}

	function get_suitable_props($codice, $corso, $anno) {
		$query = 'select * from uni.insegnamento ins
				where ins.codice <> $1 and ins.corso = $2 and ins.anno <= $3 and not exists (
					select * from uni.propedeuticità
					where propedeuticità.codice_insegnamento = $1 and propedeuticità.corso_insegnamento = $2 and
						propedeuticità.codice_propedeutico = ins.codice and propedeuticità.corso_propedeutico = ins.corso
				)
				order by ins.anno, ins.codice';
		return db_multi_select('get_suitable_props', $query, array($codice, $corso, $anno))['result'];
	}
?>
